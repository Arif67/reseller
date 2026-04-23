@extends('backEnd.layouts.master')
@section('title','Supplier Payment Create')
@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('accounts.supplier_payments.index') }}" class="btn btn-primary rounded-pill">Manage</a></div><h4 class="page-title">Supplier Payment Create</h4></div></div></div><div class="card"><div class="card-body"><form action="{{ route('accounts.supplier_payments.store') }}" method="POST" class="row">@csrf<div class="col-md-3"><div class="form-group mb-3"><label class="form-label">Supplier</label><select class="form-control" name="supplier_id" id="supplier-payment-select"><option value="">Select..</option>@foreach($supplierRows as $supplier)<option value="{{ $supplier->id }}" data-name="{{ $supplier->name }}">{{ $supplier->name }}</option>@endforeach</select></div></div><div class="col-md-3"><div class="form-group mb-3"><label class="form-label">Supplier Name *</label><input type="text" class="form-control" name="supplier_name" id="supplier-payment-name" list="supplier-list" required><datalist id="supplier-list">@foreach($suppliers as $supplier)<option value="{{ $supplier }}"></option>@endforeach</datalist></div></div><div class="col-md-4"><div class="form-group mb-3"><label class="form-label">Purchase</label><select class="form-control" name="purchase_id"><option value="">Select..</option>@foreach($purchases as $purchase)<option value="{{ $purchase->id }}">{{ $purchase->purchase_no }} - {{ $purchase->supplier_name }} (Due {{ number_format((float) $purchase->due_amount, 2, '.', '') }})</option>@endforeach</select></div></div><div class="col-md-2"><div class="form-group mb-3"><label class="form-label">Date</label><input type="date" name="payment_date" class="form-control" value="{{ now()->format('Y-m-d') }}"></div></div><div class="col-md-2"><div class="form-group mb-3"><label class="form-label">Amount *</label><input type="number" step="0.01" min="0.01" name="amount" class="form-control" required></div></div><div class="col-md-4"><div class="form-group mb-3"><label class="form-label">Financial Account</label><select class="form-control" name="financial_account_id"><option value="">Select..</option>@foreach($financialAccounts as $financialAccount)<option value="{{ $financialAccount->id }}">{{ $financialAccount->name }}</option>@endforeach</select></div></div><div class="col-md-3"><div class="form-group mb-3"><label class="form-label">Account Head</label><select class="form-control" name="account_head_id"><option value="">Select..</option>@foreach($accountHeads as $accountHead)<option value="{{ $accountHead->id }}">{{ $accountHead->name }}</option>@endforeach</select></div></div><div class="col-md-3"><div class="form-group mb-3"><label class="form-label">Reference</label><input type="text" name="reference_no" class="form-control"></div></div><div class="col-md-12"><div class="form-group mb-3"><label class="form-label">Note</label><textarea class="form-control" name="note" rows="3"></textarea></div></div><div class="col-12"><button class="btn btn-success">Save Payment</button></div></form></div></div></div>
@endsection
@push('script')
<script>
    (function () {
        const supplierSelect = document.querySelector('#supplier-payment-select');
        const supplierNameInput = document.querySelector('#supplier-payment-name');
        if (supplierSelect && supplierNameInput) {
            supplierSelect.addEventListener('change', function () {
                const selected = supplierSelect.options[supplierSelect.selectedIndex];
                supplierNameInput.value = selected ? (selected.dataset.name || '') : '';
            });
        }
    })();
</script>
@endpush
