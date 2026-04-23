@extends('backEnd.layouts.master')
@section('title','Leave Balance')

@section('content')
<div class="container-fluid">
    <div class="row"><div class="col-12"><div class="page-title-box"><h4 class="page-title">Leave Balance</h4></div></div></div>
    <div class="card mb-4"><div class="card-body">
        <form>
            <div class="row g-3">
                <div class="col-md-3"><input type="number" class="form-control" name="year" min="2000" max="2100" value="{{ $year }}"></div>
                <div class="col-md-3"><button class="btn btn-primary w-100">Filter</button></div>
            </div>
        </form>
    </div></div>
    <div class="card"><div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead><tr><th>Employee</th><th>Department</th><th>Allocated Leave</th><th>Approved Leave</th><th>Remaining Leave</th><th>Type Wise</th></tr></thead>
                <tbody>
                    @foreach($employees as $employee)
                        <tr>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->department?->name ?: '-' }}</td>
                            <td>{{ $employee->annual_leave_days }}</td>
                            <td>{{ $employee->approved_leave_days }}</td>
                            <td class="{{ $employee->remaining_leave_days < 0 ? 'text-danger' : 'text-success' }}">{{ $employee->remaining_leave_days }}</td>
                            <td>
                                @if($employee->leave_type_breakdown->isNotEmpty())
                                    @foreach($employee->leave_type_breakdown as $item)
                                        <div class="small mb-1">
                                            <strong>{{ $item['name'] }}</strong>:
                                            {{ $item['used'] }}/{{ $item['allocated'] }}
                                            <span class="{{ $item['remaining'] < 0 ? 'text-danger' : 'text-success' }}">({{ $item['remaining'] }})</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-muted">No leave types setup.</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div></div>
</div>
@endsection
