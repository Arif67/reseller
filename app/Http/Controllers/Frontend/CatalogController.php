<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\ProductCatalogService;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function __construct(private readonly ProductCatalogService $productCatalogService)
    {
    }

    public function hotdeals(Request $request)
    {
        return view('frontEnd.pages.hotdeals', $this->productCatalogService->getHotDealsData($request));
    }

    public function category(string $slug, Request $request)
    {
        return view('frontEnd.pages.category', $this->productCatalogService->getCategoryPageData($slug, $request));
    }

    public function subcategory(string $slug, Request $request)
    {
        return view('frontEnd.pages.subcategory', $this->productCatalogService->getSubcategoryPageData($slug, $request));
    }

    public function products(string $slug, Request $request)
    {
        return view('frontEnd.pages.childcategory', $this->productCatalogService->getChildCategoryPageData($slug, $request));
    }

    public function details(string $slug)
    {
        return view('frontEnd.pages.details', $this->productCatalogService->getProductDetailsData($slug));
    }

    public function search(Request $request)
    {
        return view('frontEnd.pages.search', $this->productCatalogService->getSearchPageData($request));
    }
}
