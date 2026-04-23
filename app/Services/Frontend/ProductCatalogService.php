<?php

namespace App\Services\Frontend;

use App\Models\Category;
use App\Models\Childcategory;
use App\Models\Contact;
use App\Models\CreatePage;
use App\Models\District;
use App\Models\Product;
use App\Models\Review;
use App\Models\ShippingCharge;
use App\Models\Subcategory;
use App\Services\AppService\ProductAttributeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ProductCatalogService
{
    public function __construct(
        private readonly ProductAttributeService $productAttributeService,
    ) {
    }

    public function getHotDealsData(Request $request): array
    {
        $query = $this->buildListingQuery([
            'status' => 1,
            'topsale' => 1,
        ], ['id', 'name', 'slug', 'new_price', 'old_price', 'type', 'category_id']);

        return $this->buildListingPageData($query, $request, 10, []);
    }

    public function getCategoryPageData(string $slug, Request $request): array
    {
        $category = Category::query()->where(['slug' => $slug, 'status' => 1])->firstOrFail();

        $query = $this->buildListingQuery([
            'status' => 1,
            'category_id' => $category->id,
        ], ['id', 'name', 'slug', 'new_price', 'old_price', 'type', 'category_id']);

        return $this->buildListingPageData($query, $request, 10, [
            'category' => $category,
            'subcategories' => Subcategory::query()->where('category_id', $category->id)->get(),
        ]);
    }

    public function getSubcategoryPageData(string $slug, Request $request): array
    {
        $subcategory = Subcategory::query()->where(['slug' => $slug, 'status' => 1])->firstOrFail();

        $query = $this->buildListingQuery([
            'status' => 1,
            'subcategory_id' => $subcategory->id,
        ], ['id', 'name', 'slug', 'new_price', 'old_price', 'type', 'subcategory_id']);

        return $this->buildListingPageData($query, $request, 10, [
            'subcategory' => $subcategory,
            'childcategories' => Childcategory::query()->where('subcategory_id', $subcategory->id)->get(),
        ]);
    }

    public function getChildCategoryPageData(string $slug, Request $request): array
    {
        $childcategory = Childcategory::query()->where(['slug' => $slug, 'status' => 1])->firstOrFail();

        $query = $this->buildListingQuery([
            'status' => 1,
            'childcategory_id' => $childcategory->id,
        ], ['id', 'name', 'slug', 'new_price', 'old_price', 'type', 'childcategory_id']);

        return $this->buildListingPageData($query, $request, 10, [
            'childcategory' => $childcategory,
            'childcategories' => Childcategory::query()->where('subcategory_id', $childcategory->subcategory_id)->get(),
        ]);
    }

    public function getProductDetailsData(string $slug): array
    {
        $details = Product::query()
            ->where(['slug' => $slug, 'status' => 1])
            ->with(['image', 'images', 'media', 'category', 'subcategory', 'childcategory', 'variables.selectedValues.attribute'])
            ->withCount('variable')
            ->firstOrFail();

        $reviews = Review::query()->where('product_id', $details->id)->get();
        $canReview = $details->hasCompletedPurchaseBy(Auth::guard('customer')->id());

        return [
            'details' => $details,
            'products' => Product::query()
                ->where(['category_id' => $details->category_id, 'status' => 1])
                ->with('image', 'images', 'media')
                ->select('id', 'name', 'description', 'slug', 'status', 'category_id', 'new_price', 'old_price', 'type', 'variation_pricing_mode')
                ->withCount('variable')
                ->limit(6)
                ->get(),
            'shippingcharge' => ShippingCharge::query()->where('status', 1)->get(),
            'reviews' => $reviews,
            'canReview' => $canReview,
            'averageRating' => $reviews->avg('ratting') ?? 0,
            'productAttributeGroups' => $this->productAttributeService->buildProductAttributeGroups($details),
            'contact' => Contact::query()->where('status', 1)->first(),
            'page' => CreatePage::query()->find(5),
        ];
    }

    public function checkStock(array $filters): array
    {
        $baseProduct = Product::query()
            ->select('id', 'type', 'purchase_price', 'old_price', 'new_price', 'variation_pricing_mode')
            ->find((int) $filters['id']);

        if (! $baseProduct) {
            return [
                'status' => false,
                'product' => null,
            ];
        }

        $product = $this->productAttributeService->findVariantForProduct(
            $baseProduct->id,
            $filters['value_ids'] ?? [],
            [
                'color' => $filters['color'] ?? null,
                'size' => $filters['size'] ?? null,
                'model' => $filters['model'] ?? null,
                'weight' => $filters['weight'] ?? null,
            ]
        );

        if ($product && (int) $baseProduct->type === 0 && $baseProduct->variation_pricing_mode === 'same') {
            $product->purchase_price = $baseProduct->purchase_price;
            $product->old_price = $baseProduct->old_price;
            $product->new_price = $baseProduct->new_price;
        }

        return [
            'status' => (bool) $product,
            'product' => $product,
        ];
    }

    public function getQuickViewData(int $productId): array
    {
        return [
            'data' => Product::query()
                ->where(['id' => $productId, 'status' => 1])
                ->with('images')
                ->withCount('reviews')
                ->first(),
        ];
    }

    public function getLiveSearchResults(Request $request): Collection|array
    {
        if (empty($request->category) && empty($request->keyword)) {
            return [];
        }

        return Product::query()
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'type', 'variation_pricing_mode')
            ->where('status', 1)
            ->withCount('variable')
            ->with('image', 'images', 'media')
            ->when($request->keyword, fn (Builder $query) => $query->where('name', 'like', '%' . $request->keyword . '%'))
            ->when($request->category, fn (Builder $query) => $query->where('category_id', $request->category))
            ->get();
    }

    public function getSearchPageData(Request $request): array
    {
        $products = Product::query()
            ->where('status', 1)
            ->with('image', 'images', 'media')
            ->select('id', 'name', 'slug', 'status', 'category_id', 'new_price', 'old_price', 'type', 'variation_pricing_mode')
            ->withCount('variable')
            ->when($request->keyword, fn (Builder $query) => $query->where('name', 'like', '%' . $request->keyword . '%'))
            ->when($request->category, fn (Builder $query) => $query->where('category_id', $request->category))
            ->paginate(10);

        return [
            'products' => $products,
            'keyword' => $request->keyword,
        ];
    }

    public function getDistrictAreas(string|int $districtId): Collection
    {
        return District::query()->where('district', $districtId)->pluck('area_name', 'id');
    }

    private function buildListingQuery(array $filters, array $select): Builder
    {
        if (! in_array('variation_pricing_mode', $select, true)) {
            $select[] = 'variation_pricing_mode';
        }

        return Product::query()
            ->where($filters)
            ->select($select)
            ->with('image', 'images', 'media', 'variable')
            ->withCount('variable');
    }

    private function buildListingPageData(Builder $query, Request $request, int $perPage, array $extra = []): array
    {
        $sortedQuery = $this->applySort($query, $request->sort);
        $minPrice = (clone $sortedQuery)->min('new_price');
        $maxPrice = (clone $sortedQuery)->max('new_price');

        $filteredQuery = $this->applyPriceFilter($sortedQuery, $request);

        return array_merge($extra, [
            'products' => $filteredQuery->paginate($perPage),
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
        ]);
    }

    private function applySort(Builder $query, mixed $sort): Builder
    {
        return match ((int) $sort) {
            1 => $query->orderBy('created_at', 'desc'),
            2 => $query->orderBy('created_at', 'asc'),
            3 => $query->orderBy('new_price', 'desc'),
            4 => $query->orderBy('new_price', 'asc'),
            5 => $query->orderBy('name', 'asc'),
            6 => $query->orderBy('name', 'desc'),
            default => $query->latest(),
        };
    }

    private function applyPriceFilter(Builder $query, Request $request): Builder
    {
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereBetween('new_price', [$request->min_price, $request->max_price]);
        }

        return $query;
    }
}
