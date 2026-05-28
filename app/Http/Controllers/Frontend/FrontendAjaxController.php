<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Frontend\CampaignService;
use App\Services\Frontend\ProductCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FrontendAjaxController extends Controller
{
    public function __construct(
        private readonly ProductCatalogService $productCatalogService,
        private readonly CampaignService $campaignService,
    ) {
    }

    public function districts(Request $request): JsonResponse
    {
        return response()->json($this->productCatalogService->getDistrictAreas($request->id));
    }

    public function stockCheck(Request $request): JsonResponse
    {
        return response()->json($this->productCatalogService->checkStock([
            'id' => $request->id,
            'value_ids' => $request->input('value_ids', []),
            'color' => $request->color,
            'size' => $request->size,
            'model' => $request->model,
            'weight' => $request->weight,
        ]));
    }

    public function quickView(Request $request): Response|string
    {
        return view('frontEnd.layouts.ajax.quickview', $this->productCatalogService->getQuickViewData((int) $request->id))->render();
    }

    public function liveSearch(Request $request)
    {
        return view('frontEnd.layouts.ajax.search', [
            'products' => $this->productCatalogService->getLiveSearchResults($request),
        ]);
    }

    public function shippingCharge(Request $request)
    {
        $this->campaignService->setShippingCharge((int) $request->id);

        return view('frontEnd.layouts.ajax.cart');
    }

    public function campaignStock(Request $request): JsonResponse
    {
        return response()->json($this->campaignService->updateCampaignStock($request));
    }

    public function allProducts(Request $request): JsonResponse
    {
        $products = Product::query()
            ->where('status', 1)
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'type', 'variation_pricing_mode', 'stock')
            ->with('image', 'media', 'variable')
            ->withSum('allVariables as total_variable_stock', 'stock')
            ->withCount('variable')
            ->withAvg(['activeReviews as active_reviews_avg_ratting' => fn ($q) => $q], 'ratting')
            ->withCount(['activeReviews as active_reviews_count'])
            ->latest('id')
            ->paginate(20);

        $html = '';
        foreach ($products as $product) {
            $html .= view('frontEnd.partials.product-card', compact('product'))->render();
        }

        return response()->json([
            'html'     => $html,
            'has_more' => $products->hasMorePages(),
            'next'     => $products->currentPage() + 1,
        ]);
    }
}
