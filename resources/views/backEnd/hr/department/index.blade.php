@extends('backEnd.layouts.master')
@section('title','Department Manage')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('hr.departments.create') }}" class="btn btn-primary rounded-pill">Create</a>
                </div>
                <h4 class="page-title">Department Manage</h4>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="post" action="{{ route('hr.departments.destroy') }}" id="bulk-hr-department-delete" class="mb-3">
                @csrf
                <div id="bulk-hr-department-inputs"></div>
                <button type="submit" class="btn btn-danger rounded-pill">Bulk Delete</button>
            </form>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="hr-department-check-all"></th>
                            <th>SL</th>
                            <th>Name</th>
                            <th>Employees</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $value)
                            <tr>
                                <td><input type="checkbox" class="hr-department-checkbox" value="{{ $value->id }}"></td>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $value->name }}
                                    @if($value->description)
                                        <div class="small text-muted">{{ $value->description }}</div>
                                    @endif
                                </td>
                                <td>{{ $value->employees_count }}</td>
                                <td>{!! $value->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>' !!}</td>
                                <td class="d-flex gap-1">
                                    @if($value->status)
                                        <form method="post" action="{{ route('hr.departments.inactive') }}">@csrf<input type="hidden" name="hidden_id" value="{{ $value->id }}"><button type="submit" class="btn btn-secondary btn-sm">Inactive</button></form>
                                    @else
                                        <form method="post" action="{{ route('hr.departments.active') }}">@csrf<input type="hidden" name="hidden_id" value="{{ $value->id }}"><button type="submit" class="btn btn-success btn-sm">Active</button></form>
                                    @endif
                                    <a href="{{ route('hr.departments.edit', $value->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <form method="post" action="{{ route('hr.departments.destroy') }}">@csrf<input type="hidden" name="hidden_id" value="{{ $value->id }}"><button type="submit" class="btn btn-danger btn-sm">Delete</button></form>
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
    const checkAll = document.getElementById('hr-department-check-all');
    const checkboxes = Array.from(document.querySelectorAll('.hr-department-checkbox'));
    const form = document.getElementById('bulk-hr-department-delete');
    const inputs = document.getElementById('bulk-hr-department-inputs');

    checkAll?.addEventListener('change', function () {
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = checkAll.checked;
        });
    });

    form?.addEventListener('submit', function (event) {
        const selected = checkboxes.filter((checkbox) => checkbox.checked).map((checkbox) => checkbox.value);
        if (!selected.length) {
            event.preventDefault();
            return;
        }
        inputs.innerHTML = '';
        selected.forEach(function (id) {
            inputs.insertAdjacentHTML('beforeend', '<input type="hidden" name="department_ids[]" value="' + id + '">');
        });
    });
});
</script>
@endsection
