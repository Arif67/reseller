<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
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
}
