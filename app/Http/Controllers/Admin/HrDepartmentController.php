<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Toastr;

class HrDepartmentController extends Controller
{
    public function index()
    {
        $data = HrDepartment::withCount('employees')->latest()->get();

        return view('backEnd.hr.department.index', compact('data'));
    }

    public function create()
    {
        return view('backEnd.hr.department.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);

        HrDepartment::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->status ? 1 : 0,
        ]);

        Toastr::success('Success', 'Department created successfully');

        return redirect()->route('hr.departments.index');
    }

    public function edit($id)
    {
        $edit_data = HrDepartment::findOrFail($id);

        return view('backEnd.hr.department.edit', compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:hr_departments,id',
            'name' => 'required|string|max:255',
        ]);

        $department = HrDepartment::findOrFail($request->id);
        $department->name = $request->name;
        $department->slug = Str::slug($request->name);
        $department->description = $request->description;
        $department->status = $request->status ? 1 : 0;
        $department->save();

        Toastr::success('Success', 'Department updated successfully');

        return redirect()->route('hr.departments.index');
    }

    public function inactive(Request $request)
    {
        $department = HrDepartment::findOrFail($request->hidden_id);
        $department->status = 0;
        $department->save();

        Toastr::success('Success', 'Department inactive successfully');

        return redirect()->back();
    }

    public function active(Request $request)
    {
        $department = HrDepartment::findOrFail($request->hidden_id);
        $department->status = 1;
        $department->save();

        Toastr::success('Success', 'Department active successfully');

        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $ids = array_filter((array) $request->input('department_ids', []));

        if (! empty($ids)) {
            HrDepartment::whereIn('id', $ids)->delete();
        } elseif ($request->hidden_id) {
            HrDepartment::where('id', $request->hidden_id)->delete();
        }

        Toastr::success('Success', 'Department deleted successfully');

        return redirect()->back();
    }
}
