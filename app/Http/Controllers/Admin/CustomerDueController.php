<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerDue;
use Illuminate\Http\Request;
use Toastr;

class CustomerDueController extends Controller
{
    public function index(Request $request)
    {
        $data = CustomerDue::with('customer')->latest('due_date');
        if ($request->customer_id) {
            $data->where('customer_id', $request->customer_id);
        }
        $data = $data->paginate(20)->withQueryString();
        $customers = Customer::orderBy('name')->get();
        return view('backEnd.accounts.customer_due.index', compact('data', 'customers'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        return view('backEnd.accounts.customer_due.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $this->validate($request, ['amount' => 'required|numeric|min:0']);
        $amount = (float) $request->amount;
        $paid = (float) ($request->paid_amount ?: 0);
        CustomerDue::create([
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'invoice_id' => $request->invoice_id,
            'due_date' => $request->due_date ?: now()->toDateString(),
            'amount' => $amount,
            'paid_amount' => $paid,
            'due_amount' => $amount - $paid,
            'note' => $request->note,
            'status' => $request->status ? 1 : 0,
        ]);
        Toastr::success('Success', 'Customer due created successfully');
        return redirect()->route('accounts.customer_dues.index');
    }

    public function edit($id)
    {
        $edit_data = CustomerDue::findOrFail($id);
        $customers = Customer::orderBy('name')->get();
        return view('backEnd.accounts.customer_due.edit', compact('edit_data', 'customers'));
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:customer_dues,id', 'amount' => 'required|numeric|min:0']);
        $row = CustomerDue::findOrFail($request->id);
        $amount = (float) $request->amount;
        $paid = (float) ($request->paid_amount ?: 0);
        $row->fill([
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'invoice_id' => $request->invoice_id,
            'due_date' => $request->due_date ?: now()->toDateString(),
            'amount' => $amount,
            'paid_amount' => $paid,
            'due_amount' => $amount - $paid,
            'note' => $request->note,
        ]);
        $row->status = $request->status ? 1 : 0;
        $row->save();
        Toastr::success('Success', 'Customer due updated successfully');
        return redirect()->route('accounts.customer_dues.index');
    }
}
