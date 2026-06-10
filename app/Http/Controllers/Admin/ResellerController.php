<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Reseller;
use App\Models\ResellerWithdrawal;
use Illuminate\Http\Request;
use Toastr;

class ResellerController extends Controller
{
    public function index(Request $request)
    {
        $query = Reseller::query();

        if ($request->keyword) {
            $query->where(function ($q) use ($request) {
                $q->where('phone', 'like', "%{$request->keyword}%")
                  ->orWhere('name', 'like', "%{$request->keyword}%")
                  ->orWhere('business_name', 'like', "%{$request->keyword}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $show_data = $query->latest()->paginate(20);
        $pendingWithdrawCount = ResellerWithdrawal::where('status', 'pending')->count();

        return view('backEnd.reseller.index', compact('show_data', 'pendingWithdrawCount'));
    }

    public function show($id)
    {
        $reseller = Reseller::findOrFail($id);
        $orders = Order::where('reseller_id', $id)->latest()->paginate(15);

        $stats = [
            'total_orders'     => Order::where('reseller_id', $id)->count(),
            'delivered_margin' => $reseller->deliveredMargin(),
            'pending_margin'   => $reseller->pendingMargin(),
            'withdrawn'        => $reseller->withdrawnAmount(),
            'balance'          => $reseller->availableBalance(),
        ];

        return view('backEnd.reseller.show', compact('reseller', 'orders', 'stats'));
    }

    public function approve(Request $request)
    {
        $reseller = Reseller::findOrFail($request->hidden_id);
        $reseller->status = 'active';
        $reseller->save();

        Toastr::success('Reseller approved & activated', 'Success');
        return back();
    }

    public function suspend(Request $request)
    {
        $reseller = Reseller::findOrFail($request->hidden_id);
        $reseller->status = 'suspended';
        $reseller->save();

        Toastr::success('Reseller suspended', 'Success');
        return back();
    }

    public function updateMargin(Request $request)
    {
        $this->validate($request, [
            'hidden_id'            => 'required|integer',
            'default_margin_type'  => 'required|in:percent,flat',
            'default_margin_value' => 'required|numeric|min:0',
        ]);

        $reseller = Reseller::findOrFail($request->hidden_id);
        $reseller->default_margin_type  = $request->default_margin_type;
        $reseller->default_margin_value = $request->default_margin_value;
        $reseller->save();

        Toastr::success('Margin updated', 'Success');
        return back();
    }

    public function destroy(Request $request)
    {
        $reseller = Reseller::findOrFail($request->hidden_id);

        if (Order::where('reseller_id', $reseller->id)->exists()) {
            Toastr::error('Ei reseller-er order ache, delete kora jabe na', 'Error');
            return back();
        }

        $reseller->delete();
        Toastr::success('Reseller deleted', 'Success');
        return back();
    }

    /* ---------- Withdrawals ---------- */

    public function withdrawals(Request $request)
    {
        $query = ResellerWithdrawal::with('reseller')->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->paginate(20);

        return view('backEnd.reseller.withdrawals', compact('withdrawals'));
    }

    public function withdrawStatus(Request $request)
    {
        $this->validate($request, [
            'hidden_id' => 'required|integer',
            'status'    => 'required|in:approved,paid,rejected',
        ]);

        $withdrawal = ResellerWithdrawal::findOrFail($request->hidden_id);
        $withdrawal->status = $request->status;
        if ($request->filled('note')) {
            $withdrawal->note = $request->note;
        }
        $withdrawal->save();

        Toastr::success('Withdraw status: ' . $request->status, 'Success');
        return back();
    }
}
