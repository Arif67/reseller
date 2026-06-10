<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariable;
use App\Models\ResellerFavourite;
use App\Services\AppService\ProductAttributeService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('status', 1)->orderBy('serial')->orderBy('name')->get();

        $products = $this->productQuery($request)->paginate(12)->withQueryString();

        $favouriteIds = $this->favouriteIds();

        // infinite scroll: AJAX hole sudhu card partial + pagination info
        if ($request->ajax()) {
            $html = view('resellerPanel.products._cards', compact('products', 'favouriteIds'))->render();
            return response()->json([
                'html'     => $html,
                'has_more' => $products->hasMorePages(),
                'next'     => $products->currentPage() + 1,
            ]);
        }

        $activeCategory = $request->category;

        return view('resellerPanel.products.index', compact('categories', 'products', 'activeCategory', 'favouriteIds'));
    }

    public function details($id, ProductAttributeService $attributeService)
    {
        $product = Product::with(['images', 'image', 'media', 'variables.selectedValues.attribute'])
            ->where('status', 1)
            ->findOrFail($id);

        $variables = $product->variables;

        // dynamic attribute groups (Size, Color, Material... ja-i thakuk)
        $attributeGroups = $attributeService->buildProductAttributeGroups($product);

        $inStock = $variables->count()
            ? $variables->sum('stock') > 0
            : $product->stock > 0;

        // ei product-er sob image (gallery + variation image)
        $images = $this->collectImages($product, $variables);

        $isFavourite = in_array($product->id, $this->favouriteIds(), true);

        return view('resellerPanel.products.details', compact('product', 'attributeGroups', 'inStock', 'images', 'isFavourite'));
    }

    // login kora reseller-er favourite product id gula
    private function favouriteIds(): array
    {
        return ResellerFavourite::where('reseller_id', Auth::guard('reseller')->id())
            ->pluck('product_id')
            ->all();
    }

    // ei product-er nijer image gula.
    // media = admin-er attach kora asol image (per-product correct).
    // media na thakle legacy productimages fallback.
    // variable image baad — oigula onno color/variant-er, inflate kore.
    private function collectImages(Product $product, $variables = null)
    {
        $images = $product->media->pluck('path')->filter();

        if ($images->isEmpty()) {
            $images = $product->images->pluck('image')->filter();
        }

        return $images->unique()->values();
    }

    // 1-click download — DB theke URL niye stream (SSRF safe)
    public function downloadImage($id, $index)
    {
        $product = Product::with(['images', 'media'])->findOrFail($id);
        $variables = ProductVariable::where('product_id', $product->id)->get();
        $images = $this->collectImages($product, $variables);

        $url = $images->get((int) $index);
        abort_if(!$url, 404);

        $absolute = \Illuminate\Support\Str::startsWith($url, ['http://', 'https://'])
            ? $url
            : asset($url);

        $contents = @file_get_contents($absolute);
        abort_if($contents === false, 404);

        $ext = pathinfo(parse_url($absolute, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
        $filename = \Illuminate\Support\Str::slug($product->name) . '-' . ($index + 1) . '.' . $ext;

        return response($contents)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function productQuery(Request $request)
    {
        // media + images load kori jate primary_media_image accessor N+1 chara kaj kore
        $query = Product::with(['media', 'images'])->where('status', 1);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('keyword')) {
            $query->where('name', 'like', "%{$request->keyword}%");
        }

        return $query->latest();
    }
}
