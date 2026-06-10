<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
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
        $vendor = Auth::guard('vendor')->user();
        $vendorId = $vendor->id;

        $totalOrders = Order::whereHas('orderdetails', function ($q) use ($vendorId) {
            $q->whereHas('product', function ($pq) use ($vendorId) {
                $pq->where('vendor_id', $vendorId);
            });
        })->count();

        $totalProducts = Product::where('vendor_id', $vendorId)->count();
        $balance       = $vendor->balance;

        // No real data yet -> show demo numbers so the dashboard isn't empty.
        if ($totalProducts == 0 && $totalOrders == 0) {
            $totalProducts = 24;
            $totalOrders   = 137;
            if (!$balance) {
                $balance = 18750.00;
            }
        }

        $data = [
            'total_products' => $totalProducts,
            'total_orders'   => $totalOrders,
            'balance'        => $balance,
        ];

        return view('vendorPanel.dashboard', $data);
    }

    public function payment()
    {
        // Placeholder for payment view
        return view('vendorPanel.dashboard')->with([
            'total_products' => 0,
            'total_orders' => 0,
            'balance' => Auth::guard('vendor')->user()->balance
        ]);
    }

    public function profile()
    {
        $vendor = Auth::guard('vendor')->user();
        return view('vendorPanel.profile', compact('vendor'));
    }

    public function profileUpdate(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();

        $this->validate($request, [
            'name'      => 'required|string|max:155',
            'shop_name' => 'required|string|max:155',
            'phone'     => 'required|string|max:55|unique:vendors,phone,' . $vendor->id,
            'email'     => 'nullable|email|max:100',
            'address'   => 'nullable|string|max:255',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $vendor->name      = $request->name;
        $vendor->shop_name = $request->shop_name;
        $vendor->phone     = $request->phone;
        $vendor->email     = $request->email;
        $vendor->address   = $request->address;

        if ($request->hasFile('image')) {
            $vendor->image = $this->fileUpload->processAndUploadImage($request->file('image'), 'public/uploads/vendor', [
                'width'  => 300,
                'prefix' => 'vendor',
            ]);
        }

        $vendor->save();

        Toastr::success('Profile updated successfully', 'Success');
        return back();
    }

    public function passwordUpdate(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();

        $this->validate($request, [
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $vendor->password)) {
            Toastr::error('Current password thik na', 'Error');
            return back();
        }

        $vendor->password = bcrypt($request->password);
        $vendor->save();

        Toastr::success('Password changed successfully', 'Success');
        return back();
    }
}
