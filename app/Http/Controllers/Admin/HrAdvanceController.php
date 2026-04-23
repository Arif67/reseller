<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrAdvance;
use App\Models\HrEmployee;
use Illuminate\Http\Request;
use Toastr;

class HrAdvanceController extends Controller
{
    public function index(Request $request)
    {
        $data = HrAdvance::with('employee.department')->latest('issue_date');
        if ($request->employee_id) {
            $data->where('employee_id', $request->employee_id);
        }
        if ($request->status) {
            $data->where('status', $request->status);
        }
        $data = $data->paginate(20)->withQueryString();
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();
        return view('backEnd.hr.advance.index', compact('data', 'employees'));
    }

    public function create()
    {
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();
        return view('backEnd.hr.advance.create', compact('employees'));
    }
    public function edit($id)
    {
        $edit_data = HrAdvance::findOrFail($id);
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();
        return view('backEnd.hr.advance.edit', compact('edit_data', 'employees'));
    }

    public function store(Request $request)
    {
        $this->validate($request, ['employee_id' => 'required|exists:hr_employees,id']);
        HrAdvance::create($request->only('employee_id', 'advance_type', 'amount', 'installment_amount', 'issue_date', 'deduction_start_date', 'notes', 'status'));
        Toastr::success('Success', 'Advance/loan created successfully');
        return redirect()->route('hr.advances.index');
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:hr_advances,id', 'employee_id' => 'required|exists:hr_employees,id']);
        $row = HrAdvance::findOrFail($request->id);
        $row->fill($request->only('employee_id', 'advance_type', 'amount', 'installment_amount', 'issue_date', 'deduction_start_date', 'notes', 'status'));
        $row->save();
        Toastr::success('Success', 'Advance/loan updated successfully');
        return redirect()->route('hr.advances.index');
    }

    public function destroy(Request $request)
    {
        $ids = array_filter((array) $request->input('advance_ids', []));
        !empty($ids) ? HrAdvance::whereIn('id', $ids)->delete() : HrAdvance::where('id', $request->hidden_id)->delete();
        Toastr::success('Success', 'Advance/loan deleted successfully');
        return redirect()->back();
    }
}
