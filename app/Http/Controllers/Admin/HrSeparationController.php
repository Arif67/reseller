<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrEmployee;
use App\Models\HrSeparation;
use Illuminate\Http\Request;
use Toastr;

class HrSeparationController extends Controller
{
    public function index(Request $request)
    {
        $data = HrSeparation::with('employee.department')->latest('last_working_date');
        if ($request->employee_id) { $data->where('employee_id', $request->employee_id); }
        if ($request->status) { $data->where('status', $request->status); }
        $data = $data->paginate(20)->withQueryString();
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();
        return view('backEnd.hr.separation.index', compact('data', 'employees'));
    }

    public function create() { $employees = HrEmployee::where('status', 1)->orderBy('name')->get(); return view('backEnd.hr.separation.create', compact('employees')); }
    public function edit($id) { $edit_data = HrSeparation::findOrFail($id); $employees = HrEmployee::where('status', 1)->orderBy('name')->get(); return view('backEnd.hr.separation.edit', compact('edit_data', 'employees')); }

    public function store(Request $request)
    {
        $this->validate($request, ['employee_id' => 'required|exists:hr_employees,id']);
        HrSeparation::create($request->only('employee_id', 'separation_type', 'last_working_date', 'status', 'reason', 'notes'));
        Toastr::success('Success', 'Separation entry created successfully');
        return redirect()->route('hr.separations.index');
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:hr_separations,id', 'employee_id' => 'required|exists:hr_employees,id']);
        $row = HrSeparation::findOrFail($request->id);
        $row->fill($request->only('employee_id', 'separation_type', 'last_working_date', 'status', 'reason', 'notes'));
        $row->save();
        Toastr::success('Success', 'Separation entry updated successfully');
        return redirect()->route('hr.separations.index');
    }

    public function destroy(Request $request) { $ids = array_filter((array) $request->input('separation_ids', [])); !empty($ids) ? HrSeparation::whereIn('id', $ids)->delete() : HrSeparation::where('id', $request->hidden_id)->delete(); Toastr::success('Success', 'Separation deleted successfully'); return redirect()->back(); }
}
