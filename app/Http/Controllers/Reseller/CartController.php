<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariable;
use App\Models\ResellerCart;
use App\Services\AppService\ProductAttributeService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Auth;

class CartController extends Controller
{
    public function __construct(private ProductAttributeService $attributeService)
    {
    }

    private function resellerId(): int
    {
        return Auth::guard('reseller')->id();
    }

    public function index()
    {
        $items = ResellerCart::where('reseller_id', $this->resellerId())->latest()->get();

        $totalSell   = $items->sum('subtotal');
        $totalMargin = $items->sum('margin');

        // cart-er protita product-er dynamic attribute groups (variant change korar jonno)
        $productIds = $items->pluck('product_id')->unique();
        $products = Product::with('variables.selectedValues.attribute')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $variantOptions = $products->map(fn ($p) => $this->attributeService->buildProductAttributeGroups($p));

        return view('resellerPanel.cart.index', compact('items', 'totalSell', 'totalMargin', 'variantOptions'));
    }

    public function add(Request $request)
    {
        $this->validate($request, [
            'product_id'       => 'required|integer',
            'qty'              => 'required|integer|min:1',
            'amt'              => 'required|numeric|min:0',
            'attribute_values' => 'nullable|array',
        ]);

        $product = Product::with('media', 'image')->where('status', 1)->findOrFail($request->product_id);

        // dynamic attribute resolve (frontend cart-er same logic)
        $selectedValueIds   = $this->attributeService->sanitizeSelectedValueIds($request->input('attribute_values', []));
        $variant            = $this->attributeService->findVariantForProduct($product->id, $selectedValueIds);
        $resolvedSelections = $this->attributeService->resolveLegacySelectionsFromValueIds($selectedValueIds);
        $selectedAttributes = $this->attributeService->summarizeSelections($selectedValueIds, $resolvedSelections);

        if (! empty($selectedValueIds) && ! $variant) {
            Toastr::error('Ei variation currently unavailable', 'Unavailable');
            return back();
        }

        if ($variant && (int) $variant->stock < (int) $request->qty) {
            Toastr::error('Ei variation er stock enough nei', 'Stock limit');
            return back();
        }

        $image = $variant?->primary_media_image ?? $product->primary_media_image ?? ($product->image->image ?? null);

        // ekই product + ekই attribute combination thakle qty barabo
        $sortedIds = $selectedValueIds;
        sort($sortedIds);
        $existing = ResellerCart::where('reseller_id', $this->resellerId())
            ->where('product_id', $product->id)
            ->get()
            ->first(function ($row) use ($sortedIds) {
                $rowIds = (array) $row->selected_value_ids;
                sort($rowIds);
                return $rowIds == $sortedIds;
            });

        if ($existing) {
            $existing->qty += (int) $request->qty;
            $existing->sell_price = (float) $request->amt;
            $existing->save();
        } else {
            ResellerCart::create([
                'reseller_id'         => $this->resellerId(),
                'product_id'          => $product->id,
                'product_variable_id' => $variant?->id,
                'product_name'        => $product->name,
                'image'               => $image,
                'size'                => $resolvedSelections['size'] ?? null,
                'color'               => $resolvedSelections['color'] ?? null,
                'selected_attributes' => $selectedAttributes,
                'selected_value_ids'  => $selectedValueIds,
                'qty'                 => (int) $request->qty,
                'wholesale_price'     => (float) $product->wholesale_price,
                'sell_price'          => (float) $request->amt,
            ]);
        }

        Toastr::success('Cart e add holo', 'Success');
        return redirect()->route('reseller.cart.index');
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id'               => 'required|integer',
            'qty'              => 'required|integer|min:1',
            'sell_price'       => 'required|numeric|min:0',
            'attribute_values' => 'nullable|array',
        ]);

        $item = ResellerCart::where('reseller_id', $this->resellerId())->findOrFail($request->id);
        $item->qty        = (int) $request->qty;
        $item->sell_price = (float) $request->sell_price;

        // dynamic variant change — attribute_values request-e thakle re-resolve kori
        if ($request->has('attribute_values')) {
            $selectedValueIds   = $this->attributeService->sanitizeSelectedValueIds($request->input('attribute_values', []));
            $variant            = $this->attributeService->findVariantForProduct($item->product_id, $selectedValueIds);
            $resolvedSelections = $this->attributeService->resolveLegacySelectionsFromValueIds($selectedValueIds);
            $selectedAttributes = $this->attributeService->summarizeSelections($selectedValueIds, $resolvedSelections);

            if (! empty($selectedValueIds) && ! $variant) {
                return $this->unavailableResponse($request, 'Ei variation currently unavailable');
            }

            $item->product_variable_id = $variant?->id;
            $item->selected_value_ids  = $selectedValueIds;
            $item->selected_attributes = $selectedAttributes;
            $item->size  = $resolvedSelections['size'] ?? null;
            $item->color = $resolvedSelections['color'] ?? null;
        }

        if ($item->product_variable_id) {
            $variant = ProductVariable::availableForReseller()->find($item->product_variable_id);
            if (! $variant) {
                return $this->unavailableResponse($request, 'Ei variation currently unavailable');
            }

            if ((int) $variant->stock < (int) $item->qty) {
                return $this->unavailableResponse($request, 'Ei variation er stock enough nei');
            }
        }

        $item->save();

        // real-time AJAX response — notun value gula firiye dei
        if ($request->ajax()) {
            $items = ResellerCart::where('reseller_id', $this->resellerId())->get();
            return response()->json([
                'qty'          => $item->qty,
                'margin'       => round($item->margin),
                'subtotal'     => round($item->subtotal),
                'variant'      => $item->variant_label,
                'total_margin' => round($items->sum('margin')),
                'total_sell'   => round($items->sum('subtotal')),
            ]);
        }

        Toastr::success('Cart updated', 'Success');
        return back();
    }

    private function unavailableResponse(Request $request, string $message)
    {
        if ($request->ajax()) {
            return response()->json(['message' => $message], 422);
        }

        Toastr::error($message, 'Unavailable');
        return back();
    }

    public function remove(Request $request)
    {
        $item = ResellerCart::where('reseller_id', $this->resellerId())->findOrFail($request->id);
        $item->delete();

        Toastr::success('Item removed', 'Success');
        return back();
    }

    public function clear()
    {
        ResellerCart::where('reseller_id', $this->resellerId())->delete();

        Toastr::success('Cart clear kora holo', 'Success');
        return back();
    }
}
