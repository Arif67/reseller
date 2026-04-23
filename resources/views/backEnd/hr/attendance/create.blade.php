@extends('backEnd.layouts.master')
@section('title','Attendance Create')

@section('content')
<div class="container-fluid">
    <div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('hr.attendance.index') }}" class="btn btn-primary rounded-pill">Manage</a></div><h4 class="page-title">Attendance Create</h4></div></div></div>
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#single-attendance-tab" type="button">Single Entry</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#bulk-attendance-tab" type="button">Bulk Entry</button></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="single-attendance-tab">
                    <form action="{{ route('hr.attendance.store') }}" method="POST" class="row">
                        @csrf
                        @include('backEnd.hr.attendance.partials.form', ['attendance' => null])
                        <div class="col-12"><button type="submit" class="btn btn-success">Submit</button></div>
                    </form>
                </div>

                <div class="tab-pane fade" id="bulk-attendance-tab">
                    <form action="{{ route('hr.attendance.bulk_store') }}" method="POST">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Attendance Date *</label>
                                <input type="date" name="attendance_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <div class="alert alert-info w-100 mb-0 py-2">Ekbar e multiple employee-r attendance mark koro. Empty status thakle oi row save hobe na.</div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Status</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $employee)
                                        <tr>
                                            <td>
                                                <strong>{{ $employee->name }}</strong>
                                                <div class="small text-muted">{{ $employee->department?->name ?: '-' }}</div>
                                            </td>
                                            <td>
                                                <select class="form-control" name="statuses[{{ $employee->id }}]">
                                                    <option value="">Skip</option>
                                                    @foreach(['present','absent','late','leave'] as $status)
                                                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" class="form-control" name="check_in[{{ $employee->id }}]" placeholder="09:00 AM"></td>
                                            <td><input type="text" class="form-control" name="check_out[{{ $employee->id }}]" placeholder="06:00 PM"></td>
                                            <td><input type="text" class="form-control" name="notes[{{ $employee->id }}]" placeholder="Optional note"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <button type="submit" class="btn btn-success">Save Bulk Attendance</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
