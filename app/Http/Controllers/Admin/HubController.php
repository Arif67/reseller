<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HubStock;
use App\Models\OrderDetails;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Toastr;

class HubController extends Controller
{
    /**
     * Receiving queue: items a vendor has collected but the hub
     * hasn't received yet. Admin confirms each one item-by-item.
     */
    public function receiving(Request $request)
    {
        $query = OrderDetails::query()
            ->where('vendor_collected', 1)
            ->where('admin_received', 0)
            ->whereHas('product', fn ($q) => $q->whereNotNull('vendor_id')->where('vendor_id', '>', 0))
            ->with(['product.image', 'product.vendor', 'image', 'order']);

        if ($request->vendor_id) {
            $query->whereHas('product', fn ($q) => $q->where('vendor_id', $request->vendor_id));
        }

        $items   = $query->latest('vendor_collected_at')->paginate(24)->withQueryString();
        $vendors = Vendor::orderBy('shop_name')->get(['id', 'shop_name']);

        return view('backEnd.hub.receiving', compact('items', 'vendors'));
    }

    /**
     * Mark one collected item as received at the hub and add its qty
     * to the hub stock for that product/variant.
     */
    public function receive(Request $request)
    {
        $this->validate($request, ['id' => 'required|integer']);

        $detail = OrderDetails::where('id', $request->id)
            ->where('vendor_collected', 1)
            ->where('admin_received', 0)
            ->whereHas('product', fn ($q) => $q->whereNotNull('vendor_id')->where('vendor_id', '>', 0))
            ->firstOrFail();

        DB::transaction(function () use ($detail) {
            $detail->admin_received = 1;
            $detail->admin_received_at = now();
            $detail->save();

            $stock = HubStock::firstOrNew([
                'product_id'          => $detail->product_id,
                'product_variable_id' => $detail->product_variable_id ?? 0,
            ]);
            $stock->qty = (int) $stock->qty + (int) $detail->qty;
            $stock->save();
        });

        if ($request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Item received at hub',
            ]);
        }

        Toastr::success('Item received at hub & added to hub stock', 'Success');
        return back();
    }

    /**
     * Current hub inventory.
     */
    public function stock(Request $request)
    {
        $query = HubStock::query()
            ->where('qty', '>', 0)
            ->with(['product.image', 'product.vendor', 'productVariable']);

        if ($request->keyword) {
            $query->whereHas('product', fn ($q) => $q->where('name', 'like', "%{$request->keyword}%"));
        }

        $stocks    = $query->latest('updated_at')->paginate(30)->withQueryString();
        $totalPics = HubStock::sum('qty');

        return view('backEnd.hub.stock', compact('stocks', 'totalPics'));
    }
}
