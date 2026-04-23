@extends('backEnd.layouts.master')
@section('title','Employee Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right d-flex gap-2">
                    <a href="{{ route('hr.employees.edit', $employee->id) }}" class="btn btn-primary rounded-pill">Edit Employee</a>
                    <a href="{{ route('hr.employees.index') }}" class="btn btn-light rounded-pill">Back</a>
                </div>
                <h4 class="page-title">Employee Details</h4>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="mb-1">{{ $employee->name }}</h4>
                            <div class="text-muted">{{ $employee->employee_id ?: 'No Employee ID' }}</div>
                        </div>
                        {!! $employee->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>' !!}
                    </div>
                    <div class="mb-2"><strong>Department:</strong> {{ $employee->department?->name ?: 'Unassigned' }}</div>
                    <div class="mb-2"><strong>Designation:</strong> {{ $employee->designationMaster?->name ?: ($employee->designation ?: '-') }}</div>
                    <div class="mb-2"><strong>Shift:</strong> {{ $employee->shift?->name ?: '-' }}</div>
                    <div class="mb-2"><strong>Linked User:</strong> {{ $employee->user?->email ?: '-' }}</div>
                    <div class="mb-2"><strong>Phone:</strong> {{ $employee->phone ?: '-' }}</div>
                    <div class="mb-2"><strong>Email:</strong> {{ $employee->email ?: '-' }}</div>
                    <div class="mb-2"><strong>Joining Date:</strong> {{ $employee->joining_date?->format('d M Y') ?: '-' }}</div>
                    <div class="mb-2"><strong>Employment Type:</strong> {{ $employee->employment_type ?: '-' }}</div>
                    <div class="mb-2"><strong>Salary:</strong> {{ number_format((float) $employee->salary, 2, '.', '') }}</div>
                    <div class="mb-0"><strong>Address:</strong> {{ $employee->address ?: '-' }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-3">
                <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Present This Month</div><h4 class="mb-0">{{ $attendanceSummary['present'] }}</h4></div></div></div>
                <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Absent This Month</div><h4 class="mb-0">{{ $attendanceSummary['absent'] }}</h4></div></div></div>
                <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Late This Month</div><h4 class="mb-0">{{ $attendanceSummary['late'] }}</h4></div></div></div>
                <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">On Leave This Month</div><h4 class="mb-0">{{ $attendanceSummary['leave'] }}</h4></div></div></div>
                <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Allocated Leave</div><h4 class="mb-0">{{ $leaveSummary['allocated'] }}</h4></div></div></div>
                <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Approved Leave</div><h4 class="mb-0">{{ $leaveSummary['approved'] }}</h4></div></div></div>
                <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Remaining Leave</div><h4 class="mb-0">{{ $leaveSummary['remaining'] }}</h4></div></div></div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3" id="employee-detail-tabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#employee-overview-tab" type="button">Overview</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#employee-attendance-tab" type="button">Attendance</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#employee-leave-tab" type="button">Leave</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#employee-payroll-tab" type="button">Payroll</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#employee-document-tab" type="button">Documents</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#employee-performance-tab" type="button">Performance</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#employee-separation-tab" type="button">Separation</button></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="employee-overview-tab">
                    <h5 class="mb-3">Current Month Payroll</h5>
                    @if($monthlyPayroll)
                        <div class="row g-3">
                            <div class="col-md-3"><strong>Basic Salary</strong><div>{{ number_format((float) $monthlyPayroll->basic_salary, 2, '.', '') }}</div></div>
                            <div class="col-md-2"><strong>Bonus</strong><div>{{ number_format((float) $monthlyPayroll->bonus, 2, '.', '') }}</div></div>
                            <div class="col-md-2"><strong>Deduction</strong><div>{{ number_format((float) $monthlyPayroll->deduction, 2, '.', '') }}</div></div>
                            <div class="col-md-2"><strong>Net Salary</strong><div>{{ number_format((float) $monthlyPayroll->net_salary, 2, '.', '') }}</div></div>
                            <div class="col-md-3"><strong>Status</strong><div><span class="badge bg-{{ $monthlyPayroll->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($monthlyPayroll->status) }}</span></div></div>
                        </div>
                    @else
                        <div class="text-muted">No payroll entry found for this month.</div>
                    @endif
                    @if($employee->notes)
                        <hr>
                        <div><strong>Notes:</strong> {{ $employee->notes }}</div>
                    @endif
                </div>

                <div class="tab-pane fade" id="employee-attendance-tab">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Date</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($recentAttendances as $attendance)
                                    <tr>
                                        <td>{{ $attendance->attendance_date?->format('d M Y') }}</td>
                                        <td><span class="badge bg-light text-dark">{{ ucfirst($attendance->status) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center text-muted">No attendance history.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="employee-leave-tab">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Range</th><th>Type</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($recentLeaves as $leave)
                                    <tr>
                                        <td>{{ $leave->start_date?->format('d M') }} - {{ $leave->end_date?->format('d M') }}</td>
                                        <td>{{ $leave->leave_type ?: '-' }}</td>
                                        <td><span class="badge bg-light text-dark">{{ ucfirst($leave->status) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">No leave history.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="employee-payroll-tab">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Month</th><th>Basic</th><th>Net</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($recentPayrolls as $payroll)
                                    <tr>
                                        <td>{{ str_pad((string) $payroll->month, 2, '0', STR_PAD_LEFT) }}/{{ $payroll->year }}</td>
                                        <td>{{ number_format((float) $payroll->basic_salary, 2, '.', '') }}</td>
                                        <td>{{ number_format((float) $payroll->net_salary, 2, '.', '') }}</td>
                                        <td><span class="badge bg-{{ $payroll->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($payroll->status) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">No payroll history.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="employee-document-tab">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Title</th><th>Type</th><th>File</th><th>Expiry</th></tr></thead>
                            <tbody>
                                @forelse($recentDocuments as $document)
                                    <tr>
                                        <td>{{ $document->title }}</td>
                                        <td>{{ $document->document_type ?: '-' }}</td>
                                        <td>@if($document->file_path)<a href="{{ str_starts_with($document->file_path, 'http') ? $document->file_path : asset($document->file_path) }}" target="_blank">Open</a>@else - @endif</td>
                                        <td>{{ $document->expiry_date?->format('d M Y') ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">No document history.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="employee-performance-tab">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Date</th><th>Title</th><th>Rating</th><th>Description</th></tr></thead>
                            <tbody>
                                @forelse($recentPerformanceNotes as $note)
                                    <tr>
                                        <td>{{ $note->note_date?->format('d M Y') ?: '-' }}</td>
                                        <td>{{ $note->title }}</td>
                                        <td>{{ $note->rating ?: '-' }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($note->description, 80) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">No performance notes.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="employee-separation-tab">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Type</th><th>Last Working Date</th><th>Status</th><th>Reason</th></tr></thead>
                            <tbody>
                                @forelse($recentSeparations as $separation)
                                    <tr>
                                        <td>{{ ucfirst($separation->separation_type) }}</td>
                                        <td>{{ $separation->last_working_date?->format('d M Y') ?: '-' }}</td>
                                        <td>{{ ucfirst($separation->status) }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($separation->reason, 80) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">No separation history.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
