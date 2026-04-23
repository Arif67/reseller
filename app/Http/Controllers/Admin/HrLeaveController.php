<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrEmployee;
use App\Models\HrLeave;
use App\Models\HrLeaveType;
use Illuminate\Http\Request;
use Toastr;

class HrLeaveController extends Controller
{
    public function balance(Request $request)
    {
        $year = (int) ($request->year ?: now()->year);
        $leaveTypes = HrLeaveType::where('status', 1)->orderBy('name')->get();
        $employees = HrEmployee::with('department')
            ->where('status', 1)
            ->orderBy('name')
            ->get()
            ->map(function ($employee) use ($year, $leaveTypes) {
                $approvedDays = HrLeave::query()
                    ->where('employee_id', $employee->id)
                    ->where('status', 'approved')
                    ->whereYear('start_date', $year)
                    ->sum('total_days');

                $employee->leave_type_breakdown = $leaveTypes->map(function ($leaveType) use ($employee, $year) {
                    $usedDays = (int) HrLeave::query()
                        ->where('employee_id', $employee->id)
                        ->where('status', 'approved')
                        ->where('leave_type', $leaveType->name)
                        ->whereYear('start_date', $year)
                        ->sum('total_days');

                    return [
                        'name' => $leaveType->name,
                        'allocated' => (int) ($leaveType->days_per_year ?? 0),
                        'used' => $usedDays,
                        'remaining' => (int) ($leaveType->days_per_year ?? 0) - $usedDays,
                    ];
                });

                $employee->approved_leave_days = (int) $approvedDays;
                $employee->remaining_leave_days = (int) ($employee->annual_leave_days ?? 0) - (int) $approvedDays;

                return $employee;
            });

        return view('backEnd.hr.leave.balance', compact('employees', 'year', 'leaveTypes'));
    }

    public function index(Request $request)
    {
        $data = HrLeave::with('employee.department')->latest('start_date');

        if ($request->employee_id) {
            $data->where('employee_id', $request->employee_id);
        }

        if ($request->status) {
            $data->where('status', $request->status);
        }

        if ($request->start_date && $request->end_date) {
            $data->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
            });
        }

        $data = $data->paginate(20)->withQueryString();
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();

        return view('backEnd.hr.leave.index', compact('data', 'employees'));
    }

    public function create()
    {
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();
        $leaveTypes = HrLeaveType::where('status', 1)->orderBy('name')->get();

        return view('backEnd.hr.leave.create', compact('employees', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'employee_id' => 'required|exists:hr_employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string',
        ]);

        HrLeave::create([
            'employee_id' => $request->employee_id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $request->total_days ?: 1,
            'status' => $request->status,
            'reason' => $request->reason,
            'admin_note' => $request->admin_note,
        ]);

        Toastr::success('Success', 'Leave entry created successfully');

        return redirect()->route('hr.leave.index');
    }

    public function edit($id)
    {
        $edit_data = HrLeave::findOrFail($id);
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();
        $leaveTypes = HrLeaveType::where('status', 1)->orderBy('name')->get();

        return view('backEnd.hr.leave.edit', compact('edit_data', 'employees', 'leaveTypes'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:hr_leaves,id',
            'employee_id' => 'required|exists:hr_employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string',
        ]);

        $leave = HrLeave::findOrFail($request->id);
        $leave->employee_id = $request->employee_id;
        $leave->leave_type = $request->leave_type;
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;
        $leave->total_days = $request->total_days ?: 1;
        $leave->status = $request->status;
        $leave->reason = $request->reason;
        $leave->admin_note = $request->admin_note;
        $leave->save();

        Toastr::success('Success', 'Leave entry updated successfully');

        return redirect()->route('hr.leave.index');
    }

    public function destroy(Request $request)
    {
        $ids = array_filter((array) $request->input('leave_ids', []));

        if (! empty($ids)) {
            HrLeave::whereIn('id', $ids)->delete();
        } elseif ($request->hidden_id) {
            HrLeave::where('id', $request->hidden_id)->delete();
        }

        Toastr::success('Success', 'Leave entry deleted successfully');

        return redirect()->back();
    }
}
