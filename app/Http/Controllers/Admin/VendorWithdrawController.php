<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorWithdraw;
use Illuminate\Http\Request;
use Toastr;

class VendorWithdrawController extends Controller
{
    public function index(Request $request)
    {
        $query = VendorWithdraw::with('vendor');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $withdraws = $query->latest()->paginate(25)->withQueryString();

        $pendingTotal = VendorWithdraw::where('status', 'pending')->sum('amount');

        return view('backEnd.vendor.withdrawals', compact('withdraws', 'pendingTotal'));
    }

    public function process(Request $request)
    {
        $this->validate($request, [
            'id'     => 'required|integer',
            'action' => 'required|in:paid,rejected',
            'note'   => 'nullable|string|max:255',
        ]);

        $withdraw = VendorWithdraw::where('status', 'pending')->findOrFail($request->id);

        $withdraw->status       = $request->action;
        $withdraw->admin_note   = $request->note;
        $withdraw->processed_at = now();
        $withdraw->save();

        Toastr::success('Withdraw marked as ' . $request->action, 'Success');
        return back();
    }
}
