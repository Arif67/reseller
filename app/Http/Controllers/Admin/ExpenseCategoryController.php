<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Toastr;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $data = ExpenseCategories::latest()->get();

        return view('backEnd.expense_category.index', compact('data'));
    }

    public function create()
    {
        return view('backEnd.expense_category.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);

        ExpenseCategories::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status ? 1 : 0,
        ]);

        Toastr::success('Success', 'Expense category created successfully');

        return redirect()->route('expensecategories.index');
    }

    public function edit($id)
    {
        $edit_data = ExpenseCategories::findOrFail($id);

        return view('backEnd.expense_category.edit', compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:expense_categories,id',
            'name' => 'required|string|max:255',
        ]);

        $category = ExpenseCategories::findOrFail($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->status = $request->status ? 1 : 0;
        $category->save();

        Toastr::success('Success', 'Expense category updated successfully');

        return redirect()->route('expensecategories.index');
    }

    public function inactive(Request $request)
    {
        $category = ExpenseCategories::findOrFail($request->hidden_id);
        $category->status = 0;
        $category->save();

        Toastr::success('Success', 'Expense category inactive successfully');

        return redirect()->back();
    }

    public function active(Request $request)
    {
        $category = ExpenseCategories::findOrFail($request->hidden_id);
        $category->status = 1;
        $category->save();

        Toastr::success('Success', 'Expense category active successfully');

        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $ids = array_filter((array) $request->input('category_ids', []));

        if (! empty($ids)) {
            ExpenseCategories::whereIn('id', $ids)->delete();
        } elseif ($request->hidden_id) {
            ExpenseCategories::where('id', $request->hidden_id)->delete();
        }

        Toastr::success('Success', 'Expense category deleted successfully');

        return redirect()->back();
    }
}
