<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrNotice;
use Illuminate\Http\Request;
use Toastr;

class HrNoticeController extends Controller
{
    public function index() { $data = HrNotice::latest('notice_date')->paginate(20); return view('backEnd.hr.notice.index', compact('data')); }
    public function create() { return view('backEnd.hr.notice.create'); }
    public function edit($id) { $edit_data = HrNotice::findOrFail($id); return view('backEnd.hr.notice.edit', compact('edit_data')); }

    public function store(Request $request)
    {
        $this->validate($request, ['title' => 'required|string|max:255']);
        HrNotice::create($request->only('title', 'notice_date', 'audience', 'description') + ['status' => $request->status ? 1 : 0]);
        Toastr::success('Success', 'Notice created successfully');
        return redirect()->route('hr.notices.index');
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:hr_notices,id', 'title' => 'required|string|max:255']);
        $row = HrNotice::findOrFail($request->id);
        $row->fill($request->only('title', 'notice_date', 'audience', 'description'));
        $row->status = $request->status ? 1 : 0;
        $row->save();
        Toastr::success('Success', 'Notice updated successfully');
        return redirect()->route('hr.notices.index');
    }

    public function inactive(Request $request) { HrNotice::where('id', $request->hidden_id)->update(['status' => 0]); Toastr::success('Success', 'Notice inactive successfully'); return redirect()->back(); }
    public function active(Request $request) { HrNotice::where('id', $request->hidden_id)->update(['status' => 1]); Toastr::success('Success', 'Notice active successfully'); return redirect()->back(); }
    public function destroy(Request $request) { $ids = array_filter((array) $request->input('notice_ids', [])); !empty($ids) ? HrNotice::whereIn('id', $ids)->delete() : HrNotice::where('id', $request->hidden_id)->delete(); Toastr::success('Success', 'Notice deleted successfully'); return redirect()->back(); }
}
