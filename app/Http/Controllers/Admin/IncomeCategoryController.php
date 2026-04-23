<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;
use Toastr;

class IncomeCategoryController extends Controller
{
    public function index()
    {
        $data = IncomeCategory::latest()->get();
        return view('backEnd.accounts.income_category.index', compact('data'));
    }

    public function create()
    {
        return view('backEnd.accounts.income_category.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, ['name' => 'required|string|max:255']);
        IncomeCategory::create([
            'name' => $request->name,
            'note' => $request->note,
            'status' => $request->status ? 1 : 0,
        ]);
        Toastr::success('Success', 'Income category created successfully');
        return redirect()->route('accounts.income_categories.index');
    }

    public function edit($id)
    {
        $edit_data = IncomeCategory::findOrFail($id);
        return view('backEnd.accounts.income_category.edit', compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:income_categories,id', 'name' => 'required|string|max:255']);
        $row = IncomeCategory::findOrFail($request->id);
        $row->fill($request->only('name', 'note'));
        $row->status = $request->status ? 1 : 0;
        $row->save();
        Toastr::success('Success', 'Income category updated successfully');
        return redirect()->route('accounts.income_categories.index');
    }
}
