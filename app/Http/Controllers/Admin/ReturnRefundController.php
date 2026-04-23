<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ReturnRefund;
use Illuminate\Http\Request;
use Toastr;

class ReturnRefundController extends Controller
{
    public function index(Request $request)
    {
        $data = ReturnRefund::with('order')->latest('refund_date');
        if ($request->invoice_id) {
            $data->where('invoice_id', 'LIKE', '%' . $request->invoice_id . '%');
        }
        $data = $data->paginate(20)->withQueryString();
        return view('backEnd.accounts.return_refund.index', compact('data'));
    }

    public function create()
    {
        return view('backEnd.accounts.return_refund.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, ['amount' => 'required|numeric|min:0.01']);
        $order = null;
        if ($request->invoice_id) {
            $order = Order::where('invoice_id', $request->invoice_id)->first();
        }
        ReturnRefund::create([
            'order_id' => $order?->id,
            'invoice_id' => $request->invoice_id,
            'refund_date' => $request->refund_date ?: now()->toDateString(),
            'amount' => $request->amount,
            'return_type' => $request->return_type,
            'payment_method' => $request->payment_method,
            'reason' => $request->reason,
            'status' => $request->status ?: 'processed',
        ]);
        Toastr::success('Success', 'Return / refund entry created successfully');
        return redirect()->route('accounts.return_refunds.index');
    }
}
