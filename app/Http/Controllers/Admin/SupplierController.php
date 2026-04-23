<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplierLedger;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Toastr;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $data = Supplier::latest();

        if ($request->keyword) {
            $data->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('phone', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('company_name', 'LIKE', '%' . $request->keyword . '%');
            });
        }

        $data = $data->paginate(20)->withQueryString();

        return view('backEnd.accounts.supplier_master.index', compact('data'));
    }

    public function create()
    {
        return view('backEnd.accounts.supplier_master.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        Supplier::create([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'note' => $request->note,
            'status' => $request->status ? 1 : 0,
        ]);

        Toastr::success('Success', 'Supplier created successfully');
        return redirect()->route('accounts.supplier_master.index');
    }

    public function edit($id)
    {
        $edit_data = Supplier::findOrFail($id);
        return view('backEnd.accounts.supplier_master.edit', compact('edit_data'));
    }

    public function show($id)
    {
        $supplier = Supplier::with(['purchases' => function ($query) {
            $query->latest('purchase_date')->limit(15);
        }])->findOrFail($id);

        $ledger = SupplierLedger::where('supplier_name', $supplier->name)
            ->latest('transaction_date')
            ->paginate(20);

        $summary = [
            'total_purchase' => (float) $supplier->purchases()->sum('grand_total'),
            'total_paid' => (float) $supplier->purchases()->sum('paid_amount'),
            'total_due' => (float) $supplier->purchases()->sum('due_amount'),
        ];

        return view('backEnd.accounts.supplier_master.show', compact('supplier', 'ledger', 'summary'));
    }

    public function statement($id)
    {
        $supplier = Supplier::findOrFail($id);
        $rows = SupplierLedger::where('supplier_name', $supplier->name)
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        return view('backEnd.accounts.supplier_master.statement', compact('supplier', 'rows'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $supplier = Supplier::findOrFail($request->id);
        $supplier->fill([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'note' => $request->note,
        ]);
        $supplier->status = $request->status ? 1 : 0;
        $supplier->save();

        Toastr::success('Success', 'Supplier updated successfully');
        return redirect()->route('accounts.supplier_master.index');
    }
}
