@extends('backEnd.layouts.master')
@section('title','Expense Category Manage')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('expensecategories.create') }}" class="btn btn-primary rounded-pill">Create</a>
                </div>
                <h4 class="page-title">Expense Category Manage</h4>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="post" action="{{ route('expensecategories.destroy') }}" id="bulk-expense-category-delete" class="mb-3">
                @csrf
                <div id="bulk-expense-category-inputs"></div>
                <button type="submit" class="btn btn-danger rounded-pill">Bulk Delete</button>
            </form>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="expense-category-check-all"></th>
                            <th>SL</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $value)
                            <tr>
                                <td><input type="checkbox" class="expense-category-checkbox" value="{{ $value->id }}"></td>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $value->name }}</td>
                                <td>{{ $value->slug }}</td>
                                <td>
                                    @if($value->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="d-flex gap-1">
                                    @if($value->status)
                                        <form method="post" action="{{ route('expensecategories.inactive') }}">
                                            @csrf
                                            <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                            <button type="submit" class="btn btn-secondary btn-sm">Inactive</button>
                                        </form>
                                    @else
                                        <form method="post" action="{{ route('expensecategories.active') }}">
                                            @csrf
                                            <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                            <button type="submit" class="btn btn-success btn-sm">Active</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('expensecategories.edit', $value->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <form method="post" action="{{ route('expensecategories.destroy') }}">
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
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('expense-category-check-all');
    const checkboxes = Array.from(document.querySelectorAll('.expense-category-checkbox'));
    const form = document.getElementById('bulk-expense-category-delete');
    const inputs = document.getElementById('bulk-expense-category-inputs');

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
            inputs.insertAdjacentHTML('beforeend', '<input type="hidden" name="category_ids[]" value="' + id + '">');
        });
    });
});
</script>
@endsection
