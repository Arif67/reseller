<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountHead;
use App\Models\AccountTransaction;
use App\Models\FinancialAccount;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\Admin\DailyClosingService;
use Illuminate\Http\Request;

class AccountsReportController extends Controller
{
    public function __construct(
        private DailyClosingService $dailyClosingService
    ) {
    }

    public function ledger(Request $request)
    {
        $data = AccountTransaction::with(['financialAccount', 'accountHead'])->latest('transaction_date');
        if ($request->financial_account_id) {
            $data->where('financial_account_id', $request->financial_account_id);
        }
        if ($request->account_head_id) {
            $data->where('account_head_id', $request->account_head_id);
        }
        if ($request->start_date && $request->end_date) {
            $data->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }
        $data = $data->paginate(30)->withQueryString();
        $financialAccounts = FinancialAccount::where('status', 1)->orderBy('name')->get();
        $accountHeads = AccountHead::where('status', 1)->orderBy('name')->get();
        return view('backEnd.accounts.report.ledger', compact('data', 'financialAccounts', 'accountHeads'));
    }

    public function dailyClosing(Request $request)
    {
        $summary = $this->dailyClosingService->summarize($request->start_date, $request->end_date);
        $transactions = AccountTransaction::with(['financialAccount', 'accountHead'])
            ->whereBetween('transaction_date', [$summary['start_date'], $summary['end_date']])
            ->latest('transaction_date')
            ->limit(50)
            ->get();
        return view('backEnd.accounts.report.daily_closing', compact('summary', 'transactions'));
    }

    public function purchaseReport(Request $request)
    {
        $data = Purchase::with(['supplier', 'financialAccount'])->latest('purchase_date');

        if ($request->supplier_id) {
            $data->where('supplier_id', $request->supplier_id);
        }

        if ($request->date_from) {
            $data->whereDate('purchase_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $data->whereDate('purchase_date', '<=', $request->date_to);
        }

        $purchases = $data->paginate(20)->withQueryString();

        $summaryQuery = Purchase::query();
        if ($request->supplier_id) {
            $summaryQuery->where('supplier_id', $request->supplier_id);
        }
        if ($request->date_from) {
            $summaryQuery->whereDate('purchase_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $summaryQuery->whereDate('purchase_date', '<=', $request->date_to);
        }

        $summary = [
            'total_purchase' => (float) $summaryQuery->sum('grand_total'),
            'total_paid' => (float) $summaryQuery->sum('paid_amount'),
            'total_due' => (float) $summaryQuery->sum('due_amount'),
            'total_count' => (int) $summaryQuery->count(),
        ];

        $suppliers = Supplier::where('status', 1)->orderBy('name')->get();

        return view('backEnd.accounts.report.purchase_report', compact('purchases', 'summary', 'suppliers'));
    }

    public function purchaseReportPrint(Request $request)
    {
        $data = Purchase::with(['supplier', 'financialAccount'])->latest('purchase_date');

        if ($request->supplier_id) {
            $data->where('supplier_id', $request->supplier_id);
        }

        if ($request->date_from) {
            $data->whereDate('purchase_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $data->whereDate('purchase_date', '<=', $request->date_to);
        }

        $purchases = $data->get();

        return view('backEnd.accounts.report.purchase_report_print', compact('purchases'));
    }
}
