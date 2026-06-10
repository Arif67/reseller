<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\ResellerPaymentMethod;
use App\Models\ResellerWithdrawal;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Auth;

class WithdrawController extends Controller
{
    public function index()
    {
        $reseller = Auth::guard('reseller')->user();

        $available      = $reseller->availableBalance();
        $withdrawals    = ResellerWithdrawal::where('reseller_id', $reseller->id)->latest()->get();
        $paymentMethods = ResellerPaymentMethod::where('reseller_id', $reseller->id)->get();

        return view('resellerPanel.withdraw.index', compact('reseller', 'available', 'withdrawals', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $reseller  = Auth::guard('reseller')->user();
        $available = $reseller->availableBalance();

        $this->validate($request, [
            'amount'            => 'required|numeric|min:1|max:' . max($available, 0),
            'payment_method_id' => 'required|integer',
        ], [
            'amount.max' => 'Available balance er beshi withdraw kora jabe na (৳' . number_format($available, 0) . ')',
        ]);

        // selected method reseller-er nijer kina verify
        $method = ResellerPaymentMethod::where('reseller_id', $reseller->id)
            ->findOrFail($request->payment_method_id);

        ResellerWithdrawal::create([
            'reseller_id' => $reseller->id,
            'amount'      => $request->amount,
            'method'      => $method->type,
            'account'     => $method->label,
            'status'      => 'pending',
        ]);

        Toastr::success('Withdraw request submit holo. Admin approve korbe.', 'Success');
        return back();
    }
}
