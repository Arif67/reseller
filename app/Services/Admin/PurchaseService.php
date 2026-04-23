<?php

namespace App\Services\Admin;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\ProductVariable;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\SupplierLedger;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseService
{
    public function __construct(
        private AccountingTransactionService $accountingTransactionService,
        private InventoryService $inventoryService
    ) {
    }

    public function create(array $payload): Purchase
    {
        return DB::transaction(function () use ($payload) {
            $totals = $this->calculateTotals($payload);

            $purchase = new Purchase();
            $purchase->purchase_no = $payload['purchase_no'] ?? $this->generatePurchaseNo();

            return $this->persistPurchase($purchase, $payload, $totals);
        });
    }

    public function update(Purchase $purchase, array $payload): Purchase
    {
        return DB::transaction(function () use ($purchase, $payload) {
            $totals = $this->calculateTotals($payload);

            $this->inventoryService->rollbackPurchase($purchase);
            $purchase->items()->delete();

            return $this->persistPurchase($purchase, $payload, $totals);
        });
    }

    public function createReturn(Purchase $purchase, array $payload): PurchaseReturn
    {
        return DB::transaction(function () use ($purchase, $payload) {
            $purchase->loadMissing('items');
            $quantities = Arr::get($payload, 'qty', []);
            $itemIds = Arr::get($payload, 'purchase_item_id', []);
            $totalAmount = 0;
            $return = PurchaseReturn::create([
                'purchase_id' => $purchase->id,
                'return_no' => 'PRN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'return_date' => $payload['return_date'] ?? now()->toDateString(),
                'reason' => $payload['reason'] ?? null,
                'note' => $payload['note'] ?? null,
                'status' => 1,
            ]);

            foreach ($itemIds as $index => $purchaseItemId) {
                $purchaseItem = $purchase->items->firstWhere('id', (int) $purchaseItemId);
                $qty = (int) ($quantities[$index] ?? 0);

                if (! $purchaseItem || $qty <= 0) {
                    continue;
                }

                $lineTotal = $qty * (float) $purchaseItem->unit_cost;
                $totalAmount += $lineTotal;

                $return->items()->create([
                    'purchase_item_id' => $purchaseItem->id,
                    'product_id' => $purchaseItem->product_id,
                    'product_variable_id' => $purchaseItem->product_variable_id,
                    'product_name' => $purchaseItem->product_name,
                    'variant_name' => $purchaseItem->variant_name,
                    'qty' => $qty,
                    'unit_cost' => $purchaseItem->unit_cost,
                    'line_total' => $lineTotal,
                ]);
            }

            $return->total_amount = $totalAmount;
            $return->save();

            $this->inventoryService->recordPurchaseReturn($return);

            $purchase->paid_amount = min((float) $purchase->paid_amount, max(0, (float) $purchase->grand_total - $return->total_amount));
            $purchase->due_amount = max(0, ((float) $purchase->grand_total - $return->total_amount) - (float) $purchase->paid_amount);
            $purchase->save();
            $this->syncSupplierLedger($purchase);

            SupplierLedger::create([
                'supplier_name' => $purchase->supplier_name,
                'transaction_date' => $return->return_date,
                'reference_no' => $return->return_no,
                'reference_type' => 'purchase_return',
                'reference_id' => $return->id,
                'amount' => -1 * $return->total_amount,
                'paid_amount' => 0,
                'due_amount' => -1 * $return->total_amount,
                'note' => $return->reason ?: $return->note,
                'status' => 1,
            ]);

            return $return->load('items');
        });
    }

    public function settleSupplierPayment(array $payload): SupplierPayment
    {
        return DB::transaction(function () use ($payload) {
            $payment = SupplierPayment::create([
                'purchase_id' => $payload['purchase_id'] ?? null,
                'supplier_id' => $payload['supplier_id'] ?? null,
                'supplier_name' => $payload['supplier_name'],
                'payment_date' => $payload['payment_date'] ?? now()->toDateString(),
                'financial_account_id' => $payload['financial_account_id'] ?? null,
                'account_head_id' => $payload['account_head_id'] ?? null,
                'reference_no' => $payload['reference_no'] ?? null,
                'amount' => (float) $payload['amount'],
                'note' => $payload['note'] ?? null,
                'status' => 1,
            ]);

            SupplierLedger::create([
                'supplier_name' => $payment->supplier_name,
                'transaction_date' => $payment->payment_date,
                'reference_no' => $payment->reference_no ?: ('PAY-' . $payment->id),
                'reference_type' => 'supplier_payment',
                'reference_id' => $payment->id,
                'amount' => 0,
                'paid_amount' => $payment->amount,
                'due_amount' => -1 * $payment->amount,
                'note' => $payment->note,
                'status' => 1,
            ]);

            if ($payment->financial_account_id && $payment->amount > 0) {
                $this->accountingTransactionService->postGeneralTransaction([
                    'financial_account_id' => $payment->financial_account_id,
                    'account_head_id' => $payment->account_head_id,
                    'transaction_date' => $payment->payment_date ?: now()->toDateString(),
                    'direction' => 'out',
                    'amount' => $payment->amount,
                    'description' => 'Supplier payment - ' . $payment->supplier_name,
                    'reference_type' => 'supplier_payment',
                    'reference_id' => $payment->id,
                    'note' => $payment->note,
                    'status' => 1,
                ]);
            }

            if ($payment->purchase_id) {
                $purchase = Purchase::find($payment->purchase_id);
                if ($purchase) {
                    $purchase->paid_amount = min((float) $purchase->grand_total, (float) $purchase->paid_amount + (float) $payment->amount);
                    $purchase->due_amount = max(0, (float) $purchase->grand_total - (float) $purchase->paid_amount);
                    $purchase->save();
                    $this->syncSupplierLedger($purchase);
                }
            }

            return $payment;
        });
    }

    protected function calculateTotals(array $payload): array
    {
        $productIds = Arr::get($payload, 'product_id', []);
        $variableIds = Arr::get($payload, 'product_variable_id', []);
        $quantities = Arr::get($payload, 'qty', []);
        $unitCosts = Arr::get($payload, 'unit_cost', []);

        $items = [];
        $subtotal = 0;

        foreach ($productIds as $index => $productId) {
            if (! $productId) {
                continue;
            }

            $qty = max(1, (int) ($quantities[$index] ?? 0));
            $unitCost = (float) ($unitCosts[$index] ?? 0);
            $productVariableId = $variableIds[$index] ?? null;

            $product = Product::findOrFail($productId);
            $variable = $productVariableId ? ProductVariable::where('product_id', $productId)->findOrFail($productVariableId) : null;

            $lineTotal = $qty * $unitCost;
            $subtotal += $lineTotal;

            $items[] = [
                'product_id' => $product->id,
                'product_variable_id' => $variable?->id,
                'product_name' => $product->name,
                'variant_name' => $this->variantName($variable),
                'qty' => $qty,
                'unit_cost' => $unitCost,
                'line_total' => $lineTotal,
            ];
        }

        $discount = (float) ($payload['discount_amount'] ?? 0);
        $transport = (float) ($payload['transport_cost'] ?? 0);
        $other = (float) ($payload['other_cost'] ?? 0);
        $paid = (float) ($payload['paid_amount'] ?? 0);
        $grandTotal = max(0, $subtotal - $discount + $transport + $other);

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'grand_total' => $grandTotal,
            'paid_amount' => min($paid, $grandTotal),
            'due_amount' => max(0, $grandTotal - $paid),
        ];
    }

    protected function syncSupplierLedger(Purchase $purchase): void
    {
        SupplierLedger::updateOrCreate(
            [
                'reference_type' => 'purchase',
                'reference_id' => $purchase->id,
            ],
            [
                'supplier_name' => $purchase->supplier_name,
                'transaction_date' => $purchase->purchase_date,
                'reference_no' => $purchase->purchase_no,
                'amount' => $purchase->grand_total,
                'paid_amount' => $purchase->paid_amount,
                'due_amount' => $purchase->due_amount,
                'note' => $purchase->note,
                'status' => $purchase->status,
            ]
        );
    }

    protected function syncAccountTransaction(Purchase $purchase): void
    {
        \App\Models\AccountTransaction::query()
            ->where('reference_type', 'purchase')
            ->where('reference_id', $purchase->id)
            ->delete();

        if (! $purchase->status || ! $purchase->financial_account_id || $purchase->paid_amount <= 0) {
            $this->accountingTransactionService->recalculateBalance($purchase->financial_account_id);
            return;
        }

        $this->accountingTransactionService->postGeneralTransaction([
            'financial_account_id' => $purchase->financial_account_id,
            'account_head_id' => $purchase->account_head_id,
            'transaction_date' => $purchase->purchase_date ?: now()->toDateString(),
            'direction' => 'out',
            'amount' => $purchase->paid_amount,
            'description' => 'Purchase payment - ' . $purchase->purchase_no,
            'reference_type' => 'purchase',
            'reference_id' => $purchase->id,
            'note' => $purchase->note,
            'status' => 1,
        ]);
    }

    protected function variantName(?ProductVariable $variable): ?string
    {
        if (! $variable) {
            return null;
        }

        return trim(collect([$variable->size, $variable->color])->filter()->implode(' / ')) ?: ('Variant #' . $variable->id);
    }

    protected function generatePurchaseNo(): string
    {
        return 'PUR-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
    }

    protected function persistPurchase(Purchase $purchase, array $payload, array $totals): Purchase
    {
        $purchase->fill([
            'supplier_id' => $payload['supplier_id'] ?? null,
            'supplier_name' => $payload['supplier_name'],
            'purchase_date' => $payload['purchase_date'] ?? now()->toDateString(),
            'financial_account_id' => $payload['financial_account_id'] ?? null,
            'account_head_id' => $payload['account_head_id'] ?? null,
            'reference_no' => $payload['reference_no'] ?? null,
            'subtotal' => $totals['subtotal'],
            'discount_amount' => (float) ($payload['discount_amount'] ?? 0),
            'transport_cost' => (float) ($payload['transport_cost'] ?? 0),
            'other_cost' => (float) ($payload['other_cost'] ?? 0),
            'grand_total' => $totals['grand_total'],
            'paid_amount' => $totals['paid_amount'],
            'due_amount' => $totals['due_amount'],
            'note' => $payload['note'] ?? null,
            'status' => Arr::get($payload, 'status', 1) ? 1 : 0,
        ]);
        $purchase->save();

        foreach ($totals['items'] as $item) {
            $purchase->items()->create($item);
        }

        $this->syncSupplierLedger($purchase);
        $this->syncAccountTransaction($purchase);
        $this->inventoryService->recordPurchase($purchase);

        return $purchase->load('items');
    }
}
