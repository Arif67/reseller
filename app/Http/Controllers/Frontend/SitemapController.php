<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Childcategory;
use App\Models\CreatePage;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = Cache::remember('frontend_sitemap_urls_v1', 3600, function () {
            $urls = [
                [
                    'loc' => route('home'),
                    'lastmod' => now()->toDateString(),
                    'changefreq' => 'daily',
                    'priority' => '1.0',
                ],
                [
                    'loc' => route('storepage'),
                    'lastmod' => now()->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ],
                [
                    'loc' => route('offers'),
                    'lastmod' => now()->toDateString(),
                    'changefreq' => 'daily',
                    'priority' => '0.8',
                ],
                [
                    'loc' => route('hotdeals'),
                    'lastmod' => now()->toDateString(),
                    'changefreq' => 'daily',
                    'priority' => '0.8',
                ],
            ];

            $categoryQuery = Category::query()->select('slug', 'updated_at');
            if (Schema::hasColumn('categories', 'status')) {
                $categoryQuery->where('status', 1);
            }

            foreach ($categoryQuery->get() as $category) {
                $urls[] = [
                    'loc' => route('category', $category->slug),
                    'lastmod' => optional($category->updated_at)->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            }

            $subcategoryQuery = Subcategory::query()->select('slug', 'updated_at');
            if (Schema::hasColumn('subcategories', 'status')) {
                $subcategoryQuery->where('status', 1);
            }

            foreach ($subcategoryQuery->get() as $subcategory) {
                $urls[] = [
                    'loc' => route('subcategory', $subcategory->slug),
                    'lastmod' => optional($subcategory->updated_at)->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }

            $childCategoryQuery = Childcategory::query()->select('slug', 'updated_at');
            if (Schema::hasColumn('childcategories', 'status')) {
                $childCategoryQuery->where('status', 1);
            }

            foreach ($childCategoryQuery->get() as $childcategory) {
                $urls[] = [
                    'loc' => route('products', $childcategory->slug),
                    'lastmod' => optional($childcategory->updated_at)->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }

            $productQuery = Product::query()->select('slug', 'updated_at');
            if (Schema::hasColumn('products', 'status')) {
                $productQuery->where('status', 1);
            }

            foreach ($productQuery->get() as $product) {
                $urls[] = [
                    'loc' => route('product', $product->slug),
                    'lastmod' => optional($product->updated_at)->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.9',
                ];
            }

            $pageQuery = CreatePage::query()->select('slug', 'updated_at');
            if (Schema::hasColumn('create_pages', 'status')) {
                $pageQuery->where('status', 1);
            }

            foreach ($pageQuery->get() as $page) {
                $urls[] = [
                    'loc' => route('page', $page->slug),
                    'lastmod' => optional($page->updated_at)->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            }

            return $urls;
        });

        return response()
            ->view('frontEnd.seo.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
