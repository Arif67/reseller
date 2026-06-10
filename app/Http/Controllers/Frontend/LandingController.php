<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Toastr;

/**
 * Public marketing landing page for the reseller / dropshipping platform.
 *
 * NOTE: This replaces the old customer-facing e-commerce storefront home page.
 * The storefront routes/controllers (HomeController@index, cart, checkout, etc.)
 * are commented out in routes/web.php — keep them around for reference only.
 *
 * Performance: the nav-menu pages are fully static marketing pages, so their
 * rendered HTML is cached (see CACHED_PAGES). Cache is flushed via flushCache()
 * whenever the theme/hero or general settings change. The contact page is NOT
 * cached because it contains a per-session CSRF form.
 */
class LandingController extends Controller
{
    /**
     * Static nav-menu pages that are safe to full-page cache: key => view.
     * NOTE: the products page is intentionally excluded — it is a dynamic
     * category/subcategory product browser driven by query params.
     */
    private const CACHED_PAGES = [
        'home'     => 'landing.home',
        'about'    => 'landing.about',
        'services' => 'landing.services',
        'how'      => 'landing.how',
    ];

    /** How long a rendered page stays cached (minutes). */
    private const TTL_MINUTES = 360;

    public function index()
    {
        return $this->renderCached('home');
    }

    public function about()
    {
        return $this->renderCached('about');
    }

    public function services()
    {
        return $this->renderCached('services');
    }

    /**
     * Public category/subcategory product browser.
     * Categories on top, subcategories of the selected category below, and the
     * matching products in a grid. Driven by ?category= and ?subcategory= slugs.
     */
    public function products(Request $request)
    {
        $categories = Category::query()
            ->where('status', 1)
            ->with(['menusubcategories' => fn ($q) => $q->where('status', 1)])
            ->orderBy('serial')
            ->orderBy('id')
            ->get(['id', 'name', 'slug', 'image', 'status', 'serial']);

        $selectedSubcategory = $request->filled('subcategory')
            ? Subcategory::where('status', 1)->where('slug', $request->query('subcategory'))->first()
            : null;

        $selectedCategory = null;
        if ($selectedSubcategory) {
            $selectedCategory = $categories->firstWhere('id', $selectedSubcategory->category_id);
        } elseif ($request->filled('category')) {
            $selectedCategory = $categories->firstWhere('slug', $request->query('category'));
        }

        // Default view: first category that actually has products (fallback: first).
        if (! $selectedCategory && ! $selectedSubcategory) {
            $selectedCategory = $categories->first(
                fn ($c) => Product::where('status', 1)->where('category_id', $c->id)->exists()
            ) ?? $categories->first();
        }

        $products = Product::query()
            ->where('status', 1)
            ->when($selectedSubcategory, fn ($q) => $q->where('subcategory_id', $selectedSubcategory->id))
            ->when(! $selectedSubcategory && $selectedCategory, fn ($q) => $q->where('category_id', $selectedCategory->id))
            ->with(['media', 'images', 'image'])
            ->latest('id')
            ->limit(48)
            ->get();

        $subcategories = $selectedCategory
            ? $categories->firstWhere('id', $selectedCategory->id)?->menusubcategories ?? collect()
            : collect();

        return view('landing.products', compact(
            'categories',
            'subcategories',
            'selectedCategory',
            'selectedSubcategory',
            'products'
        ));
    }

    public function howItWorks()
    {
        return $this->renderCached('how');
    }

    /**
     * Public product details page — product info + related products.
     * No cart/checkout here (ordering happens after reseller registration).
     */
    public function productShow(string $slug)
    {
        $product = Product::query()
            ->where('status', 1)
            ->where('slug', $slug)
            ->with(['media', 'images', 'image', 'category', 'subcategory', 'brand'])
            ->firstOrFail();

        $related = Product::query()
            ->where('status', 1)
            ->where('id', '!=', $product->id)
            ->when($product->subcategory_id, fn ($q) => $q->where('subcategory_id', $product->subcategory_id))
            ->when(! $product->subcategory_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->with(['media', 'images', 'image'])
            ->latest('id')
            ->limit(8)
            ->get();

        return view('landing.product', compact('product', 'related'));
    }

    public function contact()
    {
        // Not cached: contains a CSRF-protected contact form.
        return view('landing.contact');
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:50',
            'email'   => 'nullable|email|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // TODO: persist/notify as needed. For now just acknowledge the sender.
        Toastr::success('আপনার মেসেজ পাঠানো হয়েছে। আমরা শীঘ্রই যোগাযোগ করব।', 'ধন্যবাদ');

        return redirect()->route('landing.contact');
    }

    /**
     * Serve a static landing page from cache, rendering once on a miss.
     */
    private function renderCached(string $key)
    {
        $html = Cache::remember(
            self::cacheKey($key),
            now()->addMinutes(self::TTL_MINUTES),
            fn () => view(self::CACHED_PAGES[$key])->render()
        );

        return response($html);
    }

    private static function cacheKey(string $key): string
    {
        return "landing_page_{$key}_v1";
    }

    /**
     * Clear all cached landing pages. Call after any admin change that affects
     * the landing header/footer/hero (theme customization, general settings...).
     */
    public static function flushCache(): void
    {
        foreach (array_keys(self::CACHED_PAGES) as $key) {
            Cache::forget(self::cacheKey($key));
        }
    }
}
