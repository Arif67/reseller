<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrHoliday;
use Illuminate\Http\Request;
use Toastr;

class HrHolidayController extends Controller
{
    public function index(Request $request)
    {
        $data = HrHoliday::latest('holiday_date');
        if ($request->year) {
            $data->whereYear('holiday_date', $request->year);
        }
        $data = $data->paginate(20)->withQueryString();
        return view('backEnd.hr.holiday.index', compact('data'));
    }

    public function create() { return view('backEnd.hr.holiday.create'); }
    public function edit($id) { $edit_data = HrHoliday::findOrFail($id); return view('backEnd.hr.holiday.edit', compact('edit_data')); }

    public function store(Request $request)
    {
        $this->validate($request, ['name' => 'required|string|max:255', 'holiday_date' => 'required|date']);
        HrHoliday::create($request->only('name', 'holiday_date', 'holiday_type', 'notes') + ['status' => $request->status ? 1 : 0]);
        Toastr::success('Success', 'Holiday created successfully');
        return redirect()->route('hr.holidays.index');
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:hr_holidays,id', 'name' => 'required|string|max:255', 'holiday_date' => 'required|date']);
        $row = HrHoliday::findOrFail($request->id);
        $row->fill($request->only('name', 'holiday_date', 'holiday_type', 'notes'));
        $row->status = $request->status ? 1 : 0;
        $row->save();
        Toastr::success('Success', 'Holiday updated successfully');
        return redirect()->route('hr.holidays.index');
    }

    public function inactive(Request $request) { HrHoliday::where('id', $request->hidden_id)->update(['status' => 0]); Toastr::success('Success', 'Holiday inactive successfully'); return redirect()->back(); }
    public function active(Request $request) { HrHoliday::where('id', $request->hidden_id)->update(['status' => 1]); Toastr::success('Success', 'Holiday active successfully'); return redirect()->back(); }
    public function destroy(Request $request) { $ids = array_filter((array) $request->input('holiday_ids', [])); !empty($ids) ? HrHoliday::whereIn('id', $ids)->delete() : HrHoliday::where('id', $request->hidden_id)->delete(); Toastr::success('Success', 'Holiday deleted successfully'); return redirect()->back(); }
}
