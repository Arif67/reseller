<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ResellerFavourite;
use Illuminate\Http\Request;
use Auth;

class FavouriteController extends Controller
{
    private function resellerId(): int
    {
        return Auth::guard('reseller')->id();
    }

    // Favourite list page
    public function index()
    {
        $favProductIds = ResellerFavourite::where('reseller_id', $this->resellerId())
            ->latest()
            ->pluck('product_id');

        $products = Product::with('media', 'image')
            ->where('status', 1)
            ->whereIn('id', $favProductIds)
            ->get()
            // favourite add korar order onujayi sajai
            ->sortBy(fn ($p) => $favProductIds->search($p->id))
            ->values();

        // partial-er heart filled dekhanor jonno
        $favouriteIds = $favProductIds->all();

        return view('resellerPanel.favourites.index', compact('products', 'favouriteIds'));
    }

    // Add/remove toggle — AJAX
    public function toggle(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|integer',
        ]);

        // product ache kina niশ্চিত kori
        Product::where('status', 1)->findOrFail($request->product_id);

        $existing = ResellerFavourite::where('reseller_id', $this->resellerId())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            $existing->delete();
            $favourited = false;
        } else {
            ResellerFavourite::create([
                'reseller_id' => $this->resellerId(),
                'product_id'  => (int) $request->product_id,
            ]);
            $favourited = true;
        }

        $count = ResellerFavourite::where('reseller_id', $this->resellerId())->count();

        if ($request->ajax()) {
            return response()->json([
                'favourited' => $favourited,
                'count'      => $count,
            ]);
        }

        return back();
    }
}
