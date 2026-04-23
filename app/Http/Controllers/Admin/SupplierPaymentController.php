<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountHead;
use App\Models\FinancialAccount;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use App\Models\SupplierPayment;
use App\Services\Admin\PurchaseService;
use Illuminate\Http\Request;
use Toastr;

class SupplierPaymentController extends Controller
{
    public function __construct(
        private PurchaseService $purchaseService
    ) {
    }

    public function index()
    {
        $data = SupplierPayment::with(['purchase', 'financialAccount'])->latest('payment_date')->paginate(20);
        return view('backEnd.accounts.supplier_payment.index', compact('data'));
    }

    public function create()
    {
        $financialAccounts = FinancialAccount::where('status', 1)->orderBy('name')->get();
        $accountHeads = AccountHead::where('status', 1)->where('type', 'expense')->orderBy('name')->get();
        $suppliers = SupplierLedger::query()->select('supplier_name')->distinct()->orderBy('supplier_name')->pluck('supplier_name');
        $supplierRows = Supplier::where('status', 1)->orderBy('name')->get();
        $purchases = Purchase::orderByDesc('purchase_date')->get(['id', 'purchase_no', 'supplier_name', 'due_amount']);
        return view('backEnd.accounts.supplier_payment.create', compact('financialAccounts', 'accountHeads', 'suppliers', 'supplierRows', 'purchases'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'supplier_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $this->purchaseService->settleSupplierPayment($request->all());
        Toastr::success('Success', 'Supplier payment saved successfully');
        return redirect()->route('accounts.supplier_payments.index');
    }
}
