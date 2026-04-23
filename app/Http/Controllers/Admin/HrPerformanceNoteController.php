<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrEmployee;
use App\Models\HrPerformanceNote;
use Illuminate\Http\Request;
use Toastr;

class HrPerformanceNoteController extends Controller
{
    public function index(Request $request)
    {
        $data = HrPerformanceNote::with('employee.department')->latest('note_date');
        if ($request->employee_id) { $data->where('employee_id', $request->employee_id); }
        $data = $data->paginate(20)->withQueryString();
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();
        return view('backEnd.hr.performance_note.index', compact('data', 'employees'));
    }

    public function create() { $employees = HrEmployee::where('status', 1)->orderBy('name')->get(); return view('backEnd.hr.performance_note.create', compact('employees')); }
    public function edit($id) { $edit_data = HrPerformanceNote::findOrFail($id); $employees = HrEmployee::where('status', 1)->orderBy('name')->get(); return view('backEnd.hr.performance_note.edit', compact('edit_data', 'employees')); }

    public function store(Request $request)
    {
        $this->validate($request, ['employee_id' => 'required|exists:hr_employees,id', 'title' => 'required|string|max:255']);
        HrPerformanceNote::create($request->only('employee_id', 'title', 'note_date', 'rating', 'description') + ['status' => $request->status ? 1 : 0]);
        Toastr::success('Success', 'Performance note created successfully');
        return redirect()->route('hr.performance_notes.index');
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:hr_performance_notes,id', 'employee_id' => 'required|exists:hr_employees,id', 'title' => 'required|string|max:255']);
        $row = HrPerformanceNote::findOrFail($request->id);
        $row->fill($request->only('employee_id', 'title', 'note_date', 'rating', 'description'));
        $row->status = $request->status ? 1 : 0;
        $row->save();
        Toastr::success('Success', 'Performance note updated successfully');
        return redirect()->route('hr.performance_notes.index');
    }

    public function inactive(Request $request) { HrPerformanceNote::where('id', $request->hidden_id)->update(['status' => 0]); Toastr::success('Success', 'Performance note inactive successfully'); return redirect()->back(); }
    public function active(Request $request) { HrPerformanceNote::where('id', $request->hidden_id)->update(['status' => 1]); Toastr::success('Success', 'Performance note active successfully'); return redirect()->back(); }
    public function destroy(Request $request) { $ids = array_filter((array) $request->input('performance_note_ids', [])); !empty($ids) ? HrPerformanceNote::whereIn('id', $ids)->delete() : HrPerformanceNote::where('id', $request->hidden_id)->delete(); Toastr::success('Success', 'Performance note deleted successfully'); return redirect()->back(); }
}
