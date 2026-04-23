@extends('backEnd.layouts.master')
@section('title','Stock Adjustment Create')
@section('content')
@php
    $productOptions = $products->map(function ($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'unit_cost' => number_format((float) $product->purchase_price, 2, '.', ''),
            'variants' => $product->allVariables->map(function ($variable) {
                return [
                    'id' => $variable->id,
                    'label' => trim(collect([$variable->size, $variable->color])->filter()->implode(' / ')) ?: ('Variant #' . $variable->id),
                    'purchase_price' => number_format((float) $variable->purchase_price, 2, '.', ''),
                ];
            })->values()->all(),
        ];
    })->values();
@endphp
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('inventory.adjustments.index') }}" class="btn btn-primary rounded-pill">Manage</a></div><h4 class="page-title">Stock Adjustment Create</h4></div></div></div><div class="card"><div class="card-body"><form action="{{ route('inventory.adjustments.store') }}" method="POST" class="row">@csrf<div class="col-md-4"><div class="form-group mb-3"><label class="form-label">Product *</label><select class="form-control" name="product_id" id="adjustment-product" required><option value="">Select..</option>@foreach($productOptions as $product)<option value="{{ $product['id'] }}" data-unit-cost="{{ $product['unit_cost'] }}" data-variants='{{ json_encode($product['variants'], JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}'>{{ $product['name'] }}</option>@endforeach</select></div></div><div class="col-md-4"><div class="form-group mb-3"><label class="form-label">Variant</label><select class="form-control" name="product_variable_id" id="adjustment-variant"><option value="">No variant</option></select></div></div><div class="col-md-2"><div class="form-group mb-3"><label class="form-label">Type *</label><select class="form-control" name="adjustment_type" required><option value="adjustment_in">Adjustment In</option><option value="adjustment_out">Adjustment Out</option></select></div></div><div class="col-md-2"><div class="form-group mb-3"><label class="form-label">Date</label><input type="date" name="adjustment_date" class="form-control" value="{{ now()->format('Y-m-d') }}"></div></div><div class="col-md-2"><div class="form-group mb-3"><label class="form-label">Qty *</label><input type="number" min="1" name="qty" class="form-control" value="1" required></div></div><div class="col-md-2"><div class="form-group mb-3"><label class="form-label">Unit Cost</label><input type="number" step="0.01" min="0" name="unit_cost" id="adjustment-cost" class="form-control" value="0.00"></div></div><div class="col-md-4"><div class="form-group mb-3"><label class="form-label">Reason</label><input type="text" name="reason" class="form-control"></div></div><div class="col-md-12"><div class="form-group mb-3"><label class="form-label">Note</label><textarea name="note" class="form-control" rows="4"></textarea></div></div><div class="col-12"><button class="btn btn-success">Save Adjustment</button></div></form></div></div></div>
@endsection
@push('script')
<script>
    (function () {
        const productSelect = document.querySelector('#adjustment-product');
        const variantSelect = document.querySelector('#adjustment-variant');
        const costInput = document.querySelector('#adjustment-cost');

        if (!productSelect) {
            return;
        }

        function fillVariants() {
            const selected = productSelect.options[productSelect.selectedIndex];
            const variants = selected ? JSON.parse(selected.dataset.variants || '[]') : [];
            costInput.value = selected ? (selected.dataset.unitCost || '0.00') : '0.00';
            variantSelect.innerHTML = '<option value="">No variant</option>';
            variants.forEach((variant) => {
                const option = document.createElement('option');
                option.value = variant.id;
                option.textContent = variant.label;
                option.dataset.purchasePrice = variant.purchase_price;
                variantSelect.appendChild(option);
            });
        }

        productSelect.addEventListener('change', fillVariants);
        variantSelect.addEventListener('change', function () {
            const selected = variantSelect.options[variantSelect.selectedIndex];
            if (selected && selected.dataset.purchasePrice) {
                costInput.value = selected.dataset.purchasePrice;
            }
        });
    })();
</script>
@endpush
