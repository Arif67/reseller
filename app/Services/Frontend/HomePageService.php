<?php

namespace App\Services\Frontend;

use App\Models\Banner;
use App\Models\Category;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class HomePageService
{
    private const CACHE_TTL = 3600;
    private const HOME_CATEGORY_PRODUCT_LIMIT = 12;
    private const TOP_SALE_LIMIT = 12;
    private const NEW_ARRIVAL_LIMIT = 14;
    private const BEST_SELLING_LIMIT = 8;
    private const FEATURED_LIMIT = 12;

    public function getHomePageData(): array
    {
        return [
            'newArrival' => $this->getNewArrivalProducts(),
            'sliders' => $this->getSliderBanners(),
            'frontcategory' => $this->getFrontCategories(),
            'hotdeal_top' => $this->getTopSaleProducts(),
            'featuredProducts' => $this->getFeaturedProducts(),
            'bestSellingProducts' => $this->getBestSellingProducts(),
            'recentlyViewedProducts' => $this->getRecentlyViewedProducts(),
            'homecategory' => $this->getHomeCategories(),
            'marketing_banner' => $this->getMarketingBanners(),
        ];
    }

    public function getStorePageData(): array
    {
        return [
            'data' => Cache::remember('store_page_data_v1', self::CACHE_TTL, fn () => Store::query()->get()),
        ];
    }

    private function getFrontCategories(): Collection
    {
        return Cache::remember('home_front_categories_v1', self::CACHE_TTL, function () {
            return Category::query()
                ->where('status', 1)
                ->select('id', 'name', 'image', 'slug', 'icon')
                ->get();
        });
    }

    private function getSliderBanners(): Collection
    {
        return Cache::remember('home_slider_banners_v1', self::CACHE_TTL, function () {
            return Banner::query()
                ->where(['status' => 1, 'category_id' => 1])
                ->select('id', 'image', 'link')
                ->get();
        });
    }

    private function getMarketingBanners(): Collection
    {
        return Cache::remember('home_marketing_banners_v1', self::CACHE_TTL, function () {
            return Banner::query()
                ->where(['status' => 1, 'category_id' => 2])
                ->select('id', 'image', 'link')
                ->get();
        });
    }

    private function getTopSaleProducts(): Collection
    {
        return Cache::remember('home_top_sale_products_v1', self::CACHE_TTL, function () {
            return $this->baseProductCardQuery()
                ->where(['status' => 1, 'topsale' => 1])
                ->latest('id')
                ->limit(self::TOP_SALE_LIMIT)
                ->get();
        });
    }

    private function getFeaturedProducts(): Collection
    {
        return Cache::remember('home_featured_products_v1', self::CACHE_TTL, function () {
            return $this->baseProductCardQuery()
                ->where(['status' => 1, 'feature_product' => 1])
                ->latest('id')
                ->limit(self::FEATURED_LIMIT)
                ->get();
        });
    }

    private function getNewArrivalProducts(): Collection
    {
        return Cache::remember('home_new_arrival_products_v1', self::CACHE_TTL, function () {
            return $this->baseProductCardQuery()
                ->where('status', 1)
                ->latest('id')
                ->limit(self::NEW_ARRIVAL_LIMIT)
                ->get();
        });
    }

    private function getBestSellingProducts(): Collection
    {
        return Cache::remember('home_best_selling_products_v1', self::CACHE_TTL, function () {
            $bestSellingStats = OrderDetails::query()
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->where('orders.order_status', 6)
                ->whereNotNull('order_details.product_id')
                ->select(
                    'order_details.product_id',
                    DB::raw('SUM(order_details.qty) as sold_quantity'),
                    DB::raw('SUM(order_details.qty * order_details.sale_price) as sold_amount')
                )
                ->groupBy('order_details.product_id')
                ->orderByDesc('sold_quantity')
                ->limit(self::BEST_SELLING_LIMIT)
                ->get();

            if ($bestSellingStats->isEmpty()) {
                return $this->getFallbackBestSellingProducts();
            }

            $productMap = $this->baseProductCardQuery()
                ->where('status', 1)
                ->whereIn('id', $bestSellingStats->pluck('product_id'))
                ->get()
                ->keyBy('id');

            $products = $bestSellingStats->map(function ($stat) use ($productMap) {
                $product = $productMap->get($stat->product_id);

                if (! $product) {
                    return null;
                }

                $product->setAttribute('sold_quantity', (int) $stat->sold_quantity);
                $product->setAttribute('sold_amount', (float) $stat->sold_amount);

                return $product;
            })->filter()->values();

            return $products->isNotEmpty() ? $products : $this->getFallbackBestSellingProducts();
        });
    }

    private function getFallbackBestSellingProducts(): Collection
    {
        return $this->baseProductCardQuery()
            ->where('status', 1)
            ->latest('id')
            ->limit(self::BEST_SELLING_LIMIT)
            ->get()
            ->each(function ($product): void {
                $product->setAttribute('sold_quantity', 0);
                $product->setAttribute('sold_amount', 0);
            });
    }

    private function getRecentlyViewedProducts(): Collection
    {
        $recentlyViewedIds = collect(Session::get('recently_viewed_products', []))
            ->map(fn ($id) => (int) $id)
            ->reject(fn ($id) => $id <= 0)
            ->values();

        if ($recentlyViewedIds->isEmpty()) {
            return collect();
        }

        $products = $this->baseProductCardQuery()
            ->where('status', 1)
            ->whereIn('id', $recentlyViewedIds)
            ->get()
            ->keyBy('id');

        return $recentlyViewedIds
            ->map(fn ($id) => $products->get($id))
            ->filter()
            ->values();
    }

    private function getHomeCategories(): Collection
    {
        $productLimit = $this->getHomeCategoryProductLimit();
        $cacheKey = 'home_categories_v3_' . $productLimit;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($productLimit) {
            $categories = Category::query()
                ->where(['front_view' => 1, 'status' => 1])
                ->whereHas('products', function ($query): void {
                    $query->where('status', 1);
                })
                ->select('id', 'name', 'slug', 'image', 'banner_image')
                ->orderBy('id')
                ->get();

            $categories->each(function (Category $category) use ($productLimit): void {
                $products = Product::query()
                    ->select('id', 'name', 'slug', 'category_id', 'new_price', 'old_price', 'description', 'type', 'variation_pricing_mode', 'stock')
                    ->where('status', 1)
                    ->where('category_id', $category->id)
                    ->latest('id')
                    ->with('image', 'images', 'media', 'variable')
                    ->withSum('allVariables as total_variable_stock', 'stock')
                    ->withCount('variable')
                    ->limit($productLimit)
                    ->get();

                $category->setRelation('products', $products);
            });

            return $categories;
        });
    }

    private function getHomeCategoryProductLimit(): int
    {
        return self::HOME_CATEGORY_PRODUCT_LIMIT;
    }

    private function baseProductCardQuery(): Builder
    {
        return Product::query()
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'type', 'variation_pricing_mode', 'stock')
            ->with('image', 'images', 'media', 'variable')
            ->withSum('allVariables as total_variable_stock', 'stock')
            ->withCount('variable');
    }
}
