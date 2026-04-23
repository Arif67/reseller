<?php

namespace App\Services\Admin;

use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariable;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function recordPurchase(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            $purchase->loadMissing('items');

            InventoryMovement::query()
                ->where('reference_type', 'purchase')
                ->where('reference_id', $purchase->id)
                ->delete();

            foreach ($purchase->items as $item) {
                $this->applyMovement([
                    'movement_date' => $purchase->purchase_date ?: now()->toDateString(),
                    'product_id' => $item->product_id,
                    'product_variable_id' => $item->product_variable_id,
                    'movement_type' => 'purchase_in',
                    'direction' => 'in',
                    'qty' => (int) $item->qty,
                    'unit_cost' => (float) $item->unit_cost,
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                    'reference_no' => $purchase->purchase_no,
                    'note' => $purchase->supplier_name,
                ]);
            }
        });
    }

    public function rollbackPurchase(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            $movements = InventoryMovement::query()
                ->where('reference_type', 'purchase')
                ->where('reference_id', $purchase->id)
                ->orderByDesc('id')
                ->get();

            foreach ($movements as $movement) {
                $this->reverseMovement($movement);
            }

            InventoryMovement::query()
                ->where('reference_type', 'purchase')
                ->where('reference_id', $purchase->id)
                ->delete();
        });
    }

    public function recordPurchaseReturn(PurchaseReturn $purchaseReturn): void
    {
        DB::transaction(function () use ($purchaseReturn) {
            $purchaseReturn->loadMissing('items');

            foreach ($purchaseReturn->items as $item) {
                $this->applyMovement([
                    'movement_date' => $purchaseReturn->return_date ?: now()->toDateString(),
                    'product_id' => $item->product_id,
                    'product_variable_id' => $item->product_variable_id,
                    'movement_type' => 'purchase_return_out',
                    'direction' => 'out',
                    'qty' => (int) $item->qty,
                    'unit_cost' => (float) $item->unit_cost,
                    'reference_type' => 'purchase_return',
                    'reference_id' => $purchaseReturn->id,
                    'reference_no' => $purchaseReturn->return_no,
                    'note' => $purchaseReturn->reason,
                ]);
            }
        });
    }

    public function syncOrderDelivery(Order $order, bool $delivered): void
    {
        DB::transaction(function () use ($order, $delivered) {
            $order->loadMissing('orderdetails');

            $existingMovements = InventoryMovement::query()
                ->where('reference_type', 'order_delivery')
                ->where('reference_id', $order->id)
                ->get();

            if (! $delivered) {
                foreach ($existingMovements as $movement) {
                    $this->reverseMovement($movement);
                }

                InventoryMovement::query()
                    ->where('reference_type', 'order_delivery')
                    ->where('reference_id', $order->id)
                    ->delete();

                return;
            }

            if ($existingMovements->isNotEmpty()) {
                return;
            }

            foreach ($order->orderdetails as $detail) {
                $this->applyMovement([
                    'movement_date' => optional($order->updated_at)->toDateString() ?: now()->toDateString(),
                    'product_id' => $detail->product_id,
                    'product_variable_id' => $detail->product_variable_id,
                    'movement_type' => 'sale_out',
                    'direction' => 'out',
                    'qty' => (int) $detail->qty,
                    'unit_cost' => (float) ($detail->purchase_price ?? 0),
                    'reference_type' => 'order_delivery',
                    'reference_id' => $order->id,
                    'reference_no' => (string) $order->invoice_id,
                    'note' => $detail->product_name,
                ]);
            }
        });
    }

    public function createAdjustment(array $payload): StockAdjustment
    {
        return DB::transaction(function () use ($payload) {
            $adjustment = StockAdjustment::create([
                'adjustment_date' => $payload['adjustment_date'] ?? now()->toDateString(),
                'product_id' => $payload['product_id'],
                'product_variable_id' => $payload['product_variable_id'] ?? null,
                'adjustment_type' => $payload['adjustment_type'],
                'qty' => (int) $payload['qty'],
                'unit_cost' => (float) ($payload['unit_cost'] ?? 0),
                'reason' => $payload['reason'] ?? null,
                'note' => $payload['note'] ?? null,
                'status' => ! empty($payload['status']) ? 1 : 0,
            ]);

            if ($adjustment->status) {
                $this->applyMovement([
                    'movement_date' => $adjustment->adjustment_date ?: now()->toDateString(),
                    'product_id' => $adjustment->product_id,
                    'product_variable_id' => $adjustment->product_variable_id,
                    'movement_type' => $adjustment->adjustment_type,
                    'direction' => in_array($adjustment->adjustment_type, ['adjustment_in'], true) ? 'in' : 'out',
                    'qty' => (int) $adjustment->qty,
                    'unit_cost' => (float) $adjustment->unit_cost,
                    'reference_type' => 'stock_adjustment',
                    'reference_id' => $adjustment->id,
                    'reference_no' => 'ADJ-' . $adjustment->id,
                    'note' => $adjustment->reason ?: $adjustment->note,
                ]);
            }

            return $adjustment;
        });
    }

    public function applyMovement(array $payload): InventoryMovement
    {
        $item = $this->resolveStockTarget($payload['product_id'], $payload['product_variable_id'] ?? null);
        $qty = max(1, (int) ($payload['qty'] ?? 0));
        $before = (int) ($item->stock ?? 0);
        $after = ($payload['direction'] ?? 'in') === 'out' ? max(0, $before - $qty) : $before + $qty;

        $item->stock = $after;
        $item->save();

        return InventoryMovement::create([
            'movement_date' => $payload['movement_date'] ?? now()->toDateString(),
            'product_id' => $payload['product_id'],
            'product_variable_id' => $payload['product_variable_id'] ?? null,
            'movement_type' => $payload['movement_type'],
            'direction' => $payload['direction'],
            'qty' => $qty,
            'stock_before' => $before,
            'stock_after' => $after,
            'unit_cost' => (float) ($payload['unit_cost'] ?? 0),
            'reference_type' => $payload['reference_type'] ?? null,
            'reference_id' => $payload['reference_id'] ?? null,
            'reference_no' => $payload['reference_no'] ?? null,
            'note' => $payload['note'] ?? null,
        ]);
    }

    protected function reverseMovement(InventoryMovement $movement): void
    {
        $item = $this->resolveStockTarget($movement->product_id, $movement->product_variable_id);
        $current = (int) ($item->stock ?? 0);
        $item->stock = $movement->direction === 'out'
            ? $current + (int) $movement->qty
            : max(0, $current - (int) $movement->qty);
        $item->save();
    }

    protected function resolveStockTarget(int $productId, ?int $productVariableId = null): Product|ProductVariable
    {
        if ($productVariableId) {
            return ProductVariable::where('product_id', $productId)->findOrFail($productVariableId);
        }

        return Product::findOrFail($productId);
    }
}
