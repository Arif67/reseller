<?php

namespace App\Services\Frontend;

use App\Models\Campaign;
use App\Models\Product;
use App\Models\ProductVariable;
use App\Models\ShippingCharge;
use App\Services\AppService\ProductAttributeService;
use Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CampaignService
{
    public function __construct(
        private readonly ProductAttributeService $productAttributeService,
    ) {
    }

    public function getCampaignPageData(string $slug, Request $request): array
    {
        $campaign = Campaign::query()->where('slug', $slug)->with('images')->firstOrFail();

        $product = Product::query()
            ->with('image')
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'purchase_price', 'type', 'variation_pricing_mode', 'stock')
            ->where('id', $campaign->product_id)
            ->firstOrFail();

        $productcolors = $this->getDistinctOptions($campaign->product_id, 'color');
        $productsizes = $this->getDistinctOptions($campaign->product_id, 'size');

        Cart::instance('shopping')->destroy();

        $variableProduct = ProductVariable::query()->where('product_id', $campaign->product_id)->first();
        $pricing = $this->resolveProductPricing($product, $variableProduct);

        $this->addCampaignProductToCart($product, $pricing, $request->product_size, $request->product_color, $variableProduct);

        $shippingcharge = ShippingCharge::query()->where('status', 1)->get();
        $selectedCharge = ShippingCharge::query()->where('status', 1)->first();

        if ($selectedCharge) {
            Session::put('shipping', $selectedCharge->amount);
        }

        return [
            'campaign' => $campaign,
            'productsizes' => $productsizes,
            'productcolors' => $productcolors,
            'shippingcharge' => $shippingcharge,
            'old_price' => $pricing['old_price'],
            'new_price' => $pricing['new_price'],
        ];
    }

    public function updateCampaignStock(Request $request): array
    {
        $product = Product::query()
            ->with('image')
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'purchase_price', 'type', 'variation_pricing_mode', 'stock')
            ->where('id', $request->id)
            ->firstOrFail();

        $variable = $this->productAttributeService->findVariantForProduct($request->id, [], [
            'color' => $request->color,
            'size' => $request->size,
        ]);

        if (! $variable) {
            return [
                'status' => false,
                'product' => null,
            ];
        }

        $pricing = $this->resolveProductPricing($product, $variable);

        Cart::instance('shopping')->destroy();
        $this->addCampaignProductToCart($product, $pricing, $request->size, $request->color, $variable);

        return [
            'status' => true,
            'product' => [
                'stock' => $pricing['stock'],
                'old_price' => $pricing['old_price'],
                'new_price' => $pricing['new_price'],
            ],
        ];
    }

    public function setShippingCharge(int $shippingChargeId): void
    {
        $shipping = ShippingCharge::query()->findOrFail($shippingChargeId);
        Session::put('shipping', $shipping->amount);
    }

    private function getDistinctOptions(int $productId, string $column)
    {
        return ProductVariable::query()
            ->where('product_id', $productId)
            ->where('stock', '>', 0)
            ->whereNotNull($column)
            ->select($column)
            ->distinct()
            ->get();
    }

    private function resolveProductPricing(Product $product, ?ProductVariable $variableProduct): array
    {
        if ((int) $product->type === 0) {
            if ($product->variation_pricing_mode === 'same') {
                return [
                    'purchase_price' => $product->purchase_price,
                    'old_price' => $product->old_price,
                    'new_price' => $product->new_price,
                    'stock' => $variableProduct?->stock ?? 0,
                ];
            }

            return [
                'purchase_price' => $variableProduct?->purchase_price ?? 0,
                'old_price' => $variableProduct?->old_price ?? 0,
                'new_price' => $variableProduct?->new_price ?? 0,
                'stock' => $variableProduct?->stock ?? 0,
            ];
        }

        return [
            'purchase_price' => $product->purchase_price,
            'old_price' => $product->old_price,
            'new_price' => $product->new_price,
            'stock' => $product->stock,
        ];
    }

    private function addCampaignProductToCart(Product $product, array $pricing, ?string $size, ?string $color, ?ProductVariable $variableProduct = null): void
    {
        Cart::instance('shopping')->add([
            'id' => $product->id,
            'name' => $product->name,
            'qty' => 1,
            'price' => $pricing['new_price'],
            'options' => [
                'slug' => $product->slug,
                'image' => $variableProduct?->primary_media_image ?? $product->primary_media_image ?? optional($product->image)->image ?? 'uploads/logo.png',
                'old_price' => $pricing['old_price'],
                'purchase_price' => $pricing['purchase_price'],
                'product_size' => $size,
                'product_color' => $color,
                'product_variable_id' => $variableProduct?->id,
                'type' => $product->type,
            ],
        ]);
    }
}
