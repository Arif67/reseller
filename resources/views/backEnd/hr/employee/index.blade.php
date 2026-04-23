@extends('backEnd.layouts.master')
@section('title','Employee Manage')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('hr.employees.create') }}" class="btn btn-primary rounded-pill">Create</a>
                </div>
                <h4 class="page-title">Employee Manage</h4>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Active Employees</div>
                    <h4 class="mb-0">{{ $summary['active_employees'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Joined This Month</div>
                    <h4 class="mb-0">{{ $summary['joined_this_month'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Present Today</div>
                    <h4 class="mb-0">{{ $summary['present_today'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Payroll Paid This Month</div>
                    <h4 class="mb-0">{{ number_format((float) $summary['payroll_paid_this_month'], 2, '.', '') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="mb-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="keyword" value="{{ request('keyword') }}" placeholder="Employee / phone / email / ID">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="department_id">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" @selected((string) request('department_id') === (string) $department->id)>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>

            <form method="post" action="{{ route('hr.employees.destroy') }}" id="bulk-hr-employee-delete" class="mb-3">
                @csrf
                <div id="bulk-hr-employee-inputs"></div>
                <button type="submit" class="btn btn-danger rounded-pill">Bulk Delete</button>
            </form>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="hr-employee-check-all"></th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Shift</th>
                            <th>Contact</th>
                            <th>Joining</th>
                            <th>Salary</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $value)
                            <tr>
                                <td><input type="checkbox" class="hr-employee-checkbox" value="{{ $value->id }}"></td>
                                <td>
                                    <strong>{{ $value->name }}</strong>
                                    <div class="small text-muted">{{ $value->employee_id ?: 'No ID' }}</div>
                                </td>
                                <td>{{ $value->department?->name ?: 'Unassigned' }}</td>
                                <td>{{ $value->designationMaster?->name ?: ($value->designation ?: '-') }}</td>
                                <td>{{ $value->shift?->name ?: '-' }}</td>
                                <td>
                                    <div>{{ $value->phone ?: '-' }}</div>
                                    <div class="small text-muted">{{ $value->email ?: '' }}</div>
                                    <div class="small text-muted">{{ $value->user?->email ?: '' }}</div>
                                </td>
                                <td>{{ $value->joining_date?->format('d M Y') ?: '-' }}</td>
                                <td>{{ number_format((float) $value->salary, 2, '.', '') }}</td>
                                <td>{!! $value->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>' !!}</td>
                                <td class="d-flex gap-1">
                                    <a href="{{ route('hr.employees.show', $value->id) }}" class="btn btn-info btn-sm">View</a>
                                    @if($value->status)
                                        <form method="post" action="{{ route('hr.employees.inactive') }}">@csrf<input type="hidden" name="hidden_id" value="{{ $value->id }}"><button type="submit" class="btn btn-secondary btn-sm">Inactive</button></form>
                                    @else
                                        <form method="post" action="{{ route('hr.employees.active') }}">@csrf<input type="hidden" name="hidden_id" value="{{ $value->id }}"><button type="submit" class="btn btn-success btn-sm">Active</button></form>
                                    @endif
                                    <a href="{{ route('hr.employees.edit', $value->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <form method="post" action="{{ route('hr.employees.destroy') }}">@csrf<input type="hidden" name="hidden_id" value="{{ $value->id }}"><button type="submit" class="btn btn-danger btn-sm">Delete</button></form>
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
    const checkAll = document.getElementById('hr-employee-check-all');
    const checkboxes = Array.from(document.querySelectorAll('.hr-employee-checkbox'));
    const form = document.getElementById('bulk-hr-employee-delete');
    const inputs = document.getElementById('bulk-hr-employee-inputs');

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
            inputs.insertAdjacentHTML('beforeend', '<input type="hidden" name="employee_ids[]" value="' + id + '">');
        });
    });
});
</script>
@endsection
