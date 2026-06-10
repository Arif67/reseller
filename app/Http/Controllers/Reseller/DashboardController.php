<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Services\AppService\FileUploadService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;

class DashboardController extends Controller
{
    public function __construct(private FileUploadService $fileUpload)
    {
    }

    public function dashboard()
    {
        $reseller = Auth::guard('reseller')->user();

        $data = [
            'total_orders'     => \App\Models\Order::where('reseller_id', $reseller->id)->count(),
            'delivered_orders' => \App\Models\Order::where('reseller_id', $reseller->id)->where('order_status', 7)->count(),
            'delivered_margin' => $reseller->deliveredMargin(),
            'pending_margin'   => $reseller->pendingMargin(),
            'withdrawn'        => $reseller->withdrawnAmount(),
            'balance'          => $reseller->availableBalance(),
        ];

        return view('resellerPanel.dashboard', $data);
    }

    public function profile()
    {
        $reseller = Auth::guard('reseller')->user();
        return view('resellerPanel.profile', compact('reseller'));
    }

    public function paymentMethods()
    {
        $paymentMethods = \App\Models\ResellerPaymentMethod::where('reseller_id', Auth::guard('reseller')->id())
            ->latest()->get();

        return view('resellerPanel.payment_methods.index', compact('paymentMethods'));
    }

    public function addPaymentMethod(Request $request)
    {
        $this->validate($request, [
            'type'           => 'required|in:bkash,nagad,bank',
            'account_number' => 'required|string|max:100',
            'account_name'   => 'nullable|string|max:155',
            'bank_name'      => 'nullable|string|max:155',
        ]);

        \App\Models\ResellerPaymentMethod::create([
            'reseller_id'    => Auth::guard('reseller')->id(),
            'type'           => $request->type,
            'account_number' => $request->account_number,
            'account_name'   => $request->account_name,
            'bank_name'      => $request->type === 'bank' ? $request->bank_name : null,
        ]);

        Toastr::success('Payment method add holo', 'Success');
        return back();
    }

    public function deletePaymentMethod(Request $request)
    {
        \App\Models\ResellerPaymentMethod::where('reseller_id', Auth::guard('reseller')->id())
            ->where('id', $request->id)
            ->delete();

        Toastr::success('Payment method delete holo', 'Success');
        return back();
    }

    public function profileUpdate(Request $request)
    {
        $reseller = Auth::guard('reseller')->user();

        $this->validate($request, [
            'name'          => 'required|string|max:155',
            'business_name' => 'nullable|string|max:155',
            'phone'         => 'required|string|max:55|unique:resellers,phone,' . $reseller->id,
            'email'         => 'nullable|email|max:100',
            'address'       => 'nullable|string|max:255',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $reseller->name          = $request->name;
        $reseller->business_name = $request->business_name;
        $reseller->phone         = $request->phone;
        $reseller->email         = $request->email;
        $reseller->address       = $request->address;

        if ($request->hasFile('image')) {
            $reseller->image = $this->fileUpload->processAndUploadImage($request->file('image'), 'public/uploads/reseller', [
                'width'  => 300,
                'prefix' => 'reseller',
            ]);
        }

        $reseller->save();

        Toastr::success('Profile updated successfully', 'Success');
        return back();
    }

    public function passwordUpdate(Request $request)
    {
        $reseller = Auth::guard('reseller')->user();

        $this->validate($request, [
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $reseller->password)) {
            Toastr::error('Current password thik na', 'Error');
            return back();
        }

        $reseller->password = bcrypt($request->password);
        $reseller->save();

        Toastr::success('Password changed successfully', 'Success');
        return back();
    }
}
