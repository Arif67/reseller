<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrAttendance;
use App\Models\HrEmployee;
use Illuminate\Http\Request;
use Toastr;

class HrAttendanceController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = HrAttendance::query()->with('employee.department');

        $startDate = $request->start_date ?: now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?: now()->format('Y-m-d');

        $query->whereBetween('attendance_date', [$startDate, $endDate]);

        if ($request->department_id) {
            $query->whereHas('employee', function ($employeeQuery) use ($request) {
                $employeeQuery->where('department_id', $request->department_id);
            });
        }

        $records = $query->get();
        $employees = HrEmployee::with('department')->where('status', 1)->orderBy('name')->get();

        $summary = [
            'present' => $records->where('status', 'present')->count(),
            'absent' => $records->where('status', 'absent')->count(),
            'late' => $records->where('status', 'late')->count(),
            'leave' => $records->where('status', 'leave')->count(),
            'total' => $records->count(),
        ];
        $summary['present_rate'] = $summary['total'] > 0 ? round(($summary['present'] / $summary['total']) * 100, 1) : 0;

        $employeeSummary = $records
            ->groupBy('employee_id')
            ->map(function ($group) use ($employees) {
                $employee = $employees->firstWhere('id', $group->first()->employee_id);

                return [
                    'employee' => $employee,
                    'present' => $group->where('status', 'present')->count(),
                    'absent' => $group->where('status', 'absent')->count(),
                    'late' => $group->where('status', 'late')->count(),
                    'leave' => $group->where('status', 'leave')->count(),
                    'total' => $group->count(),
                ];
            })
            ->sortBy(fn ($row) => strtolower($row['employee']?->name ?? ''))
            ->values();

        $departments = \App\Models\HrDepartment::where('status', 1)->orderBy('name')->get();

        return view('backEnd.hr.attendance.dashboard', compact('summary', 'employeeSummary', 'departments', 'startDate', 'endDate'));
    }

    public function index(Request $request)
    {
        $data = HrAttendance::with('employee.department')->latest('attendance_date');

        if ($request->employee_id) {
            $data->where('employee_id', $request->employee_id);
        }

        if ($request->status) {
            $data->where('status', $request->status);
        }

        if ($request->start_date && $request->end_date) {
            $data->whereBetween('attendance_date', [$request->start_date, $request->end_date]);
        }

        $data = $data->paginate(20)->withQueryString();
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();

        return view('backEnd.hr.attendance.index', compact('data', 'employees'));
    }

    public function create()
    {
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();

        return view('backEnd.hr.attendance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'employee_id' => 'required|exists:hr_employees,id',
            'attendance_date' => 'required|date',
            'status' => 'required|string',
        ]);

        HrAttendance::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'attendance_date' => $request->attendance_date,
            ],
            [
                'status' => $request->status,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'notes' => $request->notes,
            ]
        );

        Toastr::success('Success', 'Attendance saved successfully');

        return redirect()->route('hr.attendance.index');
    }

    public function bulkStore(Request $request)
    {
        $this->validate($request, [
            'attendance_date' => 'required|date',
            'statuses' => 'required|array',
        ]);

        $saved = 0;

        foreach ((array) $request->input('statuses', []) as $employeeId => $status) {
            if (! $status || ! HrEmployee::where('id', $employeeId)->exists()) {
                continue;
            }

            HrAttendance::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'attendance_date' => $request->attendance_date,
                ],
                [
                    'status' => $status,
                    'check_in' => data_get($request->input('check_in', []), $employeeId),
                    'check_out' => data_get($request->input('check_out', []), $employeeId),
                    'notes' => data_get($request->input('notes', []), $employeeId),
                ]
            );

            $saved++;
        }

        Toastr::success('Success', $saved . ' attendance entries saved successfully');

        return redirect()->route('hr.attendance.index');
    }

    public function edit($id)
    {
        $edit_data = HrAttendance::findOrFail($id);
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();

        return view('backEnd.hr.attendance.edit', compact('edit_data', 'employees'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:hr_attendances,id',
            'employee_id' => 'required|exists:hr_employees,id',
            'attendance_date' => 'required|date',
            'status' => 'required|string',
        ]);

        $attendance = HrAttendance::findOrFail($request->id);
        $attendance->employee_id = $request->employee_id;
        $attendance->attendance_date = $request->attendance_date;
        $attendance->status = $request->status;
        $attendance->check_in = $request->check_in;
        $attendance->check_out = $request->check_out;
        $attendance->notes = $request->notes;
        $attendance->save();

        Toastr::success('Success', 'Attendance updated successfully');

        return redirect()->route('hr.attendance.index');
    }

    public function destroy(Request $request)
    {
        $ids = array_filter((array) $request->input('attendance_ids', []));

        if (! empty($ids)) {
            HrAttendance::whereIn('id', $ids)->delete();
        } elseif ($request->hidden_id) {
            HrAttendance::where('id', $request->hidden_id)->delete();
        }

        Toastr::success('Success', 'Attendance deleted successfully');

        return redirect()->back();
    }
}
