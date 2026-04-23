<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrBonus;
use App\Models\HrEmployee;
use Illuminate\Http\Request;
use Toastr;

class HrBonusController extends Controller
{
    public function index(Request $request)
    {
        $data = HrBonus::with('employee.department')->latest('bonus_date');
        if ($request->employee_id) { $data->where('employee_id', $request->employee_id); }
        $data = $data->paginate(20)->withQueryString();
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();
        return view('backEnd.hr.bonus.index', compact('data', 'employees'));
    }

    public function create() { $employees = HrEmployee::where('status', 1)->orderBy('name')->get(); return view('backEnd.hr.bonus.create', compact('employees')); }
    public function edit($id) { $edit_data = HrBonus::findOrFail($id); $employees = HrEmployee::where('status', 1)->orderBy('name')->get(); return view('backEnd.hr.bonus.edit', compact('edit_data', 'employees')); }

    public function store(Request $request)
    {
        $this->validate($request, ['employee_id' => 'required|exists:hr_employees,id', 'title' => 'required|string|max:255']);
        HrBonus::create($request->only('employee_id', 'title', 'bonus_type', 'amount', 'bonus_date', 'notes') + ['status' => $request->status ? 1 : 0]);
        Toastr::success('Success', 'Bonus created successfully');
        return redirect()->route('hr.bonuses.index');
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:hr_bonuses,id', 'employee_id' => 'required|exists:hr_employees,id', 'title' => 'required|string|max:255']);
        $row = HrBonus::findOrFail($request->id);
        $row->fill($request->only('employee_id', 'title', 'bonus_type', 'amount', 'bonus_date', 'notes'));
        $row->status = $request->status ? 1 : 0;
        $row->save();
        Toastr::success('Success', 'Bonus updated successfully');
        return redirect()->route('hr.bonuses.index');
    }

    public function inactive(Request $request) { HrBonus::where('id', $request->hidden_id)->update(['status' => 0]); Toastr::success('Success', 'Bonus inactive successfully'); return redirect()->back(); }
    public function active(Request $request) { HrBonus::where('id', $request->hidden_id)->update(['status' => 1]); Toastr::success('Success', 'Bonus active successfully'); return redirect()->back(); }
    public function destroy(Request $request) { $ids = array_filter((array) $request->input('bonus_ids', [])); !empty($ids) ? HrBonus::whereIn('id', $ids)->delete() : HrBonus::where('id', $request->hidden_id)->delete(); Toastr::success('Success', 'Bonus deleted successfully'); return redirect()->back(); }
}
