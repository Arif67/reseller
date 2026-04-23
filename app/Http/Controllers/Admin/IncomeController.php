<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountHead;
use App\Models\FinancialAccount;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Services\Admin\AccountingTransactionService;
use Illuminate\Http\Request;
use Toastr;

class IncomeController extends Controller
{
    public function __construct(
        private AccountingTransactionService $accountingTransactionService
    ) {
    }

    public function index(Request $request)
    {
        $data = Income::with(['category', 'financialAccount', 'accountHead'])->latest('received_at');
        if ($request->keyword) {
            $data->where('name', 'LIKE', '%' . $request->keyword . '%');
        }
        if ($request->income_category_id) {
            $data->where('income_category_id', $request->income_category_id);
        }
        $data = $data->paginate(20)->withQueryString();
        $categories = IncomeCategory::where('status', 1)->orderBy('name')->get();
        return view('backEnd.accounts.income.index', compact('data', 'categories'));
    }

    public function create()
    {
        $categories = IncomeCategory::where('status', 1)->orderBy('name')->get();
        $financialAccounts = FinancialAccount::where('status', 1)->orderBy('name')->get();
        $accountHeads = AccountHead::where('status', 1)->orderBy('name')->get();
        return view('backEnd.accounts.income.create', compact('categories', 'financialAccounts', 'accountHeads'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $income = Income::create([
            'name' => $request->name,
            'income_category_id' => $request->income_category_id,
            'financial_account_id' => $request->financial_account_id,
            'account_head_id' => $request->account_head_id,
            'received_at' => $request->received_at ?: now()->toDateString(),
            'amount' => $request->amount,
            'note' => $request->note,
            'status' => $request->status ? 1 : 0,
        ]);

        $this->accountingTransactionService->postIncome($income);

        Toastr::success('Success', 'Income created successfully');
        return redirect()->route('accounts.income.index');
    }

    public function edit($id)
    {
        $edit_data = Income::findOrFail($id);
        $categories = IncomeCategory::where('status', 1)->orderBy('name')->get();
        $financialAccounts = FinancialAccount::where('status', 1)->orderBy('name')->get();
        $accountHeads = AccountHead::where('status', 1)->orderBy('name')->get();
        return view('backEnd.accounts.income.edit', compact('edit_data', 'categories', 'financialAccounts', 'accountHeads'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:incomes,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $income = Income::findOrFail($request->id);
        $income->fill([
            'name' => $request->name,
            'income_category_id' => $request->income_category_id,
            'financial_account_id' => $request->financial_account_id,
            'account_head_id' => $request->account_head_id,
            'received_at' => $request->received_at ?: now()->toDateString(),
            'amount' => $request->amount,
            'note' => $request->note,
        ]);
        $income->status = $request->status ? 1 : 0;
        $income->save();

        $this->accountingTransactionService->postIncome($income);

        Toastr::success('Success', 'Income updated successfully');
        return redirect()->route('accounts.income.index');
    }
}
