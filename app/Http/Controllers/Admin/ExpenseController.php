<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountHead;
use App\Models\Expense;
use App\Models\ExpenseCategories;
use App\Models\FinancialAccount;
use App\Services\Admin\AccountingTransactionService;
use Illuminate\Http\Request;
use Toastr;

class ExpenseController extends Controller
{
    public function __construct(
        private AccountingTransactionService $accountingTransactionService
    ) {
    }

    public function index(Request $request)
    {
        $data = Expense::with(['category', 'financialAccount', 'accountHead'])->latest();

        if ($request->keyword) {
            $data->where('name', 'LIKE', '%' . $request->keyword . '%');
        }

        if ($request->expense_cat_id) {
            $data->where('expense_cat_id', $request->expense_cat_id);
        }

        if ($request->start_date && $request->end_date) {
            $data->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $data = $data->paginate(20)->withQueryString();
        $categories = ExpenseCategories::where('status', 1)->orderBy('name')->get();
        $financialAccounts = FinancialAccount::where('status', 1)->orderBy('name')->get();

        return view('backEnd.expense.index', compact('data', 'categories', 'financialAccounts'));
    }

    public function create()
    {
        $categories = ExpenseCategories::where('status', 1)->orderBy('name')->get();
        $financialAccounts = FinancialAccount::where('status', 1)->orderBy('name')->get();
        $accountHeads = AccountHead::where('status', 1)->orderBy('name')->get();

        return view('backEnd.expense.create', compact('categories', 'financialAccounts', 'accountHeads'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_cat_id' => 'nullable|exists:expense_categories,id',
        ]);

        $expense = Expense::create([
            'name' => $request->name,
            'expense_cat_id' => $request->expense_cat_id,
            'financial_account_id' => $request->financial_account_id,
            'account_head_id' => $request->account_head_id,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date ?: now()->toDateString(),
            'note' => $request->note,
            'status' => $request->status ? 1 : 0,
        ]);

        $this->accountingTransactionService->postExpense($expense);

        Toastr::success('Success', 'Expense created successfully');

        return redirect()->route('expenses.index');
    }

    public function edit($id)
    {
        $edit_data = Expense::findOrFail($id);
        $categories = ExpenseCategories::where('status', 1)->orderBy('name')->get();
        $financialAccounts = FinancialAccount::where('status', 1)->orderBy('name')->get();
        $accountHeads = AccountHead::where('status', 1)->orderBy('name')->get();

        return view('backEnd.expense.edit', compact('edit_data', 'categories', 'financialAccounts', 'accountHeads'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:expenses,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_cat_id' => 'nullable|exists:expense_categories,id',
        ]);

        $expense = Expense::findOrFail($request->id);
        $expense->name = $request->name;
        $expense->expense_cat_id = $request->expense_cat_id;
        $expense->financial_account_id = $request->financial_account_id;
        $expense->account_head_id = $request->account_head_id;
        $expense->amount = $request->amount;
        $expense->transaction_date = $request->transaction_date ?: now()->toDateString();
        $expense->note = $request->note;
        $expense->status = $request->status ? 1 : 0;
        $expense->save();

        $this->accountingTransactionService->postExpense($expense);

        Toastr::success('Success', 'Expense updated successfully');

        return redirect()->route('expenses.index');
    }

    public function inactive(Request $request)
    {
        $expense = Expense::findOrFail($request->hidden_id);
        $expense->status = 0;
        $expense->save();

        Toastr::success('Success', 'Expense inactive successfully');

        return redirect()->back();
    }

    public function active(Request $request)
    {
        $expense = Expense::findOrFail($request->hidden_id);
        $expense->status = 1;
        $expense->save();

        Toastr::success('Success', 'Expense active successfully');

        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $ids = array_filter((array) $request->input('expense_ids', []));

        if (! empty($ids)) {
            $expenses = Expense::whereIn('id', $ids)->get();
            foreach ($expenses as $expense) {
                $this->accountingTransactionService->removeExpense($expense);
                $expense->delete();
            }
        } elseif ($request->hidden_id) {
            $expense = Expense::findOrFail($request->hidden_id);
            $this->accountingTransactionService->removeExpense($expense);
            $expense->delete();
        }

        Toastr::success('Success', 'Expense deleted successfully');

        return redirect()->back();
    }
}
