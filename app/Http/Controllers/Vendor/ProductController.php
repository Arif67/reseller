<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariable;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Auth;

class ProductController extends Controller
{
    private function vendorId(): int
    {
        return Auth::guard('vendor')->id();
    }

    /**
     * Dummy product list (plain objects shaped like the Eloquent models)
     * so the vendor product page isn't empty before admin adds products.
     */
    private function dummyProducts(Request $request): LengthAwarePaginator
    {
        $catalogue = [
            ['name' => 'Premium Cotton Panjabi',  'new' => 1450, 'old' => 1800, 'stock' => 32, 'status' => 1],
            ['name' => 'Slim Fit Denim Jeans',    'new' => 1290, 'old' => 1600, 'stock' => 18, 'status' => 1],
            ['name' => 'Casual Cotton T-Shirt',   'new' => 550,  'old' => 750,  'stock' => 64, 'status' => 1],
            ['name' => 'Embroidered Three Piece', 'new' => 2350, 'old' => 2900, 'stock' => 12, 'status' => 1],
            ['name' => 'Formal Office Shirt',      'new' => 980,  'old' => 1200, 'stock' => 0,  'status' => 0],
            ['name' => 'Kids Party Frock',         'new' => 870,  'old' => 1100, 'stock' => 25, 'status' => 1],
            ['name' => 'Leather Casual Loafer',    'new' => 1990, 'old' => 2500, 'stock' => 9,  'status' => 1],
            ['name' => 'Winter Hoodie Jacket',     'new' => 1650, 'old' => 2100, 'stock' => 21, 'status' => 1],
        ];

        $items = collect($catalogue)->map(function ($item, $index) {
            $variables = collect([
                (object) [
                    'id'                        => 7000 + $index * 2,
                    'variant_label'             => 'Size M',
                    'stock'                     => (int) round($item['stock'] / 2),
                    'is_available_for_reseller' => true,
                    'vendor_status'             => 1,
                ],
                (object) [
                    'id'                        => 7001 + $index * 2,
                    'variant_label'             => 'Size L',
                    'stock'                     => (int) round($item['stock'] / 2),
                    'is_available_for_reseller' => $index % 2 === 0,
                    'vendor_status'             => $index % 2 === 0 ? 1 : 0,
                ],
            ]);

            return (object) [
                'id'           => 8000 + $index,
                'name'         => $item['name'],
                'image'        => null,
                'status'       => $item['status'],
                'new_price'    => $item['new'],
                'old_price'    => $item['old'],
                'stock'        => $item['stock'],
                'allVariables' => $variables,
            ];
        });

        return new LengthAwarePaginator(
            $items,
            $items->count(),
            20,
            1,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    // sudhu nijer product, na hole 404
    private function ownProduct($id): Product
    {
        return Product::where('id', $id)
            ->where('vendor_id', $this->vendorId())
            ->firstOrFail();
    }

    private function ownVariable($id): ProductVariable
    {
        return ProductVariable::where('id', $id)
            ->whereHas('product', fn ($query) => $query->where('vendor_id', $this->vendorId()))
            ->firstOrFail();
    }

    public function index(Request $request)
    {
        $query = Product::with(['image', 'allVariables.selectedValues.attribute'])
            ->where('vendor_id', $this->vendorId());

        if ($request->keyword) {
            $query->where('name', 'like', "%{$request->keyword}%");
        }

        $products = $query->latest()->paginate(20)->withQueryString();

        if ($products->isEmpty() && !$request->keyword) {
            $products = $this->dummyProducts($request);
        }

        return view('vendorPanel.products.index', compact('products'));
    }

    public function recentPost(Request $request)
    {
        $query = Product::with(['image', 'allVariables.selectedValues.attribute'])
            ->where('vendor_id', $this->vendorId());

        if ($request->keyword) {
            $query->where('name', 'like', "%{$request->keyword}%");
        }

        $products = $query->latest()->paginate(20)->withQueryString();

        if ($products->isEmpty() && !$request->keyword) {
            $products = $this->dummyProducts($request);
        }

        return view('vendorPanel.products.index', compact('products'));
    }

    // vendor stock update korte parbe
    public function updateStock(Request $request)
    {
        $this->validate($request, [
            'id'    => 'required|integer',
            'stock' => 'required|integer|min:0',
        ]);

        $product = $this->ownProduct($request->id);
        $product->stock = $request->stock;
        $product->save();

        Toastr::success('Stock updated', 'Success');
        return back();
    }

    // status on/off
    public function toggleStatus(Request $request)
    {
        $product = $this->ownProduct($request->id);
        $product->status = $product->status ? 0 : 1;
        $product->save();

        Toastr::success('Product ' . ($product->status ? 'activated' : 'deactivated'), 'Success');
        return back();
    }

    public function toggleVariable(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'vendor_status' => 'nullable|in:0,1',
        ]);

        $variable = $this->ownVariable($request->id);
        $variable->vendor_status = $request->has('vendor_status')
            ? (bool) $request->vendor_status
            : ! (bool) $variable->vendor_status;
        $variable->save();

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'vendor_status' => (bool) $variable->vendor_status,
                'available' => $variable->is_available_for_reseller,
                'message' => 'Variation ' . ($variable->vendor_status ? 'enabled' : 'stopped'),
            ]);
        }

        Toastr::success('Variation ' . ($variable->vendor_status ? 'enabled' : 'stopped'), 'Success');
        return back();
    }
}
