<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplierLedger;
use Illuminate\Http\Request;
use Toastr;

class SupplierLedgerController extends Controller
{
    public function index(Request $request)
    {
        $data = SupplierLedger::latest('transaction_date');
        if ($request->keyword) {
            $data->where('supplier_name', 'LIKE', '%' . $request->keyword . '%');
        }
        $data = $data->paginate(20)->withQueryString();
        return view('backEnd.accounts.supplier.index', compact('data'));
    }

    public function create()
    {
        return view('backEnd.accounts.supplier.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, ['supplier_name' => 'required|string|max:255', 'amount' => 'required|numeric|min:0']);
        $amount = (float) $request->amount;
        $paid = (float) ($request->paid_amount ?: 0);
        SupplierLedger::create([
            'supplier_name' => $request->supplier_name,
            'transaction_date' => $request->transaction_date ?: now()->toDateString(),
            'reference_no' => $request->reference_no,
            'amount' => $amount,
            'paid_amount' => $paid,
            'due_amount' => $amount - $paid,
            'note' => $request->note,
            'status' => $request->status ? 1 : 0,
        ]);
        Toastr::success('Success', 'Supplier ledger entry created successfully');
        return redirect()->route('accounts.suppliers.index');
    }

    public function edit($id)
    {
        $edit_data = SupplierLedger::findOrFail($id);
        return view('backEnd.accounts.supplier.edit', compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:supplier_ledgers,id', 'supplier_name' => 'required|string|max:255', 'amount' => 'required|numeric|min:0']);
        $row = SupplierLedger::findOrFail($request->id);
        $amount = (float) $request->amount;
        $paid = (float) ($request->paid_amount ?: 0);
        $row->fill([
            'supplier_name' => $request->supplier_name,
            'transaction_date' => $request->transaction_date ?: now()->toDateString(),
            'reference_no' => $request->reference_no,
            'amount' => $amount,
            'paid_amount' => $paid,
            'due_amount' => $amount - $paid,
            'note' => $request->note,
        ]);
        $row->status = $request->status ? 1 : 0;
        $row->save();
        Toastr::success('Success', 'Supplier ledger entry updated successfully');
        return redirect()->route('accounts.suppliers.index');
    }
}
