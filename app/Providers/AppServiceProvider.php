<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\GeneralSetting;
use App\Models\Category;
use App\Models\Brand;
use App\Models\SocialMedia;
use App\Models\Contact;
use App\Models\CreatePage;
use App\Models\OrderStatus;
use App\Models\EcomPixel;
use App\Models\GoogleTagManager;
use App\Models\MarketingToolConfig;
use App\Models\Order;
use App\Models\PaymentGateway;
use Config;
use Session;
use App\Models\Topheader;
use App\Models\ThemeCustomization;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        //Cache shurjopay credentials separately (config set, not shared in view)
        $shurjopay = Cache::remember('payment_shurjopay', 3600, function () {
            return PaymentGateway::where(['status' => 1, 'type' => 'shurjopay'])->first();
        });

        if ($shurjopay) {
            Config::set('shurjopay.apiCredentials.username', $shurjopay->username);
            Config::set('shurjopay.apiCredentials.password', $shurjopay->password);
            Config::set('shurjopay.apiCredentials.prefix', $shurjopay->prefix);
            Config::set('shurjopay.apiCredentials.return_url', $shurjopay->success_url);
            Config::set('shurjopay.apiCredentials.cancel_url', $shurjopay->return_url);
            Config::set('shurjopay.apiCredentials.base_url', $shurjopay->base_url);
        }

        // Group all shared frontend data into one cached array
        $sharedData = Cache::remember('shared_view_data_v4', 3600, function () {
            $categorySelect = ['id', 'name', 'slug', 'status', 'image', 'serial'];

            return [
                'generalsetting' => GeneralSetting::where('status', 1)->first(),
                'topheader'=> Topheader::orderBy('id', 'DESC')->get(),
                'themeCustomization' => Schema::hasTable('theme_customizations') ? ThemeCustomization::query()->first() : null,

                'sidecategories' => Category::query()
                    ->where('status', 1)
                    ->select($categorySelect)
                    ->orderBy('serial')
                    ->orderBy('id')
                    ->get(),
                'menucategories' => Category::query()
                    ->where('status', 1)
                    ->select($categorySelect)
                    ->with([
                        'menusubcategories' => function ($query) {
                            $query->with('menuchildcategories');
                        },
                    ])
                    ->orderBy('serial')
                    ->orderBy('id')
                    ->get(),
                'contact' => Contact::where('status', 1)->first(),
                'socialicons' => SocialMedia::where('status', 1)->get(),
                'cmnmenu' => CreatePage::where('status', 1)->get(),
                'brands' => Brand::where('status', 1)->get(),
                'orderstatus' => OrderStatus::get(),
                'pixels' => EcomPixel::where('status', 1)->get(),
                'gtm_code' => GoogleTagManager::where('status', 1)->get(),
                'marketingToolConfig' => Schema::hasTable('marketing_tool_configs') ? MarketingToolConfig::where('status', 1)->first() : null,
            ];
        });

        // Destructure and share
        foreach ($sharedData as $key => $value) {
            view()->share($key, $value);
        }

        // Fresh order data (cached for 10 seconds)
        view()->share('neworder', Cache::remember('neworder_count', 10, function () {
            return Order::where('order_status', 1)->count();
        }));
        view()->share('pendingorder', Cache::remember('pendingorder_list', 10, function () {
            return Order::where('order_status', 1)->latest()->limit(9)->get();
        }));

        // Live counters for the vendor panel top menu badges.
        view()->composer('vendorPanel.layouts.mobile_menu', function ($view) {
            $vendor = auth('vendor')->user();
            $view->with('vendorMenuCounts', $vendor
                ? \App\Support\VendorMenuCounts::get($vendor->id)
                : []);
        });
    }

}
