<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrShift;
use Illuminate\Http\Request;
use Toastr;

class HrShiftController extends Controller
{
    public function index()
    {
        $data = HrShift::latest()->get();
        return view('backEnd.hr.shift.index', compact('data'));
    }

    public function create()
    {
        return view('backEnd.hr.shift.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, ['name' => 'required|string|max:255']);
        HrShift::create($request->only('name', 'start_time', 'end_time', 'late_after', 'notes') + ['status' => $request->status ? 1 : 0]);
        Toastr::success('Success', 'Shift created successfully');
        return redirect()->route('hr.shifts.index');
    }

    public function edit($id)
    {
        $edit_data = HrShift::findOrFail($id);
        return view('backEnd.hr.shift.edit', compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:hr_shifts,id', 'name' => 'required|string|max:255']);
        $shift = HrShift::findOrFail($request->id);
        $shift->fill($request->only('name', 'start_time', 'end_time', 'late_after', 'notes'));
        $shift->status = $request->status ? 1 : 0;
        $shift->save();
        Toastr::success('Success', 'Shift updated successfully');
        return redirect()->route('hr.shifts.index');
    }

    public function inactive(Request $request) { HrShift::where('id', $request->hidden_id)->update(['status' => 0]); Toastr::success('Success', 'Shift inactive successfully'); return redirect()->back(); }
    public function active(Request $request) { HrShift::where('id', $request->hidden_id)->update(['status' => 1]); Toastr::success('Success', 'Shift active successfully'); return redirect()->back(); }
    public function destroy(Request $request) { $ids = array_filter((array) $request->input('shift_ids', [])); !empty($ids) ? HrShift::whereIn('id', $ids)->delete() : HrShift::where('id', $request->hidden_id)->delete(); Toastr::success('Success', 'Shift deleted successfully'); return redirect()->back(); }
}
