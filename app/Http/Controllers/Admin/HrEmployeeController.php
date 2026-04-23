<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrAttendance;
use App\Models\HrDesignation;
use App\Models\HrDepartment;
use App\Models\HrDocument;
use App\Models\HrEmployee;
use App\Models\HrLeave;
use App\Models\HrPayroll;
use App\Models\HrPerformanceNote;
use App\Models\HrSeparation;
use App\Models\HrShift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Toastr;

class HrEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $data = HrEmployee::with(['department', 'shift', 'designationMaster', 'user'])->latest();

        if ($request->keyword) {
            $data->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('employee_id', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('phone', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->keyword . '%');
            });
        }

        if ($request->department_id) {
            $data->where('department_id', $request->department_id);
        }

        $data = $data->paginate(20)->withQueryString();
        $departments = HrDepartment::where('status', 1)->orderBy('name')->get();
        $today = now()->toDateString();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $summary = [
            'active_employees' => HrEmployee::where('status', 1)->count(),
            'joined_this_month' => HrEmployee::whereMonth('joining_date', $currentMonth)->whereYear('joining_date', $currentYear)->count(),
            'present_today' => HrAttendance::whereDate('attendance_date', $today)->where('status', 'present')->count(),
            'payroll_paid_this_month' => HrPayroll::where('status', 'paid')->where('month', $currentMonth)->where('year', $currentYear)->sum('net_salary'),
        ];

        return view('backEnd.hr.employee.index', compact('data', 'departments', 'summary'));
    }

    public function show($id)
    {
        $employee = HrEmployee::with(['department', 'shift', 'designationMaster', 'user'])->findOrFail($id);
        $currentMonthStart = now()->startOfMonth()->toDateString();
        $currentMonthEnd = now()->endOfMonth()->toDateString();
        $currentYear = now()->year;

        $attendanceRecords = $employee->attendances()
            ->whereBetween('attendance_date', [$currentMonthStart, $currentMonthEnd])
            ->latest('attendance_date')
            ->get();

        $attendanceSummary = [
            'present' => $attendanceRecords->where('status', 'present')->count(),
            'absent' => $attendanceRecords->where('status', 'absent')->count(),
            'late' => $attendanceRecords->where('status', 'late')->count(),
            'leave' => $attendanceRecords->where('status', 'leave')->count(),
            'total' => $attendanceRecords->count(),
        ];

        $approvedLeaveDays = (int) $employee->leaves()
            ->where('status', 'approved')
            ->whereYear('start_date', $currentYear)
            ->sum('total_days');

        $leaveSummary = [
            'allocated' => (int) ($employee->annual_leave_days ?? 0),
            'approved' => $approvedLeaveDays,
            'remaining' => (int) ($employee->annual_leave_days ?? 0) - $approvedLeaveDays,
        ];

        $monthlyPayroll = $employee->payrolls()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->latest('payment_date')
            ->first();

        $recentAttendances = $employee->attendances()
            ->latest('attendance_date')
            ->limit(10)
            ->get();

        $recentLeaves = $employee->leaves()
            ->latest('start_date')
            ->limit(10)
            ->get();

        $recentPayrolls = $employee->payrolls()
            ->latest('year')
            ->latest('month')
            ->limit(6)
            ->get();

        $recentDocuments = $employee->documents()
            ->latest()
            ->limit(10)
            ->get();

        $recentPerformanceNotes = $employee->performanceNotes()
            ->latest('note_date')
            ->limit(10)
            ->get();

        $recentSeparations = $employee->separations()
            ->latest('last_working_date')
            ->limit(5)
            ->get();

        return view(
            'backEnd.hr.employee.show',
            compact(
                'employee',
                'attendanceSummary',
                'leaveSummary',
                'monthlyPayroll',
                'recentAttendances',
                'recentLeaves',
                'recentPayrolls',
                'recentDocuments',
                'recentPerformanceNotes',
                'recentSeparations'
            )
        );
    }

    public function create()
    {
        $departments = HrDepartment::where('status', 1)->orderBy('name')->get();
        $shifts = HrShift::where('status', 1)->orderBy('name')->get();
        $designations = HrDesignation::where('status', 1)->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('backEnd.hr.employee.create', compact('departments', 'shifts', 'designations', 'users'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:hr_departments,id',
            'user_id' => 'nullable|exists:users,id',
            'shift_id' => 'nullable|exists:hr_shifts,id',
            'designation_id' => 'nullable|exists:hr_designations,id',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'salary' => 'nullable|numeric|min:0',
        ]);

        HrEmployee::create([
            'department_id' => $request->department_id,
            'user_id' => $request->user_id,
            'shift_id' => $request->shift_id,
            'designation_id' => $request->designation_id,
            'employee_id' => $request->employee_id ?: $this->generateEmployeeId($request->name),
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'designation' => $request->designation ?: optional(HrDesignation::find($request->designation_id))->name,
            'joining_date' => $request->joining_date,
            'salary' => $request->salary ?: 0,
            'annual_leave_days' => $request->annual_leave_days ?: 0,
            'employment_type' => $request->employment_type,
            'address' => $request->address,
            'notes' => $request->notes,
            'status' => $request->status ? 1 : 0,
        ]);

        Toastr::success('Success', 'Employee created successfully');

        return redirect()->route('hr.employees.index');
    }

    public function edit($id)
    {
        $edit_data = HrEmployee::findOrFail($id);
        $departments = HrDepartment::where('status', 1)->orderBy('name')->get();
        $shifts = HrShift::where('status', 1)->orderBy('name')->get();
        $designations = HrDesignation::where('status', 1)->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('backEnd.hr.employee.edit', compact('edit_data', 'departments', 'shifts', 'designations', 'users'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:hr_employees,id',
            'name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:hr_departments,id',
            'user_id' => 'nullable|exists:users,id',
            'shift_id' => 'nullable|exists:hr_shifts,id',
            'designation_id' => 'nullable|exists:hr_designations,id',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'salary' => 'nullable|numeric|min:0',
        ]);

        $employee = HrEmployee::findOrFail($request->id);
        $employee->department_id = $request->department_id;
        $employee->user_id = $request->user_id;
        $employee->shift_id = $request->shift_id;
        $employee->designation_id = $request->designation_id;
        $employee->employee_id = $request->employee_id ?: ($employee->employee_id ?: $this->generateEmployeeId($request->name));
        $employee->name = $request->name;
        $employee->phone = $request->phone;
        $employee->email = $request->email;
        $employee->designation = $request->designation ?: optional(HrDesignation::find($request->designation_id))->name;
        $employee->joining_date = $request->joining_date;
        $employee->salary = $request->salary ?: 0;
        $employee->annual_leave_days = $request->annual_leave_days ?: 0;
        $employee->employment_type = $request->employment_type;
        $employee->address = $request->address;
        $employee->notes = $request->notes;
        $employee->status = $request->status ? 1 : 0;
        $employee->save();

        Toastr::success('Success', 'Employee updated successfully');

        return redirect()->route('hr.employees.index');
    }

    public function inactive(Request $request)
    {
        $employee = HrEmployee::findOrFail($request->hidden_id);
        $employee->status = 0;
        $employee->save();

        Toastr::success('Success', 'Employee inactive successfully');

        return redirect()->back();
    }

    public function active(Request $request)
    {
        $employee = HrEmployee::findOrFail($request->hidden_id);
        $employee->status = 1;
        $employee->save();

        Toastr::success('Success', 'Employee active successfully');

        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $ids = array_filter((array) $request->input('employee_ids', []));

        if (! empty($ids)) {
            HrEmployee::whereIn('id', $ids)->delete();
        } elseif ($request->hidden_id) {
            HrEmployee::where('id', $request->hidden_id)->delete();
        }

        Toastr::success('Success', 'Employee deleted successfully');

        return redirect()->back();
    }

    private function generateEmployeeId(string $name): string
    {
        $prefix = strtoupper(Str::substr(Str::slug($name, ''), 0, 3) ?: 'EMP');

        return $prefix . '-' . now()->format('ymdHis');
    }
}
