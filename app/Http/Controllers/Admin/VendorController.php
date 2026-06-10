<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Http\Request;
use Toastr;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::query();

        if ($request->keyword) {
            $query->where(function ($q) use ($request) {
                $q->where('phone', 'like', "%{$request->keyword}%")
                  ->orWhere('name', 'like', "%{$request->keyword}%")
                  ->orWhere('shop_name', 'like', "%{$request->keyword}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $show_data = $query->withCount('products')->latest()->paginate(20);

        return view('backEnd.vendor.index', compact('show_data'));
    }

    public function show($id)
    {
        $vendor = Vendor::withCount('products')->findOrFail($id);
        $products = Product::with('image')->where('vendor_id', $id)->latest()->paginate(15);

        return view('backEnd.vendor.show', compact('vendor', 'products'));
    }

    public function approve(Request $request)
    {
        $vendor = Vendor::findOrFail($request->hidden_id);
        $vendor->status = 'active';
        $vendor->save();

        Toastr::success('Vendor approved & activated', 'Success');
        return back();
    }

    public function suspend(Request $request)
    {
        $vendor = Vendor::findOrFail($request->hidden_id);
        $vendor->status = 'suspended';
        $vendor->save();

        Toastr::success('Vendor suspended', 'Success');
        return back();
    }

    public function updateCommission(Request $request)
    {
        $this->validate($request, [
            'hidden_id'       => 'required|integer',
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        $vendor = Vendor::findOrFail($request->hidden_id);
        $vendor->commission_rate = $request->commission_rate;
        $vendor->save();

        Toastr::success('Commission rate updated', 'Success');
        return back();
    }

    public function destroy(Request $request)
    {
        $vendor = Vendor::findOrFail($request->hidden_id);

        if (Product::where('vendor_id', $vendor->id)->exists()) {
            Toastr::error('Vendor er product ache, age product delete korun', 'Error');
            return back();
        }

        $vendor->delete();
        Toastr::success('Vendor deleted', 'Success');
        return back();
    }
}
