@extends('backEnd.layouts.master')
@section('title','Expense Manage')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary rounded-pill">Create</a>
                </div>
                <h4 class="page-title">Expense Manage</h4>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="mb-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="keyword" value="{{ request('keyword') }}" placeholder="Expense name">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="expense_cat_id">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('expense_cat_id') === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>

            <form method="post" action="{{ route('expenses.destroy') }}" id="bulk-expense-delete" class="mb-3">
                @csrf
                <div id="bulk-expense-inputs"></div>
                <button type="submit" class="btn btn-danger rounded-pill">Bulk Delete</button>
            </form>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="expense-check-all"></th>
                            <th>SL</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Financial</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $value)
                            <tr>
                                <td><input type="checkbox" class="expense-checkbox" value="{{ $value->id }}"></td>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $value->name }}
                                    @if($value->note)
                                        <div class="small text-muted">{{ $value->note }}</div>
                                    @endif
                                </td>
                                <td>{{ $value->category?->name ?: 'Uncategorized' }}</td>
                                <td>
                                    <div>{{ $value->financialAccount?->name ?: '-' }}</div>
                                    <div class="small text-muted">{{ $value->accountHead?->name ?: '' }}</div>
                                </td>
                                <td>{{ number_format((float) $value->amount, 2, '.', '') }}</td>
                                <td>
                                    @if($value->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ ($value->transaction_date ?: $value->created_at)?->format('d M Y') }}</td>
                                <td class="d-flex gap-1">
                                    @if($value->status)
                                        <form method="post" action="{{ route('expenses.inactive') }}">
                                            @csrf
                                            <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                            <button type="submit" class="btn btn-secondary btn-sm">Inactive</button>
                                        </form>
                                    @else
                                        <form method="post" action="{{ route('expenses.active') }}">
                                            @csrf
                                            <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                            <button type="submit" class="btn btn-success btn-sm">Active</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('expenses.edit', $value->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <form method="post" action="{{ route('expenses.destroy') }}">
                                        @csrf
                                        <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $data->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('expense-check-all');
    const checkboxes = Array.from(document.querySelectorAll('.expense-checkbox'));
    const form = document.getElementById('bulk-expense-delete');
    const inputs = document.getElementById('bulk-expense-inputs');

    checkAll?.addEventListener('change', function () {
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = checkAll.checked;
        });
    });

    form?.addEventListener('submit', function (event) {
        const selected = checkboxes.filter(function (checkbox) {
            return checkbox.checked;
        }).map(function (checkbox) {
            return checkbox.value;
        });

        if (!selected.length) {
            event.preventDefault();
            return;
        }

        inputs.innerHTML = '';
        selected.forEach(function (id) {
            inputs.insertAdjacentHTML('beforeend', '<input type="hidden" name="expense_ids[]" value="' + id + '">');
        });
    });
});
</script>
@endsection
