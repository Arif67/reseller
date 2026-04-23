<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrDepartment;
use App\Models\HrDesignation;
use Illuminate\Http\Request;
use Toastr;

class HrDesignationController extends Controller
{
    public function index()
    {
        $data = HrDesignation::with('department')->latest()->get();
        return view('backEnd.hr.designation.index', compact('data'));
    }

    public function create()
    {
        $departments = HrDepartment::where('status', 1)->orderBy('name')->get();
        return view('backEnd.hr.designation.create', compact('departments'));
    }

    public function edit($id)
    {
        $edit_data = HrDesignation::findOrFail($id);
        $departments = HrDepartment::where('status', 1)->orderBy('name')->get();
        return view('backEnd.hr.designation.edit', compact('edit_data', 'departments'));
    }

    public function store(Request $request)
    {
        $this->validate($request, ['name' => 'required|string|max:255']);
        HrDesignation::create($request->only('department_id', 'name', 'notes') + ['status' => $request->status ? 1 : 0]);
        Toastr::success('Success', 'Designation created successfully');
        return redirect()->route('hr.designations.index');
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:hr_designations,id', 'name' => 'required|string|max:255']);
        $row = HrDesignation::findOrFail($request->id);
        $row->fill($request->only('department_id', 'name', 'notes'));
        $row->status = $request->status ? 1 : 0;
        $row->save();
        Toastr::success('Success', 'Designation updated successfully');
        return redirect()->route('hr.designations.index');
    }

    public function inactive(Request $request) { HrDesignation::where('id', $request->hidden_id)->update(['status' => 0]); Toastr::success('Success', 'Designation inactive successfully'); return redirect()->back(); }
    public function active(Request $request) { HrDesignation::where('id', $request->hidden_id)->update(['status' => 1]); Toastr::success('Success', 'Designation active successfully'); return redirect()->back(); }
    public function destroy(Request $request) { $ids = array_filter((array) $request->input('designation_ids', [])); !empty($ids) ? HrDesignation::whereIn('id', $ids)->delete() : HrDesignation::where('id', $request->hidden_id)->delete(); Toastr::success('Success', 'Designation deleted successfully'); return redirect()->back(); }
}
