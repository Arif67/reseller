<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrLeaveType;
use Illuminate\Http\Request;
use Toastr;

class HrLeaveTypeController extends Controller
{
    public function index() { $data = HrLeaveType::latest()->get(); return view('backEnd.hr.leave_type.index', compact('data')); }
    public function create() { return view('backEnd.hr.leave_type.create'); }
    public function edit($id) { $edit_data = HrLeaveType::findOrFail($id); return view('backEnd.hr.leave_type.edit', compact('edit_data')); }

    public function store(Request $request)
    {
        $this->validate($request, ['name' => 'required|string|max:255']);
        HrLeaveType::create($request->only('name', 'days_per_year', 'notes') + ['is_paid' => $request->is_paid ? 1 : 0, 'status' => $request->status ? 1 : 0]);
        Toastr::success('Success', 'Leave type created successfully');
        return redirect()->route('hr.leave_types.index');
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:hr_leave_types,id', 'name' => 'required|string|max:255']);
        $row = HrLeaveType::findOrFail($request->id);
        $row->fill($request->only('name', 'days_per_year', 'notes'));
        $row->is_paid = $request->is_paid ? 1 : 0;
        $row->status = $request->status ? 1 : 0;
        $row->save();
        Toastr::success('Success', 'Leave type updated successfully');
        return redirect()->route('hr.leave_types.index');
    }

    public function inactive(Request $request) { HrLeaveType::where('id', $request->hidden_id)->update(['status' => 0]); Toastr::success('Success', 'Leave type inactive successfully'); return redirect()->back(); }
    public function active(Request $request) { HrLeaveType::where('id', $request->hidden_id)->update(['status' => 1]); Toastr::success('Success', 'Leave type active successfully'); return redirect()->back(); }
    public function destroy(Request $request) { $ids = array_filter((array) $request->input('leave_type_ids', [])); !empty($ids) ? HrLeaveType::whereIn('id', $ids)->delete() : HrLeaveType::where('id', $request->hidden_id)->delete(); Toastr::success('Success', 'Leave type deleted successfully'); return redirect()->back(); }
}
