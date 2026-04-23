<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountHead;
use App\Models\FinancialAccount;
use App\Models\FundTransfer;
use App\Services\Admin\FundTransferService;
use Illuminate\Http\Request;
use Toastr;

class FundTransferController extends Controller
{
    public function __construct(
        private FundTransferService $fundTransferService
    ) {
    }

    public function index()
    {
        $data = FundTransfer::with(['fromAccount', 'toAccount'])->latest('transfer_date')->paginate(20);
        return view('backEnd.accounts.fund_transfer.index', compact('data'));
    }

    public function create()
    {
        $financialAccounts = FinancialAccount::where('status', 1)->orderBy('name')->get();
        $accountHeads = AccountHead::where('status', 1)->orderBy('name')->get();
        return view('backEnd.accounts.fund_transfer.create', compact('financialAccounts', 'accountHeads'));
    }

    public function store(Request $request)
    {
        $this->validate($request, ['from_financial_account_id' => 'required|different:to_financial_account_id', 'to_financial_account_id' => 'required', 'amount' => 'required|numeric|min:0.01']);
        $transfer = FundTransfer::create([
            'from_financial_account_id' => $request->from_financial_account_id,
            'to_financial_account_id' => $request->to_financial_account_id,
            'account_head_id' => $request->account_head_id,
            'transfer_date' => $request->transfer_date ?: now()->toDateString(),
            'amount' => $request->amount,
            'reference_no' => $request->reference_no,
            'note' => $request->note,
            'status' => 1,
        ]);
        $this->fundTransferService->post($transfer);
        Toastr::success('Success', 'Fund transfer created successfully');
        return redirect()->route('accounts.fund_transfers.index');
    }
}
