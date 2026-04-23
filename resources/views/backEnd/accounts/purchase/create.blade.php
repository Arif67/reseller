@extends('backEnd.layouts.master')
@section('title','Purchase Create')
@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('accounts.purchases.index') }}" class="btn btn-primary rounded-pill">Manage</a></div><h4 class="page-title">Purchase Create</h4></div></div></div><div class="card"><div class="card-body"><form action="{{ route('accounts.purchases.store') }}" method="POST" class="row" id="purchase-form">@csrf<div class="col-md-3"><div class="form-group mb-3"><label class="form-label">Supplier</label><select name="supplier_id" id="supplier-select" class="form-control"><option value="">Select supplier..</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" data-name="{{ $supplier->name }}">{{ $supplier->name }}</option>@endforeach</select></div></div><div class="col-md-3"><div class="form-group mb-3"><label class="form-label">Supplier Name *</label><input type="text" name="supplier_name" id="supplier-name-input" class="form-control" list="supplier-suggestions" value="{{ old('supplier_name') }}" required><datalist id="supplier-suggestions">@foreach($supplierSuggestions as $supplierSuggestion)<option value="{{ $supplierSuggestion }}"></option>@endforeach</datalist></div></div><div class="col-md-2"><div class="form-group mb-3"><label class="form-label">Date</label><input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', now()->format('Y-m-d')) }}"></div></div><div class="col-md-2"><div class="form-group mb-3"><label class="form-label">Financial Account</label><select class="form-control" name="financial_account_id"><option value="">Select..</option>@foreach($financialAccounts as $financialAccount)<option value="{{ $financialAccount->id }}">{{ $financialAccount->name }}</option>@endforeach</select></div></div><div class="col-md-2"><div class="form-group mb-3"><label class="form-label">Expense Head</label><select class="form-control" name="account_head_id"><option value="">Select..</option>@foreach($accountHeads as $accountHead)<option value="{{ $accountHead->id }}">{{ $accountHead->name }}</option>@endforeach</select></div></div><div class="col-md-3"><div class="form-group mb-3"><label class="form-label">Reference No</label><input type="text" name="reference_no" class="form-control" value="{{ old('reference_no') }}"></div></div><div class="col-md-3"><div class="form-group mb-3"><label class="form-label">Discount</label><input type="number" step="0.01" min="0" name="discount_amount" class="form-control totals-trigger" value="{{ old('discount_amount', 0) }}"></div></div><div class="col-md-3"><div class="form-group mb-3"><label class="form-label">Transport Cost</label><input type="number" step="0.01" min="0" name="transport_cost" class="form-control totals-trigger" value="{{ old('transport_cost', 0) }}"></div></div><div class="col-md-3"><div class="form-group mb-3"><label class="form-label">Other Cost</label><input type="number" step="0.01" min="0" name="other_cost" class="form-control totals-trigger" value="{{ old('other_cost', 0) }}"></div></div><div class="col-12"><div class="table-responsive"><table class="table table-bordered align-middle" id="purchase-items-table"><thead><tr><th style="min-width:240px">Product</th><th style="min-width:220px">Variant</th><th width="120">Qty</th><th width="160">Unit Cost</th><th width="160">Line Total</th><th width="80"></th></tr></thead><tbody><tr><td><select class="form-control product-select" name="product_id[]" required><option value="">Select product..</option>@foreach($products as $product)<option value="{{ $product->id }}" data-unit-cost="{{ number_format((float) $product->purchase_price, 2, '.', '') }}" data-variants='@json($product->allVariables->map(fn($variable) => ['id' => $variable->id, 'label' => trim(collect([$variable->size, $variable->color])->filter()->implode(" / ")) ?: ("Variant #".$variable->id), 'purchase_price' => number_format((float) $variable->purchase_price, 2, '.', '')])->values())'>{{ $product->name }}</option>@endforeach</select></td><td><select class="form-control variant-select" name="product_variable_id[]"><option value="">No variant</option></select></td><td><input type="number" min="1" name="qty[]" class="form-control qty-input line-trigger" value="1" required></td><td><input type="number" step="0.01" min="0" name="unit_cost[]" class="form-control cost-input line-trigger" value="0.00" required></td><td><input type="text" class="form-control line-total" value="0.00" readonly></td><td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td></tr></tbody></table></div><button type="button" class="btn btn-outline-primary btn-sm" id="add-item-row">Add Item</button></div><div class="col-md-3 mt-3"><div class="form-group mb-3"><label class="form-label">Paid Amount</label><input type="number" step="0.01" min="0" name="paid_amount" class="form-control totals-trigger" value="{{ old('paid_amount', 0) }}"></div></div><div class="col-md-9 mt-3"><div class="form-group mb-3"><label class="form-label">Note</label><textarea name="note" class="form-control" rows="3">{{ old('note') }}</textarea></div></div><div class="col-md-4"><div class="alert alert-light border"><div class="d-flex justify-content-between"><span>Subtotal</span><strong id="summary-subtotal">0.00</strong></div><div class="d-flex justify-content-between"><span>Grand Total</span><strong id="summary-grand-total">0.00</strong></div><div class="d-flex justify-content-between"><span>Due</span><strong id="summary-due">0.00</strong></div></div></div><div class="col-12"><button class="btn btn-success">Save Purchase</button></div></form></div></div></div>
<template id="purchase-item-template"><tr><td><select class="form-control product-select" name="product_id[]" required><option value="">Select product..</option>@foreach($products as $product)<option value="{{ $product->id }}" data-unit-cost="{{ number_format((float) $product->purchase_price, 2, '.', '') }}" data-variants='@json($product->allVariables->map(fn($variable) => ['id' => $variable->id, 'label' => trim(collect([$variable->size, $variable->color])->filter()->implode(" / ")) ?: ("Variant #".$variable->id), 'purchase_price' => number_format((float) $variable->purchase_price, 2, '.', '')])->values())'>{{ $product->name }}</option>@endforeach</select></td><td><select class="form-control variant-select" name="product_variable_id[]"><option value="">No variant</option></select></td><td><input type="number" min="1" name="qty[]" class="form-control qty-input line-trigger" value="1" required></td><td><input type="number" step="0.01" min="0" name="unit_cost[]" class="form-control cost-input line-trigger" value="0.00" required></td><td><input type="text" class="form-control line-total" value="0.00" readonly></td><td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td></tr></template>
@endsection
@push('script')
<script>
    (function () {
        const supplierSelect = document.querySelector('#supplier-select');
        const supplierNameInput = document.querySelector('#supplier-name-input');
        const tableBody = document.querySelector('#purchase-items-table tbody');
        const template = document.querySelector('#purchase-item-template');

        function parseFloatSafe(value) {
            const parsed = parseFloat(value);
            return Number.isFinite(parsed) ? parsed : 0;
        }

        function updateVariantOptions(row) {
            const productSelect = row.querySelector('.product-select');
            const variantSelect = row.querySelector('.variant-select');
            const selected = productSelect.options[productSelect.selectedIndex];
            const variants = selected ? JSON.parse(selected.dataset.variants || '[]') : [];
            const baseCost = selected ? selected.dataset.unitCost || '0' : '0';

            variantSelect.innerHTML = '<option value="">No variant</option>';
            variants.forEach((variant) => {
                const option = document.createElement('option');
                option.value = variant.id;
                option.textContent = variant.label;
                option.dataset.purchasePrice = variant.purchase_price;
                variantSelect.appendChild(option);
            });

            row.querySelector('.cost-input').value = baseCost;
            updateLine(row);
        }

        function updateLine(row) {
            const qty = parseFloatSafe(row.querySelector('.qty-input').value);
            const unitCost = parseFloatSafe(row.querySelector('.cost-input').value);
            row.querySelector('.line-total').value = (qty * unitCost).toFixed(2);
            updateSummary();
        }

        function updateSummary() {
            const subtotal = Array.from(document.querySelectorAll('.line-total')).reduce((carry, input) => carry + parseFloatSafe(input.value), 0);
            const discount = parseFloatSafe(document.querySelector('[name="discount_amount"]').value);
            const transport = parseFloatSafe(document.querySelector('[name="transport_cost"]').value);
            const other = parseFloatSafe(document.querySelector('[name="other_cost"]').value);
            const paid = parseFloatSafe(document.querySelector('[name="paid_amount"]').value);
            const grandTotal = Math.max(0, subtotal - discount + transport + other);
            const due = Math.max(0, grandTotal - paid);

            document.querySelector('#summary-subtotal').textContent = subtotal.toFixed(2);
            document.querySelector('#summary-grand-total').textContent = grandTotal.toFixed(2);
            document.querySelector('#summary-due').textContent = due.toFixed(2);
        }

        document.querySelector('#add-item-row').addEventListener('click', function () {
            tableBody.appendChild(template.content.firstElementChild.cloneNode(true));
        });

        tableBody.addEventListener('change', function (event) {
            const row = event.target.closest('tr');
            if (!row) {
                return;
            }

            if (event.target.classList.contains('product-select')) {
                updateVariantOptions(row);
            }

            if (event.target.classList.contains('variant-select')) {
                const selectedVariant = event.target.options[event.target.selectedIndex];
                if (selectedVariant && selectedVariant.dataset.purchasePrice) {
                    row.querySelector('.cost-input').value = selectedVariant.dataset.purchasePrice;
                }
                updateLine(row);
            }
        });

        tableBody.addEventListener('input', function (event) {
            if (event.target.classList.contains('line-trigger')) {
                const row = event.target.closest('tr');
                updateLine(row);
            }
        });

        tableBody.addEventListener('click', function (event) {
            if (!event.target.classList.contains('remove-row')) {
                return;
            }

            if (tableBody.querySelectorAll('tr').length === 1) {
                return;
            }

            event.target.closest('tr').remove();
            updateSummary();
        });

        document.querySelectorAll('.totals-trigger').forEach((input) => {
            input.addEventListener('input', updateSummary);
        });

        if (supplierSelect && supplierNameInput) {
            supplierSelect.addEventListener('change', function () {
                const selected = supplierSelect.options[supplierSelect.selectedIndex];
                supplierNameInput.value = selected ? (selected.dataset.name || '') : '';
            });
        }

        tableBody.querySelectorAll('tr').forEach(updateVariantOptions);
        updateSummary();
    })();
</script>
@endpush
