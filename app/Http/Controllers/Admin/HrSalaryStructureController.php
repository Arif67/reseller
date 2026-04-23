<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrEmployee;
use App\Models\HrSalaryStructure;
use Illuminate\Http\Request;
use Toastr;

class HrSalaryStructureController extends Controller
{
    public function index(Request $request)
    {
        $data = HrSalaryStructure::with('employee.department')->latest('effective_date');
        if ($request->employee_id) { $data->where('employee_id', $request->employee_id); }
        $data = $data->paginate(20)->withQueryString();
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();
        return view('backEnd.hr.salary_structure.index', compact('data', 'employees'));
    }

    public function create() { $employees = HrEmployee::where('status', 1)->orderBy('name')->get(); return view('backEnd.hr.salary_structure.create', compact('employees')); }
    public function edit($id) { $edit_data = HrSalaryStructure::findOrFail($id); $employees = HrEmployee::where('status', 1)->orderBy('name')->get(); return view('backEnd.hr.salary_structure.edit', compact('edit_data', 'employees')); }

    public function store(Request $request)
    {
        $this->validate($request, ['employee_id' => 'required|exists:hr_employees,id']);
        $data = $request->only('employee_id', 'basic_salary', 'house_rent', 'medical_allowance', 'transport_allowance', 'other_allowance', 'effective_date', 'notes');
        $data['gross_salary'] = (float) ($request->basic_salary ?: 0) + (float) ($request->house_rent ?: 0) + (float) ($request->medical_allowance ?: 0) + (float) ($request->transport_allowance ?: 0) + (float) ($request->other_allowance ?: 0);
        $data['status'] = $request->status ? 1 : 0;
        HrSalaryStructure::create($data);
        Toastr::success('Success', 'Salary structure created successfully');
        return redirect()->route('hr.salary_structures.index');
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:hr_salary_structures,id', 'employee_id' => 'required|exists:hr_employees,id']);
        $row = HrSalaryStructure::findOrFail($request->id);
        $data = $request->only('employee_id', 'basic_salary', 'house_rent', 'medical_allowance', 'transport_allowance', 'other_allowance', 'effective_date', 'notes');
        $data['gross_salary'] = (float) ($request->basic_salary ?: 0) + (float) ($request->house_rent ?: 0) + (float) ($request->medical_allowance ?: 0) + (float) ($request->transport_allowance ?: 0) + (float) ($request->other_allowance ?: 0);
        $row->fill($data);
        $row->status = $request->status ? 1 : 0;
        $row->save();
        Toastr::success('Success', 'Salary structure updated successfully');
        return redirect()->route('hr.salary_structures.index');
    }

    public function inactive(Request $request) { HrSalaryStructure::where('id', $request->hidden_id)->update(['status' => 0]); Toastr::success('Success', 'Salary structure inactive successfully'); return redirect()->back(); }
    public function active(Request $request) { HrSalaryStructure::where('id', $request->hidden_id)->update(['status' => 1]); Toastr::success('Success', 'Salary structure active successfully'); return redirect()->back(); }
    public function destroy(Request $request) { $ids = array_filter((array) $request->input('salary_structure_ids', [])); !empty($ids) ? HrSalaryStructure::whereIn('id', $ids)->delete() : HrSalaryStructure::where('id', $request->hidden_id)->delete(); Toastr::success('Success', 'Salary structure deleted successfully'); return redirect()->back(); }
}
