<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountHead;
use App\Models\FinancialAccount;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use App\Services\Admin\PurchaseService;
use Illuminate\Http\Request;
use Toastr;

class PurchaseController extends Controller
{
    public function __construct(
        private PurchaseService $purchaseService
    ) {
    }

    public function index(Request $request)
    {
        $data = Purchase::with(['financialAccount'])->latest('purchase_date');

        if ($request->keyword) {
            $data->where(function ($query) use ($request) {
                $query->where('purchase_no', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('supplier_name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('reference_no', 'LIKE', '%' . $request->keyword . '%');
            });
        }

        if ($request->date_from) {
            $data->whereDate('purchase_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $data->whereDate('purchase_date', '<=', $request->date_to);
        }

        $data = $data->paginate(20)->withQueryString();

        return view('backEnd.accounts.purchase.index', compact('data'));
    }

    public function create()
    {
        $products = Product::with(['allVariables:id,product_id,size,color,stock,purchase_price,new_price'])
            ->select('id', 'name', 'purchase_price', 'new_price', 'stock', 'type', 'variation_pricing_mode')
            ->orderBy('name')
            ->get();
        $financialAccounts = FinancialAccount::where('status', 1)->orderBy('name')->get();
        $accountHeads = AccountHead::where('status', 1)->where('type', 'expense')->orderBy('name')->get();
        $supplierSuggestions = SupplierLedger::query()
            ->select('supplier_name')
            ->whereNotNull('supplier_name')
            ->distinct()
            ->orderBy('supplier_name')
            ->pluck('supplier_name');
        $suppliers = Supplier::where('status', 1)->orderBy('name')->get();

        return view('backEnd.accounts.purchase.create', compact('products', 'financialAccounts', 'accountHeads', 'supplierSuggestions', 'suppliers'));
    }

    public function edit($id)
    {
        $edit_data = Purchase::with('items')->findOrFail($id);
        $products = Product::with(['allVariables:id,product_id,size,color,stock,purchase_price,new_price'])
            ->select('id', 'name', 'purchase_price', 'new_price', 'stock', 'type', 'variation_pricing_mode')
            ->orderBy('name')
            ->get();
        $financialAccounts = FinancialAccount::where('status', 1)->orderBy('name')->get();
        $accountHeads = AccountHead::where('status', 1)->where('type', 'expense')->orderBy('name')->get();
        $supplierSuggestions = SupplierLedger::query()->select('supplier_name')->whereNotNull('supplier_name')->distinct()->orderBy('supplier_name')->pluck('supplier_name');
        $suppliers = Supplier::where('status', 1)->orderBy('name')->get();

        return view('backEnd.accounts.purchase.edit', compact('edit_data', 'products', 'financialAccounts', 'accountHeads', 'supplierSuggestions', 'suppliers'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'supplier_name' => 'required|string|max:255',
            'purchase_date' => 'nullable|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'product_variable_id' => 'nullable|array',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|integer|min:1',
            'unit_cost' => 'required|array|min:1',
            'unit_cost.*' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'transport_cost' => 'nullable|numeric|min:0',
            'other_cost' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        $purchase = $this->purchaseService->create($request->all());

        Toastr::success('Success', 'Purchase created and stock updated successfully');

        return redirect()->route('accounts.purchases.show', $purchase->id);
    }

    public function show($id)
    {
        $purchase = Purchase::with(['items.product', 'items.productVariable', 'financialAccount', 'accountHead', 'returns.items', 'payments'])
            ->findOrFail($id);

        return view('backEnd.accounts.purchase.show', compact('purchase'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:purchases,id',
            'supplier_name' => 'required|string|max:255',
            'purchase_date' => 'nullable|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|integer|min:1',
            'unit_cost' => 'required|array|min:1',
            'unit_cost.*' => 'required|numeric|min:0',
        ]);

        $purchase = Purchase::findOrFail($request->id);
        $purchase = $this->purchaseService->update($purchase, $request->all());

        Toastr::success('Success', 'Purchase updated with stock rollback successfully');
        return redirect()->route('accounts.purchases.show', $purchase->id);
    }

    public function returns()
    {
        $data = PurchaseReturn::with('purchase')->latest('return_date')->paginate(20);
        return view('backEnd.accounts.purchase_return.index', compact('data'));
    }

    public function createReturn($id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);
        return view('backEnd.accounts.purchase_return.create', compact('purchase'));
    }

    public function storeReturn(Request $request)
    {
        $this->validate($request, [
            'purchase_id' => 'required|exists:purchases,id',
            'purchase_item_id' => 'required|array|min:1',
            'qty' => 'required|array|min:1',
        ]);

        $purchase = Purchase::with('items')->findOrFail($request->purchase_id);
        $this->purchaseService->createReturn($purchase, $request->all());

        Toastr::success('Success', 'Purchase return processed successfully');
        return redirect()->route('accounts.purchase_returns.index');
    }
}
