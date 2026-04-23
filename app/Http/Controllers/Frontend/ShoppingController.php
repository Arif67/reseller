<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\AppService\ProductAttributeService;
use Cart;
use DB;
use Illuminate\Http\Request;
use Session;
use Toastr;

class ShoppingController extends Controller
{
    public function __construct(
        private readonly ProductAttributeService $productAttributeService,
    ) {
    }

    public function addTocartGet($id, Request $request)
    {
        return 'ok';
    }

    public function cart_store(Request $request)
    {
        $product = Product::with('media', 'image')->select('id', 'name', 'slug', 'new_price', 'old_price', 'purchase_price', 'type', 'variation_pricing_mode', 'free_shipping', 'stock')
            ->where('id', $request->id)
            ->firstOrFail();

        $selectedValueIds = $this->productAttributeService->sanitizeSelectedValueIds($request->input('attribute_values', []));
        $legacySelections = [
            'color' => $request->product_color,
            'size' => $request->product_size,
            'model' => $request->product_model,
            'weight' => $request->product_weight,
        ];

        $varProduct = $this->productAttributeService->findVariantForProduct($product->id, $selectedValueIds, $legacySelections);
        $resolvedSelections = $this->productAttributeService->resolveLegacySelectionsFromValueIds($selectedValueIds, $legacySelections);
        $selectedAttributes = $this->productAttributeService->summarizeSelections($selectedValueIds, $resolvedSelections);

        if ((int) $product->type === 0) {
            $purchasePrice = $product->variation_pricing_mode === 'same' ? $product->purchase_price : ($varProduct?->purchase_price ?? 0);
            $oldPrice = $product->variation_pricing_mode === 'same' ? $product->old_price : ($varProduct?->old_price ?? 0);
            $newPrice = $product->variation_pricing_mode === 'same' ? $product->new_price : ($varProduct?->new_price ?? 0);
            $stock = $varProduct?->stock ?? 0;
        } else {
            $purchasePrice = $product->purchase_price;
            $oldPrice = $product->old_price;
            $newPrice = $product->new_price;
            $stock = $product->stock;
        }

        $cartItem = Cart::instance('shopping')->content()->where('id', $product->id)->first();
        $cartQty = $cartItem ? $cartItem->qty + $request->qty : $request->qty;

        if ($stock < $cartQty) {
            Toastr::error('Product stock limit over', 'Failed!');
            return back();
        }

        if (Cart::instance('shopping')->count() == 0) {
            Session::forget('free_shipping');
        }

        if (Session::has('free_shipping')) {
            if ($product->free_shipping != Session::get('free_shipping')) {
                $proType = Session::get('free_shipping') == 1 ? 'Digital' : 'Goods';
                Toastr::error('You added ' . $proType . ' product, please try another type product', 'Failed!');
                return back();
            }
        } else {
            Session::put('free_shipping', $product->free_shipping);
        }

        Cart::instance('shopping')->add([
            'id' => $product->id,
            'name' => $product->name,
            'qty' => $request->qty,
            'price' => $newPrice,
            'options' => [
                'slug' => $product->slug,
                'image' => $varProduct?->primary_media_image ?? $product->primary_media_image ?? optional($product->image)->image ?? 'uploads/logo.png',
                'old_price' => $oldPrice,
                'purchase_price' => $purchasePrice,
                'product_size' => $resolvedSelections['size'],
                'product_color' => $resolvedSelections['color'],
                'product_model' => $resolvedSelections['model'],
                'product_weight' => $resolvedSelections['weight'],
                'product_variable_id' => $varProduct?->id,
                'selected_attributes' => $selectedAttributes,
                'selected_value_ids' => $selectedValueIds,
                'type' => $product->type,
                'free_shipping' => $product->free_shipping ?? 0,
            ],
        ]);

        Toastr::success('Product successfully added to cart', 'Success!');

        if ($request->order_now) {
            return redirect()->route('customer.checkout');
        }

        return back();
    }

    public function campaign_stock(Request $request)
    {
        return response()->json([
            'status' => false,
            'product' => null,
        ]);
    }

    public function cart_content(Request $request)
    {
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }

    public function cart_remove(Request $request)
    {
        Cart::instance('shopping')->update($request->id, 0);
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }

    public function cart_increment(Request $request)
    {
        $item = Cart::instance('shopping')->get($request->id);
        $qty = $item->qty + 1;
        Cart::instance('shopping')->update($request->id, $qty);
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }

    public function cart_decrement(Request $request)
    {
        $item = Cart::instance('shopping')->get($request->id);
        $qty = $item->qty - 1;
        Cart::instance('shopping')->update($request->id, $qty);
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }

    public function cart_count(Request $request)
    {
        $data = Cart::instance('shopping')->count();
        return view('frontEnd.layouts.ajax.cart_count', compact('data'));
    }

    public function mobilecart_qty(Request $request)
    {
        $data = Cart::instance('shopping')->count();
        return view('frontEnd.layouts.ajax.mobilecart_qty', compact('data'));
    }

    public function cart_remove_bn(Request $request)
    {
        Cart::instance('shopping')->update($request->id, 0);
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart_bn', compact('data'));
    }

    public function cart_increment_bn(Request $request)
    {
        $item = Cart::instance('shopping')->get($request->id);
        $qty = $item->qty + 1;
        Cart::instance('shopping')->update($request->id, $qty);
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart_bn', compact('data'));
    }

    public function cart_decrement_bn(Request $request)
    {
        $item = Cart::instance('shopping')->get($request->id);
        $qty = $item->qty - 1;
        Cart::instance('shopping')->update($request->id, $qty);
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart_bn', compact('data'));
    }
}
