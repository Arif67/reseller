@extends('backEnd.layouts.master')
@section('title','Attendance Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row"><div class="col-12"><div class="page-title-box"><h4 class="page-title">Attendance Dashboard</h4></div></div></div>
    <div class="card mb-4"><div class="card-body">
        <form>
            <div class="row g-3">
                <div class="col-md-3">
                    <select class="form-control" name="department_id">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" @selected((string) request('department_id') === (string) $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3"><input type="date" class="form-control" name="start_date" value="{{ $startDate }}"></div>
                <div class="col-md-3"><input type="date" class="form-control" name="end_date" value="{{ $endDate }}"></div>
                <div class="col-md-3"><button class="btn btn-primary w-100">Filter</button></div>
            </div>
        </form>
    </div></div>
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card h-100 border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Present</div><h4 class="mb-0">{{ $summary['present'] }}</h4></div></div></div>
        <div class="col-md-3"><div class="card h-100 border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Absent</div><h4 class="mb-0">{{ $summary['absent'] }}</h4></div></div></div>
        <div class="col-md-3"><div class="card h-100 border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Late</div><h4 class="mb-0">{{ $summary['late'] }}</h4></div></div></div>
        <div class="col-md-3"><div class="card h-100 border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Leave</div><h4 class="mb-0">{{ $summary['leave'] }}</h4></div></div></div>
    </div>
    <div class="card"><div class="card-body">
        <h5 class="mb-3">Employee Summary</h5>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead><tr><th>Employee</th><th>Department</th><th>Present</th><th>Absent</th><th>Late</th><th>Leave</th><th>Total</th></tr></thead>
                <tbody>
                    @forelse($employeeSummary as $row)
                        <tr>
                            <td>{{ $row['employee']?->name ?: 'Unknown' }}</td>
                            <td>{{ $row['employee']?->department?->name ?: '-' }}</td>
                            <td>{{ $row['present'] }}</td>
                            <td>{{ $row['absent'] }}</td>
                            <td>{{ $row['late'] }}</td>
                            <td>{{ $row['leave'] }}</td>
                            <td>{{ $row['total'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No attendance data found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div></div>
</div>
@endsection
