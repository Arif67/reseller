@extends('backEnd.layouts.master')
@section('title','Leave Manage')

@section('content')
<div class="container-fluid">
    <div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('hr.leave.create') }}" class="btn btn-primary rounded-pill">Create</a></div><h4 class="page-title">Leave Manage</h4></div></div></div>
    <div class="card"><div class="card-body">
        <form class="mb-3"><div class="row g-3"><div class="col-md-4"><select class="form-control" name="employee_id"><option value="">All Employees</option>@foreach($employees as $employee)<option value="{{ $employee->id }}" @selected((string) request('employee_id') === (string) $employee->id)>{{ $employee->name }}</option>@endforeach</select></div><div class="col-md-2"><select class="form-control" name="status"><option value="">All Status</option>@foreach(['pending','approved','rejected'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div><div class="col-md-2"><input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}"></div><div class="col-md-2"><input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}"></div><div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div></div></form>
        <div class="table-responsive"><table class="table table-striped align-middle"><thead><tr><th>Employee</th><th>Type</th><th>Period</th><th>Days</th><th>Status</th><th>Action</th></tr></thead><tbody>@foreach($data as $value)<tr><td>{{ $value->employee?->name }}</td><td>{{ $value->leave_type ?: '-' }}</td><td>{{ $value->start_date?->format('d M Y') }} - {{ $value->end_date?->format('d M Y') }}</td><td>{{ $value->total_days }}</td><td><span class="badge bg-info text-dark">{{ ucfirst($value->status) }}</span></td><td class="d-flex gap-1"><a href="{{ route('hr.leave.edit', $value->id) }}" class="btn btn-primary btn-sm">Edit</a><form method="post" action="{{ route('hr.leave.destroy') }}">@csrf<input type="hidden" name="hidden_id" value="{{ $value->id }}"><button type="submit" class="btn btn-danger btn-sm">Delete</button></form></td></tr>@endforeach</tbody></table></div>
        {{ $data->links('pagination::bootstrap-4') }}
    </div></div>
</div>
@endsection
