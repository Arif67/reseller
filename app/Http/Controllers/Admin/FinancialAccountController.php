<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use Illuminate\Http\Request;
use Toastr;

class FinancialAccountController extends Controller
{
    public function index()
    {
        $data = FinancialAccount::latest()->get();
        return view('backEnd.accounts.financial_account.index', compact('data'));
    }

    public function create()
    {
        return view('backEnd.accounts.financial_account.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, ['name' => 'required|string|max:255', 'account_type' => 'required|string']);
        FinancialAccount::create([
            'name' => $request->name,
            'account_type' => $request->account_type,
            'account_no' => $request->account_no,
            'opening_balance' => $request->opening_balance ?: 0,
            'current_balance' => $request->opening_balance ?: 0,
            'note' => $request->note,
            'status' => $request->status ? 1 : 0,
        ]);
        Toastr::success('Success', 'Financial account created successfully');
        return redirect()->route('accounts.financial_accounts.index');
    }

    public function edit($id)
    {
        $edit_data = FinancialAccount::findOrFail($id);
        return view('backEnd.accounts.financial_account.edit', compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:financial_accounts,id', 'name' => 'required|string|max:255', 'account_type' => 'required|string']);
        $row = FinancialAccount::findOrFail($request->id);
        $row->fill($request->only('name', 'account_type', 'account_no', 'note'));
        $row->status = $request->status ? 1 : 0;
        $row->save();
        Toastr::success('Success', 'Financial account updated successfully');
        return redirect()->route('accounts.financial_accounts.index');
    }
}
