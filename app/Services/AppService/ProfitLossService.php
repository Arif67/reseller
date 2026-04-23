<?php

namespace App\Services\AppService;

use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProfitLossService
{
    public function deliveredOrderStatusIds(): array
    {
        if (! Schema::hasTable('order_statuses')) {
            return [6];
        }

        $ids = OrderStatus::query()
            ->where(function ($query) {
                $query->whereIn('slug', ['delivered', 'deliveryed', 'complete', 'completed'])
                    ->orWhereIn(DB::raw('LOWER(name)'), ['delivered', 'deliveryed', 'complete', 'completed']);
            })
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->filter()
            ->values()
            ->all();

        return ! empty($ids) ? $ids : ['6'];
    }

    private function availableOrderProfitColumns(): array
    {
        $columns = [
            'item_subtotal',
            'line_discount_total',
            'item_revenue',
            'product_cost',
            'shipping_revenue',
            'discount_total',
            'courier_cost',
            'packaging_cost',
            'payment_gateway_fee',
            'misc_cost',
            'additional_cost',
            'total_expense',
            'gross_profit',
            'net_profit',
            'profit_status',
        ];

        return array_values(array_filter($columns, fn ($column) => Schema::hasColumn('orders', $column)));
    }

    public function supportsOrderProfitColumns(): bool
    {
        return Schema::hasTable('orders') && ! empty($this->availableOrderProfitColumns());
    }

    public function calculateOrderMetrics(Order $order): array
    {
        $order->loadMissing('orderdetails', 'payment');

        $itemSubtotal = (float) $order->orderdetails->sum(function ($detail) {
            return (float) ($detail->sale_price ?? 0) * (int) ($detail->qty ?? 0);
        });

        $lineDiscountTotal = (float) $order->orderdetails->sum(function ($detail) {
            return (float) ($detail->product_discount ?? 0) * (int) ($detail->qty ?? 0);
        });

        $productCost = (float) $order->orderdetails->sum(function ($detail) {
            return (float) ($detail->purchase_price ?? 0) * (int) ($detail->qty ?? 0);
        });

        $shippingRevenue = (float) ($order->shipping_charge ?? 0);
        $discountTotal = (float) ($order->discount ?? 0);
        $itemRevenue = max((float) ($order->amount ?? 0) - $shippingRevenue, 0);
        $courierCost = (float) ($order->courier_cost ?? 0);
        $packagingCost = (float) ($order->packaging_cost ?? 0);
        $gatewayFee = (float) ($order->payment_gateway_fee ?? 0);
        $miscCost = (float) ($order->misc_cost ?? 0);
        $additionalCost = $courierCost + $packagingCost + $gatewayFee + $miscCost;
        $grossProfit = $itemRevenue - $productCost;
        $totalExpense = $productCost + $additionalCost;
        $netProfit = (float) ($order->amount ?? 0) - $totalExpense;

        return [
            'item_subtotal' => $itemSubtotal,
            'line_discount_total' => $lineDiscountTotal,
            'item_revenue' => $itemRevenue,
            'product_cost' => $productCost,
            'shipping_revenue' => $shippingRevenue,
            'discount_total' => $discountTotal,
            'courier_cost' => $courierCost,
            'packaging_cost' => $packagingCost,
            'payment_gateway_fee' => $gatewayFee,
            'misc_cost' => $miscCost,
            'additional_cost' => $additionalCost,
            'total_expense' => $totalExpense,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
            'profit_status' => $netProfit > 0 ? 'profit' : ($netProfit < 0 ? 'loss' : 'breakeven'),
        ];
    }

    public function recalculateOrder(Order|int $order): array
    {
        $order = $order instanceof Order
            ? $order->fresh(['orderdetails', 'payment']) ?? $order->loadMissing('orderdetails', 'payment')
            : Order::with('orderdetails', 'payment')->findOrFail($order);

        $metrics = $this->calculateOrderMetrics($order);

        if ($this->supportsOrderProfitColumns()) {
            $persistableMetrics = array_intersect_key($metrics, array_flip($this->availableOrderProfitColumns()));
            $order->forceFill($persistableMetrics)->save();
        }

        return $metrics;
    }

    public function summarizeCompletedOrders(?string $startDate = null, ?string $endDate = null): array
    {
        $ordersQuery = Order::query()->whereIn('order_status', $this->deliveredOrderStatusIds());

        if ($startDate && $endDate) {
            $ordersQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $orders = $this->supportsOrderProfitColumns()
            ? (clone $ordersQuery)->get([
                'id',
                'amount',
                'item_revenue',
                'product_cost',
                'shipping_charge',
                'courier_cost',
                'packaging_cost',
                'payment_gateway_fee',
                'misc_cost',
                'total_expense',
                'gross_profit',
                'net_profit',
            ])
            : (clone $ordersQuery)->with('orderdetails')->get();

        $salesRevenue = 0.0;
        $productCost = 0.0;
        $shippingRevenue = 0.0;
        $additionalCost = 0.0;
        $grossProfit = 0.0;
        $netProfitBeforeOperatingExpense = 0.0;

        foreach ($orders as $order) {
            if ($this->supportsOrderProfitColumns()) {
                $salesRevenue += (float) ($order->amount ?? 0);
                $productCost += (float) ($order->product_cost ?? 0);
                $shippingRevenue += (float) ($order->shipping_charge ?? 0);
                $additionalCost += (float) ($order->courier_cost ?? 0)
                    + (float) ($order->packaging_cost ?? 0)
                    + (float) ($order->payment_gateway_fee ?? 0)
                    + (float) ($order->misc_cost ?? 0);
                $grossProfit += (float) ($order->gross_profit ?? 0);
                $netProfitBeforeOperatingExpense += (float) ($order->net_profit ?? 0);
            } else {
                $metrics = $this->calculateOrderMetrics($order);
                $salesRevenue += (float) ($order->amount ?? 0);
                $productCost += $metrics['product_cost'];
                $shippingRevenue += $metrics['shipping_revenue'];
                $additionalCost += $metrics['additional_cost'];
                $grossProfit += $metrics['gross_profit'];
                $netProfitBeforeOperatingExpense += $metrics['net_profit'];
            }
        }

        $operatingExpense = $this->sumOperatingExpenses($startDate, $endDate);

        return [
            'completed_order_count' => $orders->count(),
            'sales_revenue' => $salesRevenue,
            'product_cost' => $productCost,
            'shipping_revenue' => $shippingRevenue,
            'additional_cost' => $additionalCost,
            'gross_profit' => $grossProfit,
            'operating_expense' => $operatingExpense,
            'net_profit_before_operating_expense' => $netProfitBeforeOperatingExpense,
            'net_profit_after_operating_expense' => $netProfitBeforeOperatingExpense - $operatingExpense,
        ];
    }

    public function sumOperatingExpenses(?string $startDate = null, ?string $endDate = null): float
    {
        if (! Schema::hasTable('expenses')) {
            return 0.0;
        }

        $query = DB::table('expenses');

        if (Schema::hasColumn('expenses', 'status')) {
            $query->where('status', 1);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return (float) $query->sum('amount');
    }
}
