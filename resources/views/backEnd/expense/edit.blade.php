@extends('backEnd.layouts.master')
@section('title','Expense Edit')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('expenses.index') }}" class="btn btn-primary rounded-pill">Manage</a>
                </div>
                <h4 class="page-title">Expense Edit</h4>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('expenses.update') }}" method="POST" class="row">
                @csrf
                <input type="hidden" name="id" value="{{ $edit_data->id }}">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label">Expense Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $edit_data->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-control" name="expense_cat_id">
                            <option value="">Select..</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('expense_cat_id', $edit_data->expense_cat_id) === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label class="form-label">Amount *</label>
                        <input type="number" step="0.01" min="0" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $edit_data->amount) }}" required>
                        @error('amount')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label class="form-label">Transaction Date</label>
                        <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date', optional($edit_data->transaction_date)->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label class="form-label">Financial Account</label>
                        <select class="form-control" name="financial_account_id">
                            <option value="">Select..</option>
                            @foreach($financialAccounts as $financialAccount)
                                <option value="{{ $financialAccount->id }}" @selected((string) old('financial_account_id', $edit_data->financial_account_id) === (string) $financialAccount->id)>{{ $financialAccount->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group mb-3">
                        <label class="form-label">Account Head</label>
                        <select class="form-control" name="account_head_id">
                            <option value="">Select..</option>
                            @foreach($accountHeads as $accountHead)
                                <option value="{{ $accountHead->id }}" @selected((string) old('account_head_id', $edit_data->account_head_id) === (string) $accountHead->id)>{{ $accountHead->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control" rows="4">{{ old('note', $edit_data->note) }}</textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label class="d-block form-label">Status</label>
                        <label class="switch">
                            <input type="checkbox" name="status" value="1" @if($edit_data->status) checked @endif>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
