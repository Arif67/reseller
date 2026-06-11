<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\VendorWithdraw;
use App\Support\VendorEarnings;
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

    /**
     * Order status slugs that count as a completed sale and therefore
     * generate earnings for the vendor.
     */
    private const COMPLETED_SLUGS = ['delivered', 'completed'];

    public function dashboard()
    {
        $vendor   = Auth::guard('vendor')->user();
        $vendorId = $vendor->id;

        $totalProducts = Product::where('vendor_id', $vendorId)->count();

        $totalOrders = Order::whereHas('orderdetails.product', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })->count();

        $ownOrders = fn () => Order::whereHas('orderdetails.product', fn ($q) => $q->where('vendor_id', $vendorId));

        $deliveredOrders = $ownOrders()
            ->whereHas('status', fn ($q) => $q->whereIn('slug', VendorEarnings::EARNED_SLUGS))
            ->count();

        $returnedOrders = $ownOrders()
            ->whereHas('status', fn ($q) => $q->whereIn('slug', VendorEarnings::RETURNED_SLUGS))
            ->count();

        // Money split into order-state buckets + withdraw figures.
        $summary = VendorEarnings::summary($vendorId);
        $balance = $summary['available'];

        $recentOrders = Order::with(['status', 'shipping'])
            ->whereHas('orderdetails.product', fn ($q) => $q->where('vendor_id', $vendorId))
            ->latest()
            ->take(5)
            ->get();

        $data = [
            'total_products'   => $totalProducts,
            'total_orders'     => $totalOrders,
            'delivered_orders' => $deliveredOrders,
            'returned_orders'  => $returnedOrders,
            'summary'          => $summary,
            'balance'          => $balance,
            'recent_orders'    => $recentOrders,
        ];

        return view('vendorPanel.dashboard', $data);
    }

    public function payment()
    {
        $vendor  = Auth::guard('vendor')->user();
        $summary = VendorEarnings::summary($vendor->id);

        $payoutMethods = $vendor->payoutMethods()->get();

        $withdraws = VendorWithdraw::where('vendor_id', $vendor->id)
            ->latest()
            ->paginate(15);

        return view('vendorPanel.payment', compact('vendor', 'summary', 'withdraws', 'payoutMethods'));
    }

    // Add a saved payout method (vendor can keep several).
    public function storePayoutMethod(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();

        $this->validate($request, [
            'method'      => 'required|string|max:50',
            'account'     => 'required|string|max:100',
            'holder_name' => 'nullable|string|max:100',
        ]);

        $makeDefault = $request->boolean('is_default') || $vendor->payoutMethods()->count() === 0;

        if ($makeDefault) {
            $vendor->payoutMethods()->update(['is_default' => 0]);
        }

        $vendor->payoutMethods()->create([
            'method'      => $request->method,
            'account'     => $request->account,
            'holder_name' => $request->holder_name,
            'is_default'  => $makeDefault,
        ]);

        Toastr::success('Payout method added', 'Success');
        return back();
    }

    public function setDefaultPayoutMethod(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        $method = $vendor->payoutMethods()->findOrFail($request->id);

        $vendor->payoutMethods()->update(['is_default' => 0]);
        $method->is_default = 1;
        $method->save();

        Toastr::success('Default payout method updated', 'Success');
        return back();
    }

    public function deletePayoutMethod(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        $vendor->payoutMethods()->where('id', $request->id)->delete();

        Toastr::success('Payout method removed', 'Success');
        return back();
    }

    public function withdrawRequest(Request $request)
    {
        $vendor  = Auth::guard('vendor')->user();
        $summary = VendorEarnings::summary($vendor->id);

        $this->validate($request, [
            'amount'            => 'required|numeric|min:1|max:' . $summary['available'],
            'payout_method_id'  => 'required|integer',
        ], [
            'amount.max'              => 'Amount cannot be more than your available balance (৳' . number_format($summary['available'], 2) . ').',
            'payout_method_id.required' => 'Please select a payout method.',
        ]);

        // Snapshot the chosen saved method onto the withdraw row.
        $method = $vendor->payoutMethods()->findOrFail($request->payout_method_id);

        VendorWithdraw::create([
            'vendor_id' => $vendor->id,
            'amount'    => $request->amount,
            'method'    => $method->method,
            'account'   => $method->account,
            'status'    => 'pending',
        ]);

        Toastr::success('Withdraw request submitted. Admin will process it soon.', 'Success');
        return back();
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
