<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LandingController;
use App\Http\Controllers\Frontend\CatalogController;
use App\Http\Controllers\Frontend\ContentController;
use App\Http\Controllers\Frontend\MarketingController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Http\Controllers\Frontend\CampaignController as FrontendCampaignController;
use App\Http\Controllers\Frontend\FrontendAjaxController;
use App\Http\Controllers\Frontend\ShoppingController;
use App\Http\Controllers\Frontend\CustomerController;
use App\Http\Controllers\Vendor\AuthController as VendorAuthController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Vendor\ProductController as VendorProductController;
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;
use App\Http\Controllers\Admin\VendorController as AdminVendorController;
use App\Http\Controllers\Admin\HubController as AdminHubController;
use App\Http\Controllers\Admin\VendorWithdrawController as AdminVendorWithdrawController;
use App\Http\Controllers\Admin\ResellerController as AdminResellerController;
use App\Http\Controllers\Admin\ResellerTicketController as AdminResellerTicketController;
use App\Http\Controllers\Reseller\AuthController as ResellerAuthController;
use App\Http\Controllers\Reseller\DashboardController as ResellerDashboardController;
use App\Http\Controllers\Reseller\ProductController as ResellerProductController;
use App\Http\Controllers\Reseller\FavouriteController as ResellerFavouriteController;
use App\Http\Controllers\Reseller\TicketController as ResellerTicketController;
use App\Http\Controllers\Reseller\CartController as ResellerCartController;
use App\Http\Controllers\Reseller\CheckoutController as ResellerCheckoutController;
use App\Http\Controllers\Reseller\WithdrawController as ResellerWithdrawController;
use App\Http\Controllers\Marketing\FacebookEventController;
use App\Http\Controllers\Marketing\TiktokEventController;
use App\Http\Controllers\Payment\BkashController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\ShurjopayControllers;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\ChildcategoryController;
use App\Http\Controllers\Admin\OrderStatusController;
use App\Http\Controllers\Admin\PixelsController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\ApiIntegrationController;
use App\Http\Controllers\Admin\AtrributeListController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\SocialMediaController;
use App\Http\Controllers\Admin\PromoStripController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\BannerCategoryController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CreatePageController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\CustomerManageController;
use App\Http\Controllers\Admin\ShippingChargeController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\TagManagerController;
use App\Http\Controllers\Admin\CouponCodeController;
use App\Http\Controllers\Admin\ModelController as AdminModelController;
use App\Http\Controllers\Admin\ValueController;
use App\Http\Controllers\Admin\TopHeaderController;
use App\Http\Controllers\Admin\ThemeCustomizationController;
use App\Http\Controllers\Admin\WeightController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\MarketingToolsController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\AccountHeadController;
use App\Http\Controllers\Admin\FinancialAccountController;
use App\Http\Controllers\Admin\IncomeCategoryController;
use App\Http\Controllers\Admin\IncomeController;
use App\Http\Controllers\Admin\JournalEntryController;
use App\Http\Controllers\Admin\AccountsReportController;
use App\Http\Controllers\Admin\SupplierLedgerController;
use App\Http\Controllers\Admin\CustomerDueController;
use App\Http\Controllers\Admin\FundTransferController;
use App\Http\Controllers\Admin\ReturnRefundController;
use App\Http\Controllers\Admin\PaymentMethodReportController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\SupplierPaymentController;
use App\Http\Controllers\Admin\InventoryDashboardController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\HrDepartmentController;
use App\Http\Controllers\Admin\HrEmployeeController;
use App\Http\Controllers\Admin\HrAttendanceController;
use App\Http\Controllers\Admin\HrLeaveController;
use App\Http\Controllers\Admin\HrPayrollController;
use App\Http\Controllers\Admin\HrDashboardController;
use App\Http\Controllers\Admin\HrShiftController;
use App\Http\Controllers\Admin\HrHolidayController;
use App\Http\Controllers\Admin\HrLeaveTypeController;
use App\Http\Controllers\Admin\HrDesignationController;
use App\Http\Controllers\Admin\HrSalaryStructureController;
use App\Http\Controllers\Admin\HrAdvanceController;
use App\Http\Controllers\Admin\HrBonusController;
use App\Http\Controllers\Admin\HrDocumentController;
use App\Http\Controllers\Admin\HrNoticeController;
use App\Http\Controllers\Admin\HrSeparationController;
use App\Http\Controllers\Admin\HrPerformanceNoteController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\Support\SummernoteController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;


Auth::routes();
Route::get('/cc', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    Toastr::success('Cache cleared successfully', 'Success');

    return redirect()->back();
});
Route::get('/optimize-clear', function () {
    Artisan::call('optimize:clear');

    Toastr::success('Optimize cache cleared successfully', 'Success');

    return redirect()->back();
});

Route::get('/gc', function () {
    Artisan::call('make:controller Marketing/FacebookEventController');
    return 'Controller created!';
});


// Route::get('/migrate', function() {
//     $exitCode = Artisan::call('migrate');
//     return '<h1>Make Model</h1>';
// });

// Route::get('/controller', function() {
//     Artisan::call('make:controller Admin/TagManagerController');
//     return "Controller Done!";
// });
Route::get('/hello',function(){
    return "hello";
});
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.xml');
Route::get('/merchant-center/feed.xml', [MarketingController::class, 'merchantFeed'])->name('merchant.feed');
Route::get('/recover-cart/{token}', [MarketingController::class, 'recoverCart'])->name('marketing.recover_cart');
Route::post('/marketing/google-event-log', [MarketingController::class, 'logGoogleEvent'])->name('marketing.google_event_log');
Route::get('/dump-autoload', function () {
    Artisan::call('dump-autoload');
    return "Composer dump-autoload executed!";
});

Route::post('facebook/pageview-capi', [FacebookEventController::class, 'pageViewCAPI'])->name('facebook.pageview_capi');

Route::post('facebook/view-content-capi', [FacebookEventController::class, 'viewContent'])
    ->name('facebook.view_content_capi');


Route::post('/facebook/add-to-cart-capi', [FacebookEventController::class, 'addToCart'])
     ->name('facebook.add_to_cart_capi');

Route::post('/facebook/begin-checkout-capi', [FacebookEventController::class, 'beginCheckoutCAPI'])
    ->name('facebook.begin_checkout_capi');

Route::post('/facebook/purchase-capi', [FacebookEventController::class, 'purchaseCAPI'])
    ->name('facebook.purchase_capi');

Route::post('tiktok/pageview-capi', [TiktokEventController::class, 'pageView'])->name('tiktok.pageview_capi');

Route::post('tiktok/view-content-capi', [TiktokEventController::class, 'viewContent'])
    ->name('tiktok.view_content_capi');

Route::post('/tiktok/add-to-cart-capi', [TiktokEventController::class, 'addToCart'])
    ->name('tiktok.add_to_cart_capi');

Route::post('/tiktok/begin-checkout-capi', [TiktokEventController::class, 'beginCheckout'])
    ->name('tiktok.begin_checkout_capi');

Route::post('/tiktok/purchase-capi', [TiktokEventController::class, 'purchase'])
    ->name('tiktok.purchase_capi');
Route::post('/marketing/abandoned-cart/sync', [MarketingController::class, 'syncAbandonedCart'])->name('marketing.abandoned_cart.sync');
Route::post('/marketing/visitor-analytics/log', [MarketingController::class, 'logVisitor'])->name('marketing.visitor_analytics.log');

Route::get('/frontEnd/css/theme.css', function () {
    return response()
        ->view('frontEnd.layouts.theme-css')
        ->header('Content-Type', 'text/css');
})->name('frontend.theme_css');

/*
|--------------------------------------------------------------------------
| NEW: Reseller/Dropshipping marketing landing page (replaces storefront home)
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/about-us', [LandingController::class, 'about'])->name('landing.about');
Route::get('/services', [LandingController::class, 'services'])->name('landing.services');
Route::get('/our-products', [LandingController::class, 'products'])->name('landing.products');
Route::get('/product/{slug}', [LandingController::class, 'productShow'])->name('landing.product.show');
Route::get('/how-it-works', [LandingController::class, 'howItWorks'])->name('landing.how');
Route::get('/contact-us', [LandingController::class, 'contact'])->name('landing.contact');
Route::post('/contact-us', [LandingController::class, 'contactSubmit'])->name('landing.contact.submit');

/*
|--------------------------------------------------------------------------
| DISABLED: Customer-facing e-commerce storefront
| -------------------------------------------------------------------------
| The public shopping experience (catalog, product details, cart, checkout,
| customer accounts, payment callbacks) has been retired in favour of the
| reseller/vendor model. Routes kept commented for reference; do not remove.
|--------------------------------------------------------------------------
*/
/*
    Route::get('/store',[HomeController::class,'storepage'])->name('storepage');


Route::post('/customer/coupon', [CustomerController::class, 'customer_coupon'])->name('customer.coupon');
Route::post('/customer/coupon-remove', [CustomerController::class, 'coupon_remove'])->name('customer.coupon_remove');
Route::group(['namespace'=>'Frontend', 'middleware' => ['ipcheck','check_refer']], function() {
    Route::get('category/{category}', [CatalogController::class, 'category'])->name('category');
    Route::get('subcategory/{subcategory}', [CatalogController::class, 'subcategory'])->name('subcategory');
    Route::get('products/{slug}', [CatalogController::class, 'products'])->name('products');
    Route::get('hot-deals', [CatalogController::class, 'hotdeals'])->name('hotdeals');
    Route::get('livesearch', [FrontendAjaxController::class, 'liveSearch'])->name('livesearch');
    Route::get('search', [CatalogController::class, 'search'])->name('search');
    Route::get('product/{id}', [CatalogController::class, 'details'])->name('product');
    Route::post('recently-viewed/clear', [HomeController::class, 'clearRecentlyViewed'])->name('recently_viewed.clear');
    Route::get('quick-view', [FrontendAjaxController::class, 'quickView'])->name('quickview');
    Route::get('/shipping-charge', [FrontendAjaxController::class, 'shippingCharge'])->name('shipping.charge');
    Route::get('home/all-products', [FrontendAjaxController::class, 'allProducts'])->name('home.all_products');
    Route::get('site/contact-us', [ContentController::class, 'contact'])->name('contact');
    Route::post('contact/submit', [ContentController::class, 'contact_submit'])->name('contact.send');
    Route::get('/page/{slug}', [ContentController::class, 'page'])->name('page');
    Route::get('districts', [FrontendAjaxController::class, 'districts'])->name('districts');
    Route::get('/campaign/{slug}', [FrontendCampaignController::class, 'show'])->name('campaign');
    Route::get('/campaign-stock-check', [FrontendAjaxController::class, 'campaignStock'])->name('campaign.stock_check');
    Route::get('/offer', [HomeController::class, 'offers'])->name('offers');
    Route::get('stock-check', [FrontendAjaxController::class, 'stockCheck'])->name('stock_check');
    Route::get('/payment-success', [PaymentController::class, 'success'])->name('payment_success');
    Route::get('/payment-cancel', [PaymentController::class, 'cancel'])->name('payment_cancel');
    // cart route
    Route::post('cart/store', [ShoppingController::class, 'cart_store'])->name('cart.store');
    Route::get('/add-to-cart/{id}/{qty}', [ShoppingController::class, 'addTocartGet']);
    Route::get('shop/cart', [ShoppingController::class, 'cart_show'])->name('cart.show');
    Route::get('cart/remove', [ShoppingController::class, 'cart_remove'])->name('cart.remove');
    Route::get('cart/remove-bn', [ShoppingController::class, 'cart_remove_bn'])->name('cart.remove_bn');
    Route::get('cart/content', [ShoppingController::class, 'cart_content'])->name('cart.content');
    Route::get('cart/count', [ShoppingController::class, 'cart_count'])->name('cart.count');
    Route::get('mobilecart/count', [ShoppingController::class, 'mobilecart_qty'])->name('mobile.cart.count');
    Route::get('cart/decrement', [ShoppingController::class, 'cart_decrement'])->name('cart.decrement');
    Route::get('cart/increment', [ShoppingController::class, 'cart_increment'])->name('cart.increment');
    Route::get('cart/decrement-bn', [ShoppingController::class, 'cart_decrement_bn'])->name('cart.decrement_bn');
    Route::get('cart/increment-bn', [ShoppingController::class, 'cart_increment_bn'])->name('cart.increment_bn');

});

Route::group(['prefix'=>'customer','namespace'=>'Frontend', 'middleware' => ['ipcheck','check_refer']], function() {
    Route::get('/login', [CustomerController::class, 'login'])->name('customer.login');
    Route::post('/signin', [CustomerController::class, 'signin'])->name('customer.signin');
    Route::get('/register', [CustomerController::class, 'register'])->name('customer.register');
    Route::post('/store', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('/verify', [CustomerController::class, 'verify'])->name('customer.verify');
    Route::post('/verify-account', [CustomerController::class, 'account_verify'])->name('customer.account.verify');
    Route::post('/resend-otp', [CustomerController::class, 'resendotp'])->name('customer.resendotp');
    Route::post('/logout', [CustomerController::class, 'logout'])->name('customer.logout');
    Route::post('/post/review', [CustomerController::class, 'review'])->name('customer.review');
    Route::get('/forgot-password', [CustomerController::class, 'forgot_password'])->name('customer.forgot.password');
    Route::post('/forgot-verify', [CustomerController::class, 'forgot_verify'])->name('customer.forgot.verify');
    Route::get('/forgot-password/reset', [CustomerController::class, 'forgot_reset'])->name('customer.forgot.reset');
    Route::post('/forgot-password/store', [CustomerController::class, 'forgot_store'])->name('customer.forgot.store');
    Route::post('/forgot-password/resendotp', [CustomerController::class, 'forgot_resend'])->name('customer.forgot.resendotp');
    Route::get('/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
    Route::post('/order-save', [CustomerController::class, 'order_save'])->name('customer.ordersave');
    Route::get('/order-success/{id}', [CustomerController::class, 'order_success'])->name('customer.order_success');

   Route::get('/order-track', [CustomerController::class, 'order_track'])->name('customer.order_track');
    Route::get('/order-track/result', [CustomerController::class, 'order_track_result'])->name('customer.order_track_result');


});
// customer auth
Route::group(['prefix'=>'customer','namespace'=>'Frontend','middleware' => ['customer','ipcheck','check_refer']], function() {

    Route::get('/account', [CustomerController::class, 'account'])->name('customer.account');

    Route::get('/orders', [CustomerController::class, 'orders'])->name('customer.orders');
    Route::get('/invoice', [CustomerController::class, 'invoice'])->name('customer.invoice');
    Route::get('/invoice/order-note', [CustomerController::class, 'order_note'])->name('customer.order_note');
    Route::get('/profile-edit', [CustomerController::class, 'profile_edit'])->name('customer.profile_edit');
    Route::post('/profile-update', [CustomerController::class, 'profile_update'])->name('customer.profile_update');
    Route::get('/change-password', [CustomerController::class, 'change_pass'])->name('customer.change_pass');
    Route::post('/password-update', [CustomerController::class, 'password_update'])->name('customer.password_update');


});

Route::group(['namespace'=>'Frontend', 'middleware' => ['ipcheck','check_refer']], function() {

    Route::get('bkash/checkout-url/pay',[BkashController::class,'pay'])->name('url-pay');
Route::any('bkash/checkout-url/create',[BkashController::class,'create'])->name('url-create');
Route::get('bkash/checkout-url/callback',[BkashController::class,'callback'])->name('url-callback');
    Route::get('/payment-success', [ShurjopayControllers::class, 'payment_success'])->name('payment_success');
    Route::get('/payment-cancel', [ShurjopayControllers::class, 'payment_cancel'])->name('payment_cancel');

});
*/
// END disabled storefront routes

// unathenticate admin route
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware' => ['customer','ipcheck','check_refer']], function() {
    Route::get('locked', [DashboardController::class, 'locked'])->name('locked');
    Route::post('unlocked', [DashboardController::class, 'unlocked'])->name('unlocked');
});

// ajax route
Route::get('/ajax-product-subcategory', [ProductController::class, 'getSubcategory']);
Route::get('/ajax-product-childcategory', [ProductController::class, 'getChildcategory']);

// auth route
Route::group(['namespace'=>'Admin','middleware' => ['auth','lock','check_refer'],'prefix'=>'admin'], function() {
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('change-password', [DashboardController::class, 'changepassword'])->name('change_password');
    Route::post('new-password', [DashboardController::class, 'newpassword'])->name('new_password');

    Route::post('order-pathao', [OrderController::class,'order_pathao'])->name('admin.order.pathao');
    Route::post('order/fraud-check', [OrderController::class,'fraud_check'])->name('admin.order.fraud_check');
    Route::get('order-steadfast/{order_id}', [OrderController::class,'order_steadfast'])->name('admin.order.steadfast');
    Route::get('/pathao-update-status', [OrderController::class, 'updatePathaoStatus'])->name('updatePathaoStatus');
    Route::post('/pathao-update-status-webhook', [OrderController::class, 'updatePathaoStatusWebhook'])->name('updatePathaoStatusWebhook');
    Route::get('/steadfast-update-status', [OrderController::class, 'updateSteadfastStatus'])->name('updateSteadfastStatus');
    Route::post('/steadfast-update-status-webhook', [OrderController::class, 'updateSteadfastStatusWebhook'])->name('updateSteadfastStatusWebhook');


    // users route
    Route::get('users/manage', [UserController::class,'index'])->name('users.index');
    Route::get('users/create', [UserController::class,'create'])->name('users.create');
    Route::post('users/save', [UserController::class,'store'])->name('users.store');
    Route::get('users/{id}/edit', [UserController::class,'edit'])->name('users.edit');
    Route::post('users/update', [UserController::class,'update'])->name('users.update');
    Route::post('users/inactive', [UserController::class,'inactive'])->name('users.inactive');
    Route::post('users/active', [UserController::class,'active'])->name('users.active');
    Route::post('users/destroy', [UserController::class,'destroy'])->name('users.destroy');

    // roles
    Route::get('roles/manage', [RoleController::class,'index'])->name('roles.index');
    Route::get('roles/{id}/show', [RoleController::class,'show'])->name('roles.show');
    Route::get('roles/create', [RoleController::class,'create'])->name('roles.create');
    Route::post('roles/save', [RoleController::class,'store'])->name('roles.store');
    Route::get('roles/{id}/edit', [RoleController::class,'edit'])->name('roles.edit');
    Route::post('roles/update', [RoleController::class,'update'])->name('roles.update');
    Route::post('roles/destroy', [RoleController::class,'destroy'])->name('roles.destroy');

    // permissions
    Route::get('permissions/manage', [PermissionController::class,'index'])->name('permissions.index');
    Route::get('permissions/{id}/show', [PermissionController::class,'show'])->name('permissions.show');
    Route::get('permissions/create', [PermissionController::class,'create'])->name('permissions.create');
    Route::post('permissions/save', [PermissionController::class,'store'])->name('permissions.store');
    Route::get('permissions/{id}/edit', [PermissionController::class,'edit'])->name('permissions.edit');
    Route::post('permissions/update', [PermissionController::class,'update'])->name('permissions.update');
    Route::post('permissions/destroy', [PermissionController::class,'destroy'])->name('permissions.destroy');

    // categories
    Route::get('categories/manage', [CategoryController::class,'index'])->name('categories.index');
    Route::get('categories/create', [CategoryController::class,'create'])->name('categories.create');
    Route::post('categories/save', [CategoryController::class,'store'])->name('categories.store');
    Route::get('categories/{id}/edit', [CategoryController::class,'edit'])->name('categories.edit');
    Route::post('categories/update', [CategoryController::class,'update'])->name('categories.update');
    Route::post('categories/inactive', [CategoryController::class,'inactive'])->name('categories.inactive');
    Route::post('categories/active', [CategoryController::class,'active'])->name('categories.active');
    Route::post('categories/destroy', [CategoryController::class,'destroy'])->name('categories.destroy');

    // Subcategories
    Route::get('subcategories/manage', [SubcategoryController::class,'index'])->name('subcategories.index');
    Route::get('subcategories/create', [SubcategoryController::class,'create'])->name('subcategories.create');
    Route::post('subcategories/save', [SubcategoryController::class,'store'])->name('subcategories.store');
    Route::get('subcategories/{id}/edit', [SubcategoryController::class,'edit'])->name('subcategories.edit');
    Route::post('subcategories/update', [SubcategoryController::class,'update'])->name('subcategories.update');
    Route::post('subcategories/inactive', [SubcategoryController::class,'inactive'])->name('subcategories.inactive');
    Route::post('subcategories/active', [SubcategoryController::class,'active'])->name('subcategories.active');
    Route::post('subcategories/destroy', [SubcategoryController::class,'destroy'])->name('subcategories.destroy');

    // Childcategories
    Route::get('childcategories/manage', [ChildcategoryController::class,'index'])->name('childcategories.index');
    Route::get('childcategories/create', [ChildcategoryController::class,'create'])->name('childcategories.create');
    Route::post('childcategories/save', [ChildcategoryController::class,'store'])->name('childcategories.store');
    Route::get('childcategories/{id}/edit', [ChildcategoryController::class,'edit'])->name('childcategories.edit');
    Route::post('childcategories/update', [ChildcategoryController::class,'update'])->name('childcategories.update');
    Route::post('childcategories/inactive', [ChildcategoryController::class,'inactive'])->name('childcategories.inactive');
    Route::post('childcategories/active', [ChildcategoryController::class,'active'])->name('childcategories.active');
    Route::post('childcategories/destroy', [ChildcategoryController::class,'destroy'])->name('childcategories.destroy');

     // paymentgeteway
    Route::get('paymentgeteway/manage', [ApiIntegrationController::class,'pay_manage'])->name('paymentgeteway.manage');
    Route::post('paymentgeteway/save', [ApiIntegrationController::class,'pay_update'])->name('paymentgeteway.update');

     // smsgeteway
    Route::get('smsgeteway/manage', [ApiIntegrationController::class,'sms_manage'])->name('smsgeteway.manage');
    Route::post('smsgeteway/save', [ApiIntegrationController::class,'sms_update'])->name('smsgeteway.update');

    // courierapi
    Route::get('courierapi/manage', [ApiIntegrationController::class,'courier_manage'])->name('courierapi.manage');
    Route::post('courierapi/save', [ApiIntegrationController::class,'courier_update'])->name('courierapi.update');
    Route::post('courierapi/test-connection', [ApiIntegrationController::class,'courier_test_connection'])->name('courierapi.test_connection');
    Route::post('courierapi/pathao-regenerate-token', [ApiIntegrationController::class,'pathao_regenerate_token'])->name('courierapi.pathao_regenerate_token');

    // attribute
    Route::get('orderstatus/manage', [OrderStatusController::class,'index'])->name('orderstatus.index');
    Route::get('orderstatus/{id}/show', [OrderStatusController::class,'show'])->name('orderstatus.show');
    Route::get('orderstatus/create', [OrderStatusController::class,'create'])->name('orderstatus.create');
    Route::post('orderstatus/save', [OrderStatusController::class,'store'])->name('orderstatus.store');
    Route::get('orderstatus/{id}/edit', [OrderStatusController::class,'edit'])->name('orderstatus.edit');
    Route::post('orderstatus/update', [OrderStatusController::class,'update'])->name('orderstatus.update');
    Route::post('orderstatus/inactive', [OrderStatusController::class,'inactive'])->name('orderstatus.inactive');
    Route::post('orderstatus/active', [OrderStatusController::class,'active'])->name('orderstatus.active');
    Route::post('orderstatus/destroy', [OrderStatusController::class,'destroy'])->name('orderstatus.destroy');

    // pixels
    Route::get('pixels/manage', [PixelsController::class,'index'])->name('pixels.index');
    Route::get('pixels/{id}/show', [PixelsController::class,'show'])->name('pixels.show');
    Route::get('pixels/create', [PixelsController::class,'create'])->name('pixels.create');
    Route::post('pixels/save', [PixelsController::class,'store'])->name('pixels.store');
    Route::get('pixels/{id}/edit', [PixelsController::class,'edit'])->name('pixels.edit');
    Route::post('pixels/update', [PixelsController::class,'update'])->name('pixels.update');
    Route::post('pixels/inactive', [PixelsController::class,'inactive'])->name('pixels.inactive');
    Route::post('pixels/active', [PixelsController::class,'active'])->name('pixels.active');
    Route::post('pixels/destroy', [PixelsController::class,'destroy'])->name('pixels.destroy');

     // tag manager
    Route::get('tag-manager/manage', [TagManagerController::class,'index'])->name('tagmanagers.index');
    Route::get('tag-manager/{id}/show', [TagManagerController::class,'show'])->name('tagmanagers.show');
    Route::get('tag-manager/create', [TagManagerController::class,'create'])->name('tagmanagers.create');
    Route::post('tag-manager/save', [TagManagerController::class,'store'])->name('tagmanagers.store');
    Route::get('tag-manager/{id}/edit', [TagManagerController::class,'edit'])->name('tagmanagers.edit');
    Route::post('tag-manager/update', [TagManagerController::class,'update'])->name('tagmanagers.update');
    Route::post('tag-manager/inactive', [TagManagerController::class,'inactive'])->name('tagmanagers.inactive');
    Route::post('tag-manager/active', [TagManagerController::class,'active'])->name('tagmanagers.active');
    Route::post('tag-manager/destroy', [TagManagerController::class,'destroy'])->name('tagmanagers.destroy');

    Route::get('marketing-tools/manage', [MarketingToolsController::class, 'index'])->name('marketing.tools.index');
    Route::post('marketing-tools/update', [MarketingToolsController::class, 'update'])->name('marketing.tools.update');
    Route::get('reports/utm-campaigns', [ReportsController::class, 'utmCampaigns'])->name('reports.utm_campaigns');
    Route::get('reports/conversion-dashboard', [ReportsController::class, 'conversionDashboard'])->name('reports.conversion_dashboard');
    Route::get('reports/incomplete-orders', [ReportsController::class, 'incompleteOrders'])->name('reports.incomplete_orders');
    Route::get('reports/visitor-analytics', [ReportsController::class, 'visitorAnalytics'])->name('reports.visitor_analytics');

    // attribute
    Route::get('brands/manage', [BrandController::class,'index'])->name('brands.index');
    Route::get('brands/create', [BrandController::class,'create'])->name('brands.create');
    Route::post('brands/save', [BrandController::class,'store'])->name('brands.store');
    Route::get('brands/{id}/edit', [BrandController::class,'edit'])->name('brands.edit');
    Route::post('brands/update', [BrandController::class,'update'])->name('brands.update');
    Route::post('brands/inactive', [BrandController::class,'inactive'])->name('brands.inactive');
    Route::post('brands/active', [BrandController::class,'active'])->name('brands.active');
    Route::post('brands/destroy', [BrandController::class,'destroy'])->name('brands.destroy');

     // color
    Route::get('color/manage', [ColorController::class,'index'])->name('colors.index');
    Route::get('color/{id}/show', [ColorController::class,'show'])->name('colors.show');
    Route::get('color/create', [ColorController::class,'create'])->name('colors.create');
    Route::post('color/save', [ColorController::class,'store'])->name('colors.store');
    Route::get('color/{id}/edit', [ColorController::class,'edit'])->name('colors.edit');
    Route::post('color/update', [ColorController::class,'update'])->name('colors.update');
    Route::post('color/inactive', [ColorController::class,'inactive'])->name('colors.inactive');
    Route::post('color/active', [ColorController::class,'active'])->name('colors.active');
    Route::post('color/destroy', [ColorController::class,'destroy'])->name('colors.destroy');

    // size
    Route::get('size/manage', [SizeController::class,'index'])->name('sizes.index');
    Route::get('size/{id}/show', [SizeController::class,'show'])->name('sizes.show');
    Route::get('size/create', [SizeController::class,'create'])->name('sizes.create');
    Route::post('size/save', [SizeController::class,'store'])->name('sizes.store');
    Route::get('size/{id}/edit', [SizeController::class,'edit'])->name('sizes.edit');
    Route::post('size/update', [SizeController::class,'update'])->name('sizes.update');
    Route::post('size/inactive', [SizeController::class,'inactive'])->name('sizes.inactive');
    Route::post('size/active', [SizeController::class,'active'])->name('sizes.active');
    Route::post('size/destroy', [SizeController::class,'destroy'])->name('sizes.destroy');


    // product
    Route::get('products/manage', [ProductController::class,'index'])->name('products.index');
    Route::get('media/manage', [MediaController::class,'index'])->name('media.index');
    Route::post('media/upload', [MediaController::class,'store'])->name('media.store');
    Route::post('media/destroy', [MediaController::class,'destroy'])->name('media.destroy');
    Route::get('products/create', [ProductController::class,'create'])->name('products.create');
    Route::post('products/save', [ProductController::class,'store'])->name('products.store');
    Route::get('products/{id}/edit', [ProductController::class,'edit'])->name('products.edit');
    Route::get('products/{id}/barcode-labels', [ProductController::class,'barcodeLabels'])->name('products.barcode_labels');
    Route::get('products/{id}/copy', [ProductController::class,'copy'])->name('products.copy');
    Route::post('products/update', [ProductController::class,'update'])->name('products.update');
    Route::post('products/inactive', [ProductController::class,'inactive'])->name('products.inactive');
    Route::post('products/active', [ProductController::class,'active'])->name('products.active');
    Route::post('products/destroy', [ProductController::class,'destroy'])->name('products.destroy');
    Route::get('products/image/destroy', [ProductController::class,'imgdestroy'])->name('products.image.destroy');
    Route::get('products/price/destroy/{id}', [ProductController::class,'pricedestroy'])->name('products.price.destroy');
    Route::get('products/update-deals', [ProductController::class,'update_deals'])->name('products.update_deals');
    Route::get('products/update-feature', [ProductController::class,'update_feature'])->name('products.update_feature');
    Route::get('products/update-status', [ProductController::class,'update_status'])->name('products.update_status');
    Route::get('products/price-edit', [ProductController::class,'price_edit'])->name('products.price_edit');
    Route::post('products/price-update', [ProductController::class,'price_update'])->name('products.price_update');

    // campaign
    Route::get('campaign/manage', [CampaignController::class,'index'])->name('campaign.index');
    Route::get('campaign/{id}/show', [CampaignController::class,'show'])->name('campaign.show');
    Route::get('campaign/create', [CampaignController::class,'create'])->name('campaign.create');
    Route::post('campaign/save', [CampaignController::class,'store'])->name('campaign.store');
    Route::get('campaign/{id}/edit', [CampaignController::class,'edit'])->name('campaign.edit');
    Route::post('campaign/update', [CampaignController::class,'update'])->name('campaign.update');
    Route::post('campaign/inactive', [CampaignController::class,'inactive'])->name('campaign.inactive');
    Route::post('campaign/active', [CampaignController::class,'active'])->name('campaign.active');
    Route::post('campaign/destroy', [CampaignController::class,'destroy'])->name('campaign.destroy');
    Route::get('campaign/image/destroy', [CampaignController::class,'imgdestroy'])->name('campaign.image.destroy');

    // settings route
    Route::get('settings/manage', [GeneralSettingController::class,'index'])->name('settings.index');
    Route::get('settings/create', [GeneralSettingController::class,'create'])->name('settings.create');
    Route::post('settings/save', [GeneralSettingController::class,'store'])->name('settings.store');
    Route::get('settings/{id}/edit', [GeneralSettingController::class,'edit'])->name('settings.edit');
    Route::post('settings/update', [GeneralSettingController::class,'update'])->name('settings.update');
    Route::get('seo-configuration', [GeneralSettingController::class,'seo'])->name('seo.config.index');
    Route::post('seo-configuration/update', [GeneralSettingController::class,'seoUpdate'])->name('seo.config.update');
    Route::post('settings/inactive', [GeneralSettingController::class,'inactive'])->name('settings.inactive');
    Route::post('settings/active', [GeneralSettingController::class,'active'])->name('settings.active');
    Route::post('settings/destroy', [GeneralSettingController::class,'destroy'])->name('settings.destroy');

    Route::get('theme-customization', [ThemeCustomizationController::class,'index'])->name('theme.customization.index');
    Route::post('theme-customization/update', [ThemeCustomizationController::class,'update'])->name('theme.customization.update');
    Route::get('theme-customization/landing-hero', [ThemeCustomizationController::class,'hero'])->name('theme.hero.index');
    Route::post('theme-customization/landing-hero/update', [ThemeCustomizationController::class,'heroUpdate'])->name('theme.hero.update');

     // settings route
    Route::get('social-media/manage', [SocialMediaController::class,'index'])->name('socialmedias.index');
    Route::get('social-media/create', [SocialMediaController::class,'create'])->name('socialmedias.create');
    Route::post('social-media/save', [SocialMediaController::class,'store'])->name('socialmedias.store');
    Route::get('social-media/{id}/edit', [SocialMediaController::class,'edit'])->name('socialmedias.edit');
    Route::post('social-media/update', [SocialMediaController::class,'update'])->name('socialmedias.update');
    Route::post('social-media/inactive', [SocialMediaController::class,'inactive'])->name('socialmedias.inactive');
    Route::post('social-media/active', [SocialMediaController::class,'active'])->name('socialmedias.active');
    Route::post('social-media/destroy', [SocialMediaController::class,'destroy'])->name('socialmedias.destroy');

     // contact route
    Route::get('contact/manage', [ContactController::class,'index'])->name('contact.index');
    Route::get('contact/create', [ContactController::class,'create'])->name('contact.create');
    Route::post('contact/save', [ContactController::class,'store'])->name('contact.store');
    Route::get('contact/{id}/edit', [ContactController::class,'edit'])->name('contact.edit');
    Route::post('contact/update', [ContactController::class,'update'])->name('contact.update');
    Route::post('contact/inactive', [ContactController::class,'inactive'])->name('contact.inactive');
    Route::post('contact/active', [ContactController::class,'active'])->name('contact.active');
    Route::post('contact/destroy', [ContactController::class,'destroy'])->name('contact.destroy');

     // banner category route
    Route::get('banner-category/manage', [BannerCategoryController::class,'index'])->name('banner_category.index');
    Route::get('banner-category/create', [BannerCategoryController::class,'create'])->name('banner_category.create');
    Route::post('banner-category/save', [BannerCategoryController::class,'store'])->name('banner_category.store');
    Route::get('banner-category/{id}/edit', [BannerCategoryController::class,'edit'])->name('banner_category.edit');
    Route::post('banner-category/update', [BannerCategoryController::class,'update'])->name('banner_category.update');
    Route::post('banner-category/inactive', [BannerCategoryController::class,'inactive'])->name('banner_category.inactive');
    Route::post('banner-category/active', [BannerCategoryController::class,'active'])->name('banner_category.active');
    Route::post('banner-category/destroy', [BannerCategoryController::class,'destroy'])->name('banner_category.destroy');

    // banner  route
    Route::get('banner/manage', [BannerController::class,'index'])->name('banners.index');
    Route::get('banner/create', [BannerController::class,'create'])->name('banners.create');
    Route::post('banner/save', [BannerController::class,'store'])->name('banners.store');
    Route::get('banner/{id}/edit', [BannerController::class,'edit'])->name('banners.edit');
    Route::post('banner/update', [BannerController::class,'update'])->name('banners.update');
    Route::post('banner/inactive', [BannerController::class,'inactive'])->name('banners.inactive');
    Route::post('banner/active', [BannerController::class,'active'])->name('banners.active');
    Route::post('banner/destroy', [BannerController::class,'destroy'])->name('banners.destroy');

    // promo strip routes
    Route::get('promo-strip/manage',        [PromoStripController::class, 'index'])->name('promo_strip.index');
    Route::get('promo-strip/create',        [PromoStripController::class, 'create'])->name('promo_strip.create');
    Route::post('promo-strip/save',         [PromoStripController::class, 'store'])->name('promo_strip.store');
    Route::get('promo-strip/{id}/edit',     [PromoStripController::class, 'edit'])->name('promo_strip.edit');
    Route::post('promo-strip/update',       [PromoStripController::class, 'update'])->name('promo_strip.update');
    Route::post('promo-strip/destroy',      [PromoStripController::class, 'destroy'])->name('promo_strip.destroy');
    Route::post('promo-strip/toggle',       [PromoStripController::class, 'toggleStatus'])->name('promo_strip.toggle');

    // contact route
    Route::get('page/manage', [CreatePageController::class,'index'])->name('pages.index');
    Route::get('page/create', [CreatePageController::class,'create'])->name('pages.create');
    Route::post('page/save', [CreatePageController::class,'store'])->name('pages.store');
    Route::get('page/{id}/edit', [CreatePageController::class,'edit'])->name('pages.edit');
    Route::post('page/update', [CreatePageController::class,'update'])->name('pages.update');
    Route::post('page/inactive', [CreatePageController::class,'inactive'])->name('pages.inactive');
    Route::post('page/active', [CreatePageController::class,'active'])->name('pages.active');
    Route::post('page/destroy', [CreatePageController::class,'destroy'])->name('pages.destroy');

    // Pos route
    Route::get('order/search', [OrderController::class,'search'])->name('admin.livesearch');
    Route::get('pos', [OrderController::class,'order_create'])->name('admin.pos');
    Route::get('order/create', [OrderController::class,'order_create'])->name('admin.order.create');
    Route::get('order/product-preview', [OrderController::class,'product_preview'])->name('admin.order.product_preview');
    Route::get('order/catalog-products', [OrderController::class,'catalog_products'])->name('admin.order.catalog_products');
    Route::post('order/store', [OrderController::class,'order_store'])->name('admin.order.store');
    Route::get('order/cart-add', [OrderController::class,'cart_add'])->name('admin.order.cart_add');
    Route::get('order/cart-content', [OrderController::class,'cart_content'])->name('admin.order.cart_content');
    Route::get('order/cart-increment', [OrderController::class,'cart_increment'])->name('admin.order.cart_increment');
    Route::get('order/cart-decrement', [OrderController::class,'cart_decrement'])->name('admin.order.cart_decrement');
    Route::get('order/cart-remove', [OrderController::class,'cart_remove'])->name('admin.order.cart_remove');
    Route::get('order/cart-product-discount', [OrderController::class,'product_discount'])->name('admin.order.product_discount');
    Route::get('order/cart-product-price', [OrderController::class,'product_price'])->name('admin.order.product_price');
    Route::get('order/cart-details', [OrderController::class,'cart_details'])->name('admin.order.cart_details');
    Route::get('order/cart-shipping', [OrderController::class,'cart_shipping'])->name('admin.order.cart_shipping');
    Route::get('order/cart-clear', [OrderController::class,'cart_clear'])->name('admin.order.cart_clear');

    // Order route
    Route::get('order/{slug}', [OrderController::class,'index'])->name('admin.orders');
    Route::get('order/workspace/{invoice_id}', [OrderController::class,'order_workspace'])->name('admin.order.workspace');
    Route::get('order/edit/{invoice_id}', [OrderController::class,'order_edit'])->name('admin.order.edit');
    Route::post('order/update', [OrderController::class,'order_update'])->name('admin.order.update');
    Route::get('order/invoice/{invoice_id}', [OrderController::class,'invoice'])->name('admin.order.invoice');
    Route::get('order/invoice-print/{invoice_id}', [OrderController::class,'invoice_print'])->name('admin.order.invoice_print');
    Route::get('order/process/{invoice_id}', [OrderController::class,'process'])->name('admin.order.process');
    Route::post('order/change', [OrderController::class,'order_process'])->name('admin.order_change');
    Route::post('order/destroy', [OrderController::class,'destroy'])->name('admin.order.destroy');
    Route::post('order-assign', [OrderController::class,'order_assign'])->name('admin.order.assign');
    Route::get('order-status', [OrderController::class,'order_status'])->name('admin.order.status');
    Route::get('order-bulk-destroy', [OrderController::class,'bulk_destroy'])->name('admin.order.bulk_destroy');
    Route::get('order-print', [OrderController::class,'order_print'])->name('admin.order.order_print');
    Route::get('bulk-courier/{slug}', [OrderController::class,'bulk_courier'])->name('admin.bulk_courier');
    Route::get('stock-report', [OrderController::class,'stock_report'])->name('admin.stock_report');
    Route::get('order-report', [OrderController::class,'order_report'])->name('admin.order_report');
    Route::get('profit-loss-report', [OrderController::class,'loss_profit'])->name('admin.loss_profit');
    Route::post('profit-loss-report/recalculate', [OrderController::class,'recalculate_profit_loss_snapshots'])->name('admin.loss_profit.recalculate');
    Route::get('zero-cost-audit', [OrderController::class,'zero_cost_audit'])->name('admin.zero_cost_audit');
    Route::get('expense-categories/manage', [ExpenseCategoryController::class,'index'])->name('expensecategories.index');
    Route::get('expense-categories/create', [ExpenseCategoryController::class,'create'])->name('expensecategories.create');
    Route::post('expense-categories/save', [ExpenseCategoryController::class,'store'])->name('expensecategories.store');
    Route::get('expense-categories/{id}/edit', [ExpenseCategoryController::class,'edit'])->name('expensecategories.edit');
    Route::post('expense-categories/update', [ExpenseCategoryController::class,'update'])->name('expensecategories.update');
    Route::post('expense-categories/inactive', [ExpenseCategoryController::class,'inactive'])->name('expensecategories.inactive');
    Route::post('expense-categories/active', [ExpenseCategoryController::class,'active'])->name('expensecategories.active');
    Route::post('expense-categories/destroy', [ExpenseCategoryController::class,'destroy'])->name('expensecategories.destroy');
    Route::get('expenses/manage', [ExpenseController::class,'index'])->name('expenses.index');
    Route::get('expenses/create', [ExpenseController::class,'create'])->name('expenses.create');
    Route::post('expenses/save', [ExpenseController::class,'store'])->name('expenses.store');
    Route::get('expenses/{id}/edit', [ExpenseController::class,'edit'])->name('expenses.edit');
    Route::post('expenses/update', [ExpenseController::class,'update'])->name('expenses.update');
    Route::post('expenses/inactive', [ExpenseController::class,'inactive'])->name('expenses.inactive');
    Route::post('expenses/active', [ExpenseController::class,'active'])->name('expenses.active');
    Route::post('expenses/destroy', [ExpenseController::class,'destroy'])->name('expenses.destroy');
    Route::get('accounts/heads/manage', [AccountHeadController::class,'index'])->name('accounts.heads.index');
    Route::get('accounts/heads/create', [AccountHeadController::class,'create'])->name('accounts.heads.create');
    Route::post('accounts/heads/save', [AccountHeadController::class,'store'])->name('accounts.heads.store');
    Route::get('accounts/heads/{id}/edit', [AccountHeadController::class,'edit'])->name('accounts.heads.edit');
    Route::post('accounts/heads/update', [AccountHeadController::class,'update'])->name('accounts.heads.update');
    Route::get('accounts/financial-accounts/manage', [FinancialAccountController::class,'index'])->name('accounts.financial_accounts.index');
    Route::get('accounts/financial-accounts/create', [FinancialAccountController::class,'create'])->name('accounts.financial_accounts.create');
    Route::post('accounts/financial-accounts/save', [FinancialAccountController::class,'store'])->name('accounts.financial_accounts.store');
    Route::get('accounts/financial-accounts/{id}/edit', [FinancialAccountController::class,'edit'])->name('accounts.financial_accounts.edit');
    Route::post('accounts/financial-accounts/update', [FinancialAccountController::class,'update'])->name('accounts.financial_accounts.update');
    Route::get('accounts/income-categories/manage', [IncomeCategoryController::class,'index'])->name('accounts.income_categories.index');
    Route::get('accounts/income-categories/create', [IncomeCategoryController::class,'create'])->name('accounts.income_categories.create');
    Route::post('accounts/income-categories/save', [IncomeCategoryController::class,'store'])->name('accounts.income_categories.store');
    Route::get('accounts/income-categories/{id}/edit', [IncomeCategoryController::class,'edit'])->name('accounts.income_categories.edit');
    Route::post('accounts/income-categories/update', [IncomeCategoryController::class,'update'])->name('accounts.income_categories.update');
    Route::get('accounts/income/manage', [IncomeController::class,'index'])->name('accounts.income.index');
    Route::get('accounts/income/create', [IncomeController::class,'create'])->name('accounts.income.create');
    Route::post('accounts/income/save', [IncomeController::class,'store'])->name('accounts.income.store');
    Route::get('accounts/income/{id}/edit', [IncomeController::class,'edit'])->name('accounts.income.edit');
    Route::post('accounts/income/update', [IncomeController::class,'update'])->name('accounts.income.update');
    Route::get('accounts/journal-entries/manage', [JournalEntryController::class,'index'])->name('accounts.journal_entries.index');
    Route::get('accounts/journal-entries/create', [JournalEntryController::class,'create'])->name('accounts.journal_entries.create');
    Route::post('accounts/journal-entries/save', [JournalEntryController::class,'store'])->name('accounts.journal_entries.store');
    Route::get('accounts/ledger', [AccountsReportController::class,'ledger'])->name('accounts.ledger');
    Route::get('accounts/daily-closing', [AccountsReportController::class,'dailyClosing'])->name('accounts.daily_closing');
    Route::get('accounts/purchase-report', [AccountsReportController::class,'purchaseReport'])->name('accounts.purchase_report');
    Route::get('accounts/purchase-report/print', [AccountsReportController::class,'purchaseReportPrint'])->name('accounts.purchase_report.print');
    Route::get('accounts/supplier-master/manage', [SupplierController::class,'index'])->name('accounts.supplier_master.index');
    Route::get('accounts/supplier-master/create', [SupplierController::class,'create'])->name('accounts.supplier_master.create');
    Route::post('accounts/supplier-master/save', [SupplierController::class,'store'])->name('accounts.supplier_master.store');
    Route::get('accounts/supplier-master/{id}', [SupplierController::class,'show'])->name('accounts.supplier_master.show');
    Route::get('accounts/supplier-master/{id}/statement', [SupplierController::class,'statement'])->name('accounts.supplier_master.statement');
    Route::get('accounts/supplier-master/{id}/edit', [SupplierController::class,'edit'])->name('accounts.supplier_master.edit');
    Route::post('accounts/supplier-master/update', [SupplierController::class,'update'])->name('accounts.supplier_master.update');
    Route::get('accounts/suppliers/manage', [SupplierLedgerController::class,'index'])->name('accounts.suppliers.index');
    Route::get('accounts/suppliers/create', [SupplierLedgerController::class,'create'])->name('accounts.suppliers.create');
    Route::post('accounts/suppliers/save', [SupplierLedgerController::class,'store'])->name('accounts.suppliers.store');
    Route::get('accounts/suppliers/{id}/edit', [SupplierLedgerController::class,'edit'])->name('accounts.suppliers.edit');
    Route::post('accounts/suppliers/update', [SupplierLedgerController::class,'update'])->name('accounts.suppliers.update');
    Route::get('accounts/purchases/manage', [PurchaseController::class,'index'])->name('accounts.purchases.index');
    Route::get('accounts/purchases/create', [PurchaseController::class,'create'])->name('accounts.purchases.create');
    Route::post('accounts/purchases/save', [PurchaseController::class,'store'])->name('accounts.purchases.store');
    Route::get('accounts/purchases/{id}/edit', [PurchaseController::class,'edit'])->name('accounts.purchases.edit');
    Route::post('accounts/purchases/update', [PurchaseController::class,'update'])->name('accounts.purchases.update');
    Route::get('accounts/purchases/{id}', [PurchaseController::class,'show'])->name('accounts.purchases.show');
    Route::get('accounts/purchase-returns/manage', [PurchaseController::class,'returns'])->name('accounts.purchase_returns.index');
    Route::get('accounts/purchases/{id}/return', [PurchaseController::class,'createReturn'])->name('accounts.purchases.return.create');
    Route::post('accounts/purchase-returns/save', [PurchaseController::class,'storeReturn'])->name('accounts.purchase_returns.store');
    Route::get('accounts/supplier-payments/manage', [SupplierPaymentController::class,'index'])->name('accounts.supplier_payments.index');
    Route::get('accounts/supplier-payments/create', [SupplierPaymentController::class,'create'])->name('accounts.supplier_payments.create');
    Route::post('accounts/supplier-payments/save', [SupplierPaymentController::class,'store'])->name('accounts.supplier_payments.store');
    Route::get('inventory/dashboard', [InventoryDashboardController::class,'index'])->name('inventory.dashboard');
    Route::get('inventory/ledger', [InventoryController::class,'ledger'])->name('inventory.ledger');
    Route::get('inventory/product/{id}/movements', [InventoryController::class,'productMovements'])->name('inventory.product_movements');
    Route::get('inventory/low-stock', [InventoryController::class,'lowStock'])->name('inventory.low_stock');
    Route::get('inventory/adjustments/manage', [InventoryController::class,'adjustments'])->name('inventory.adjustments.index');
    Route::get('inventory/adjustments/create', [InventoryController::class,'createAdjustment'])->name('inventory.adjustments.create');
    Route::post('inventory/adjustments/save', [InventoryController::class,'storeAdjustment'])->name('inventory.adjustments.store');
    Route::get('accounts/customer-dues/manage', [CustomerDueController::class,'index'])->name('accounts.customer_dues.index');
    Route::get('accounts/customer-dues/create', [CustomerDueController::class,'create'])->name('accounts.customer_dues.create');
    Route::post('accounts/customer-dues/save', [CustomerDueController::class,'store'])->name('accounts.customer_dues.store');
    Route::get('accounts/customer-dues/{id}/edit', [CustomerDueController::class,'edit'])->name('accounts.customer_dues.edit');
    Route::post('accounts/customer-dues/update', [CustomerDueController::class,'update'])->name('accounts.customer_dues.update');
    Route::get('accounts/fund-transfers/manage', [FundTransferController::class,'index'])->name('accounts.fund_transfers.index');
    Route::get('accounts/fund-transfers/create', [FundTransferController::class,'create'])->name('accounts.fund_transfers.create');
    Route::post('accounts/fund-transfers/save', [FundTransferController::class,'store'])->name('accounts.fund_transfers.store');
    Route::get('accounts/return-refunds/manage', [ReturnRefundController::class,'index'])->name('accounts.return_refunds.index');
    Route::get('accounts/return-refunds/create', [ReturnRefundController::class,'create'])->name('accounts.return_refunds.create');
    Route::post('accounts/return-refunds/save', [ReturnRefundController::class,'store'])->name('accounts.return_refunds.store');
    Route::get('accounts/payment-method-report', [PaymentMethodReportController::class,'index'])->name('accounts.payment_method_report');
    Route::get('hr/dashboard', [HrDashboardController::class,'index'])->name('hr.dashboard');
    Route::get('hr/shifts/manage', [HrShiftController::class,'index'])->name('hr.shifts.index');
    Route::get('hr/shifts/create', [HrShiftController::class,'create'])->name('hr.shifts.create');
    Route::post('hr/shifts/save', [HrShiftController::class,'store'])->name('hr.shifts.store');
    Route::get('hr/shifts/{id}/edit', [HrShiftController::class,'edit'])->name('hr.shifts.edit');
    Route::post('hr/shifts/update', [HrShiftController::class,'update'])->name('hr.shifts.update');
    Route::post('hr/shifts/inactive', [HrShiftController::class,'inactive'])->name('hr.shifts.inactive');
    Route::post('hr/shifts/active', [HrShiftController::class,'active'])->name('hr.shifts.active');
    Route::post('hr/shifts/destroy', [HrShiftController::class,'destroy'])->name('hr.shifts.destroy');
    Route::get('hr/holidays/manage', [HrHolidayController::class,'index'])->name('hr.holidays.index');
    Route::get('hr/holidays/create', [HrHolidayController::class,'create'])->name('hr.holidays.create');
    Route::post('hr/holidays/save', [HrHolidayController::class,'store'])->name('hr.holidays.store');
    Route::get('hr/holidays/{id}/edit', [HrHolidayController::class,'edit'])->name('hr.holidays.edit');
    Route::post('hr/holidays/update', [HrHolidayController::class,'update'])->name('hr.holidays.update');
    Route::post('hr/holidays/inactive', [HrHolidayController::class,'inactive'])->name('hr.holidays.inactive');
    Route::post('hr/holidays/active', [HrHolidayController::class,'active'])->name('hr.holidays.active');
    Route::post('hr/holidays/destroy', [HrHolidayController::class,'destroy'])->name('hr.holidays.destroy');
    Route::get('hr/leave-types/manage', [HrLeaveTypeController::class,'index'])->name('hr.leave_types.index');
    Route::get('hr/leave-types/create', [HrLeaveTypeController::class,'create'])->name('hr.leave_types.create');
    Route::post('hr/leave-types/save', [HrLeaveTypeController::class,'store'])->name('hr.leave_types.store');
    Route::get('hr/leave-types/{id}/edit', [HrLeaveTypeController::class,'edit'])->name('hr.leave_types.edit');
    Route::post('hr/leave-types/update', [HrLeaveTypeController::class,'update'])->name('hr.leave_types.update');
    Route::post('hr/leave-types/inactive', [HrLeaveTypeController::class,'inactive'])->name('hr.leave_types.inactive');
    Route::post('hr/leave-types/active', [HrLeaveTypeController::class,'active'])->name('hr.leave_types.active');
    Route::post('hr/leave-types/destroy', [HrLeaveTypeController::class,'destroy'])->name('hr.leave_types.destroy');
    Route::get('hr/designations/manage', [HrDesignationController::class,'index'])->name('hr.designations.index');
    Route::get('hr/designations/create', [HrDesignationController::class,'create'])->name('hr.designations.create');
    Route::post('hr/designations/save', [HrDesignationController::class,'store'])->name('hr.designations.store');
    Route::get('hr/designations/{id}/edit', [HrDesignationController::class,'edit'])->name('hr.designations.edit');
    Route::post('hr/designations/update', [HrDesignationController::class,'update'])->name('hr.designations.update');
    Route::post('hr/designations/inactive', [HrDesignationController::class,'inactive'])->name('hr.designations.inactive');
    Route::post('hr/designations/active', [HrDesignationController::class,'active'])->name('hr.designations.active');
    Route::post('hr/designations/destroy', [HrDesignationController::class,'destroy'])->name('hr.designations.destroy');
    Route::get('hr/departments/manage', [HrDepartmentController::class,'index'])->name('hr.departments.index');
    Route::get('hr/departments/create', [HrDepartmentController::class,'create'])->name('hr.departments.create');
    Route::post('hr/departments/save', [HrDepartmentController::class,'store'])->name('hr.departments.store');
    Route::get('hr/departments/{id}/edit', [HrDepartmentController::class,'edit'])->name('hr.departments.edit');
    Route::post('hr/departments/update', [HrDepartmentController::class,'update'])->name('hr.departments.update');
    Route::post('hr/departments/inactive', [HrDepartmentController::class,'inactive'])->name('hr.departments.inactive');
    Route::post('hr/departments/active', [HrDepartmentController::class,'active'])->name('hr.departments.active');
    Route::post('hr/departments/destroy', [HrDepartmentController::class,'destroy'])->name('hr.departments.destroy');
    Route::get('hr/employees/manage', [HrEmployeeController::class,'index'])->name('hr.employees.index');
    Route::get('hr/employees/create', [HrEmployeeController::class,'create'])->name('hr.employees.create');
    Route::post('hr/employees/save', [HrEmployeeController::class,'store'])->name('hr.employees.store');
    Route::get('hr/employees/{id}/edit', [HrEmployeeController::class,'edit'])->name('hr.employees.edit');
    Route::get('hr/employees/{id}', [HrEmployeeController::class,'show'])->name('hr.employees.show');
    Route::post('hr/employees/update', [HrEmployeeController::class,'update'])->name('hr.employees.update');
    Route::post('hr/employees/inactive', [HrEmployeeController::class,'inactive'])->name('hr.employees.inactive');
    Route::post('hr/employees/active', [HrEmployeeController::class,'active'])->name('hr.employees.active');
    Route::post('hr/employees/destroy', [HrEmployeeController::class,'destroy'])->name('hr.employees.destroy');
    Route::get('hr/attendance/manage', [HrAttendanceController::class,'index'])->name('hr.attendance.index');
    Route::get('hr/attendance/dashboard', [HrAttendanceController::class,'dashboard'])->name('hr.attendance.dashboard');
    Route::get('hr/attendance/create', [HrAttendanceController::class,'create'])->name('hr.attendance.create');
    Route::post('hr/attendance/save', [HrAttendanceController::class,'store'])->name('hr.attendance.store');
    Route::post('hr/attendance/bulk-save', [HrAttendanceController::class,'bulkStore'])->name('hr.attendance.bulk_store');
    Route::get('hr/attendance/{id}/edit', [HrAttendanceController::class,'edit'])->name('hr.attendance.edit');
    Route::post('hr/attendance/update', [HrAttendanceController::class,'update'])->name('hr.attendance.update');
    Route::post('hr/attendance/destroy', [HrAttendanceController::class,'destroy'])->name('hr.attendance.destroy');
    Route::get('hr/leave/manage', [HrLeaveController::class,'index'])->name('hr.leave.index');
    Route::get('hr/leave/balance', [HrLeaveController::class,'balance'])->name('hr.leave.balance');
    Route::get('hr/leave/create', [HrLeaveController::class,'create'])->name('hr.leave.create');
    Route::post('hr/leave/save', [HrLeaveController::class,'store'])->name('hr.leave.store');
    Route::get('hr/leave/{id}/edit', [HrLeaveController::class,'edit'])->name('hr.leave.edit');
    Route::post('hr/leave/update', [HrLeaveController::class,'update'])->name('hr.leave.update');
    Route::post('hr/leave/destroy', [HrLeaveController::class,'destroy'])->name('hr.leave.destroy');
    Route::get('hr/payroll/manage', [HrPayrollController::class,'index'])->name('hr.payroll.index');
    Route::get('hr/payroll/create', [HrPayrollController::class,'create'])->name('hr.payroll.create');
    Route::get('hr/payroll/preview', [HrPayrollController::class,'preview'])->name('hr.payroll.preview');
    Route::post('hr/payroll/save', [HrPayrollController::class,'store'])->name('hr.payroll.store');
    Route::get('hr/payroll/{id}/edit', [HrPayrollController::class,'edit'])->name('hr.payroll.edit');
    Route::get('hr/payroll/{id}/payslip', [HrPayrollController::class,'payslip'])->name('hr.payroll.payslip');
    Route::post('hr/payroll/update', [HrPayrollController::class,'update'])->name('hr.payroll.update');
    Route::post('hr/payroll/destroy', [HrPayrollController::class,'destroy'])->name('hr.payroll.destroy');
    Route::get('hr/salary-structures/manage', [HrSalaryStructureController::class,'index'])->name('hr.salary_structures.index');
    Route::get('hr/salary-structures/create', [HrSalaryStructureController::class,'create'])->name('hr.salary_structures.create');
    Route::post('hr/salary-structures/save', [HrSalaryStructureController::class,'store'])->name('hr.salary_structures.store');
    Route::get('hr/salary-structures/{id}/edit', [HrSalaryStructureController::class,'edit'])->name('hr.salary_structures.edit');
    Route::post('hr/salary-structures/update', [HrSalaryStructureController::class,'update'])->name('hr.salary_structures.update');
    Route::post('hr/salary-structures/inactive', [HrSalaryStructureController::class,'inactive'])->name('hr.salary_structures.inactive');
    Route::post('hr/salary-structures/active', [HrSalaryStructureController::class,'active'])->name('hr.salary_structures.active');
    Route::post('hr/salary-structures/destroy', [HrSalaryStructureController::class,'destroy'])->name('hr.salary_structures.destroy');
    Route::get('hr/advances/manage', [HrAdvanceController::class,'index'])->name('hr.advances.index');
    Route::get('hr/advances/create', [HrAdvanceController::class,'create'])->name('hr.advances.create');
    Route::post('hr/advances/save', [HrAdvanceController::class,'store'])->name('hr.advances.store');
    Route::get('hr/advances/{id}/edit', [HrAdvanceController::class,'edit'])->name('hr.advances.edit');
    Route::post('hr/advances/update', [HrAdvanceController::class,'update'])->name('hr.advances.update');
    Route::post('hr/advances/destroy', [HrAdvanceController::class,'destroy'])->name('hr.advances.destroy');
    Route::get('hr/bonuses/manage', [HrBonusController::class,'index'])->name('hr.bonuses.index');
    Route::get('hr/bonuses/create', [HrBonusController::class,'create'])->name('hr.bonuses.create');
    Route::post('hr/bonuses/save', [HrBonusController::class,'store'])->name('hr.bonuses.store');
    Route::get('hr/bonuses/{id}/edit', [HrBonusController::class,'edit'])->name('hr.bonuses.edit');
    Route::post('hr/bonuses/update', [HrBonusController::class,'update'])->name('hr.bonuses.update');
    Route::post('hr/bonuses/inactive', [HrBonusController::class,'inactive'])->name('hr.bonuses.inactive');
    Route::post('hr/bonuses/active', [HrBonusController::class,'active'])->name('hr.bonuses.active');
    Route::post('hr/bonuses/destroy', [HrBonusController::class,'destroy'])->name('hr.bonuses.destroy');
    Route::get('hr/documents/manage', [HrDocumentController::class,'index'])->name('hr.documents.index');
    Route::get('hr/documents/create', [HrDocumentController::class,'create'])->name('hr.documents.create');
    Route::post('hr/documents/save', [HrDocumentController::class,'store'])->name('hr.documents.store');
    Route::get('hr/documents/{id}/edit', [HrDocumentController::class,'edit'])->name('hr.documents.edit');
    Route::post('hr/documents/update', [HrDocumentController::class,'update'])->name('hr.documents.update');
    Route::post('hr/documents/inactive', [HrDocumentController::class,'inactive'])->name('hr.documents.inactive');
    Route::post('hr/documents/active', [HrDocumentController::class,'active'])->name('hr.documents.active');
    Route::post('hr/documents/destroy', [HrDocumentController::class,'destroy'])->name('hr.documents.destroy');
    Route::get('hr/notices/manage', [HrNoticeController::class,'index'])->name('hr.notices.index');
    Route::get('hr/notices/create', [HrNoticeController::class,'create'])->name('hr.notices.create');
    Route::post('hr/notices/save', [HrNoticeController::class,'store'])->name('hr.notices.store');
    Route::get('hr/notices/{id}/edit', [HrNoticeController::class,'edit'])->name('hr.notices.edit');
    Route::post('hr/notices/update', [HrNoticeController::class,'update'])->name('hr.notices.update');
    Route::post('hr/notices/inactive', [HrNoticeController::class,'inactive'])->name('hr.notices.inactive');
    Route::post('hr/notices/active', [HrNoticeController::class,'active'])->name('hr.notices.active');
    Route::post('hr/notices/destroy', [HrNoticeController::class,'destroy'])->name('hr.notices.destroy');
    Route::get('hr/separations/manage', [HrSeparationController::class,'index'])->name('hr.separations.index');
    Route::get('hr/separations/create', [HrSeparationController::class,'create'])->name('hr.separations.create');
    Route::post('hr/separations/save', [HrSeparationController::class,'store'])->name('hr.separations.store');
    Route::get('hr/separations/{id}/edit', [HrSeparationController::class,'edit'])->name('hr.separations.edit');
    Route::post('hr/separations/update', [HrSeparationController::class,'update'])->name('hr.separations.update');
    Route::post('hr/separations/destroy', [HrSeparationController::class,'destroy'])->name('hr.separations.destroy');
    Route::get('hr/performance-notes/manage', [HrPerformanceNoteController::class,'index'])->name('hr.performance_notes.index');
    Route::get('hr/performance-notes/create', [HrPerformanceNoteController::class,'create'])->name('hr.performance_notes.create');
    Route::post('hr/performance-notes/save', [HrPerformanceNoteController::class,'store'])->name('hr.performance_notes.store');
    Route::get('hr/performance-notes/{id}/edit', [HrPerformanceNoteController::class,'edit'])->name('hr.performance_notes.edit');
    Route::post('hr/performance-notes/update', [HrPerformanceNoteController::class,'update'])->name('hr.performance_notes.update');
    Route::post('hr/performance-notes/inactive', [HrPerformanceNoteController::class,'inactive'])->name('hr.performance_notes.inactive');
    Route::post('hr/performance-notes/active', [HrPerformanceNoteController::class,'active'])->name('hr.performance_notes.active');
    Route::post('hr/performance-notes/destroy', [HrPerformanceNoteController::class,'destroy'])->name('hr.performance_notes.destroy');
    Route::get('/pathao-city', [OrderController::class, 'pathaocity'])->name('pathaocity');
    Route::get('/pathao-zone', [OrderController::class, 'pathaozone'])->name('pathaozone');

    // Order route
    Route::get('reviews', [ReviewController::class,'index'])->name('reviews.index');
    Route::get('review/pending', [ReviewController::class,'pending'])->name('reviews.pending');
     Route::post('review/inactive', [ReviewController::class,'inactive'])->name('reviews.inactive');
    Route::post('review/active', [ReviewController::class,'active'])->name('reviews.active');
     Route::get('review/create', [ReviewController::class,'create'])->name('reviews.create');
    Route::post('review/save', [ReviewController::class,'store'])->name('reviews.store');
    Route::get('review/{id}/edit', [ReviewController::class,'edit'])->name('reviews.edit');
    Route::post('review/update', [ReviewController::class,'update'])->name('reviews.update');
    Route::post('review/destroy', [ReviewController::class,'destroy'])->name('reviews.destroy');

    // flavor  route
    Route::get('shipping-charge/manage', [ShippingChargeController::class,'index'])->name('shippingcharges.index');
    Route::get('shipping-charge/create', [ShippingChargeController::class,'create'])->name('shippingcharges.create');
    Route::post('shipping-charge/save', [ShippingChargeController::class,'store'])->name('shippingcharges.store');
    Route::get('shipping-charge/{id}/edit', [ShippingChargeController::class,'edit'])->name('shippingcharges.edit');
    Route::post('shipping-charge/update', [ShippingChargeController::class,'update'])->name('shippingcharges.update');
    Route::post('shipping-charge/inactive', [ShippingChargeController::class,'inactive'])->name('shippingcharges.inactive');
    Route::post('shipping-charge/active', [ShippingChargeController::class,'active'])->name('shippingcharges.active');
    Route::post('shipping-charge/destroy', [ShippingChargeController::class,'destroy'])->name('shippingcharges.destroy');

    // backend customer route

    Route::get('customer/manage', [CustomerManageController::class,'index'])->name('customers.index');
    Route::get('customer/{id}/edit', [CustomerManageController::class,'edit'])->name('customers.edit');
    Route::post('customer/update', [CustomerManageController::class,'update'])->name('customers.update');
    Route::post('customer/inactive', [CustomerManageController::class,'inactive'])->name('customers.inactive');
    Route::post('customer/active', [CustomerManageController::class,'active'])->name('customers.active');
    Route::get('customer/profile', [CustomerManageController::class,'profile'])->name('customers.profile');
    Route::post('customer/adminlog', [CustomerManageController::class,'adminlog'])->name('customers.adminlog');
    Route::get('customer/ip-block', [CustomerManageController::class,'ip_block'])->name('customers.ip_block');
    Route::post('customer/ip-store', [CustomerManageController::class,'ipblock_store'])->name('customers.ipblock.store');
    Route::post('customer/ip-update', [CustomerManageController::class,'ipblock_update'])->name('customers.ipblock.update');
    Route::post('customer/ip-destroy', [CustomerManageController::class,'ipblock_destroy'])->name('customers.ipblock.destroy');

      // coupon code route
    Route::get('coupon-code/manage', [CouponCodeController::class,'index'])->name('couponcodes.index');
    Route::get('coupon-code/create', [CouponCodeController::class,'create'])->name('couponcodes.create');
    Route::post('coupon-code/save', [CouponCodeController::class,'store'])->name('couponcodes.store');
    Route::get('coupon-code/{id}/edit', [CouponCodeController::class,'edit'])->name('couponcodes.edit');
    Route::post('coupon-code/update', [CouponCodeController::class,'update'])->name('couponcodes.update');
    Route::post('coupon-code/inactive', [CouponCodeController::class,'inactive'])->name('couponcodes.inactive');
    Route::post('coupon-code/active', [CouponCodeController::class,'active'])->name('couponcodes.active');
    Route::post('coupon-code/destroy', [CouponCodeController::class,'destroy'])->name('couponcodes.destroy');


    // Attribute


    Route::get('attribute/manage', [AtrributeListController::class,'index'])->name('attribute.index');
    Route::get('attribute/{id}/show', [AtrributeListController::class,'show'])->name('attribute.show');
    Route::get('attribute/create', [AtrributeListController::class,'create'])->name('attribute.create');
    Route::post('attribute/save', [AtrributeListController::class,'store'])->name('attribute.store');
    Route::get('attribute/{id}/edit', [AtrributeListController::class,'edit'])->name('attribute.edit');
    Route::post('attribute/update', [AtrributeListController::class,'update'])->name('attribute.update');
    Route::post('attribute/inactive', [AtrributeListController::class,'inactive'])->name('attribute.inactive');
    Route::post('attribute/active', [AtrributeListController::class,'active'])->name('attribute.active');
    Route::post('attribute/destroy', [AtrributeListController::class,'destroy'])->name('attribute.destroy');



    // value

    Route::get('value/manage', [ValueController::class,'index'])->name('value.index');
    Route::get('value/{id}/show', [ValueController::class,'show'])->name('value.show');
    Route::get('value/create', [ValueController::class,'create'])->name('value.create');
    Route::post('value/save', [ValueController::class,'store'])->name('value.store');
    Route::get('value/{id}/edit', [ValueController::class,'edit'])->name('value.edit');
    Route::post('value/update', [ValueController::class,'update'])->name('value.update');
    Route::post('value/inactive', [ValueController::class,'inactive'])->name('value.inactive');
    Route::post('value/active', [ValueController::class,'active'])->name('value.active');
    Route::post('value/destroy', [ValueController::class,'destroy'])->name('value.destroy');
    Route::post('value/bulk-destroy', [ValueController::class,'bulkDestroy'])->name('value.bulk_destroy');

    // Weight
    Route::get('weight/manage', [WeightController::class,'index'])->name('weight.index');
    Route::get('weight/{id}/show', [WeightController::class,'show'])->name('weight.show');
    Route::get('weight/create', [WeightController::class,'create'])->name('weight.create');
    Route::post('weight/save', [WeightController::class,'store'])->name('weight.store');
    Route::get('weight/{id}/edit', [WeightController::class,'edit'])->name('weight.edit');
    Route::post('weight/update', [WeightController::class,'update'])->name('weight.update');
    Route::post('weight/inactive', [WeightController::class,'inactive'])->name('weight.inactive');
    Route::post('weight/active', [WeightController::class,'active'])->name('weight.active');
    Route::post('weight/destroy', [WeightController::class,'destroy'])->name('weight.destroy');

    // Weight
    Route::get('model/manage', [AdminModelController::class,'index'])->name('model.index');
    Route::get('model/{id}/show', [AdminModelController::class,'show'])->name('model.show');
    Route::get('model/create', [AdminModelController::class,'create'])->name('model.create');
    Route::post('model/save', [AdminModelController::class,'store'])->name('model.store');
    Route::get('model/{id}/edit', [AdminModelController::class,'edit'])->name('model.edit');
    Route::post('model/update', [AdminModelController::class,'update'])->name('model.update');
    Route::post('model/inactive', [AdminModelController::class,'inactive'])->name('model.inactive');
    Route::post('model/active', [AdminModelController::class,'active'])->name('model.active');
    Route::post('model/destroy', [AdminModelController::class,'destroy'])->name('model.destroy');

    Route::get('header/manage', [TopHeaderController::class,'index'])->name('header.index');
    Route::get('header/{id}/show', [TopHeaderController::class,'show'])->name('header.show');
    Route::get('header/create', [TopHeaderController::class,'create'])->name('header.create');
    Route::post('header/save', [TopHeaderController::class,'store'])->name('header.store');
    Route::get('header/{id}/edit', [TopHeaderController::class,'edit'])->name('header.edit');
    Route::post('header/update', [TopHeaderController::class,'update'])->name('header.update');
    Route::post('header/inactive', [TopHeaderController::class,'inactive'])->name('header.inactive');
    Route::post('header/active', [TopHeaderController::class,'active'])->name('header.active');
    Route::post('header/destroy', [TopHeaderController::class,'destroy'])->name('header.destroy');

    Route::get('store/manage', [StoreController::class,'index'])->name('store.index');
    Route::get('store/{id}/show', [StoreController::class,'show'])->name('store.show');
    Route::get('store/create', [StoreController::class,'create'])->name('store.create');
    Route::post('store/save', [StoreController::class,'store'])->name('store.store');
    Route::get('store/{id}/edit', [StoreController::class,'edit'])->name('store.edit');
    Route::post('store/update', [StoreController::class,'update'])->name('store.update');
    Route::post('store/inactive', [StoreController::class,'inactive'])->name('store.inactive');
    Route::post('store/active', [StoreController::class,'active'])->name('store.active');
    Route::post('store/destroy', [StoreController::class,'destroy'])->name('store.destroy');


    Route::post('/summernote/upload', [SummernoteController::class, 'upload'])->name('summernote.upload');

    // Vendor management (admin)
    Route::get('vendors', [AdminVendorController::class, 'index'])->name('admin.vendors.index');
    Route::get('vendors/{id}/show', [AdminVendorController::class, 'show'])->name('admin.vendors.show');
    Route::post('vendors/approve', [AdminVendorController::class, 'approve'])->name('admin.vendors.approve');
    Route::post('vendors/suspend', [AdminVendorController::class, 'suspend'])->name('admin.vendors.suspend');
    Route::post('vendors/commission', [AdminVendorController::class, 'updateCommission'])->name('admin.vendors.commission');
    Route::post('vendors/destroy', [AdminVendorController::class, 'destroy'])->name('admin.vendors.destroy');

    // Vendor withdrawals (admin)
    Route::get('vendor-withdrawals', [AdminVendorWithdrawController::class, 'index'])->name('admin.vendor.withdrawals');
    Route::post('vendor-withdrawals/process', [AdminVendorWithdrawController::class, 'process'])->name('admin.vendor.withdrawals.process');

    // Hub: receiving collected items from vendors + hub stock (admin)
    Route::get('hub/receiving', [AdminHubController::class, 'receiving'])->name('admin.hub.receiving');
    Route::post('hub/receive', [AdminHubController::class, 'receive'])->name('admin.hub.receive');
    Route::get('hub/stock', [AdminHubController::class, 'stock'])->name('admin.hub.stock');

    // Reseller management (admin)
    Route::get('resellers', [AdminResellerController::class, 'index'])->name('admin.resellers.index');
    Route::get('resellers/withdrawals', [AdminResellerController::class, 'withdrawals'])->name('admin.resellers.withdrawals');
    Route::post('resellers/withdraw-status', [AdminResellerController::class, 'withdrawStatus'])->name('admin.resellers.withdraw_status');
    Route::get('resellers/{id}/show', [AdminResellerController::class, 'show'])->name('admin.resellers.show');
    Route::post('resellers/approve', [AdminResellerController::class, 'approve'])->name('admin.resellers.approve');
    Route::post('resellers/suspend', [AdminResellerController::class, 'suspend'])->name('admin.resellers.suspend');
    Route::post('resellers/margin', [AdminResellerController::class, 'updateMargin'])->name('admin.resellers.margin');
    Route::post('resellers/destroy', [AdminResellerController::class, 'destroy'])->name('admin.resellers.destroy');

    // Reseller Support Tickets
    Route::get('reseller-tickets', [AdminResellerTicketController::class, 'index'])->name('admin.reseller_tickets.index');
    Route::get('reseller-tickets/{id}', [AdminResellerTicketController::class, 'show'])->name('admin.reseller_tickets.show');
    Route::get('reseller-tickets/{id}/messages', [AdminResellerTicketController::class, 'messages'])->name('admin.reseller_tickets.messages');
    Route::post('reseller-tickets/{id}/reply', [AdminResellerTicketController::class, 'reply'])->name('admin.reseller_tickets.reply');
    Route::post('reseller-tickets/{id}/status', [AdminResellerTicketController::class, 'status'])->name('admin.reseller_tickets.status');

});


/*
|--------------------------------------------------------------------------
| Vendor Panel (guard: vendor)
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'vendor', 'middleware' => ['ipcheck', 'check_refer']], function () {
    // guest
    Route::get('/login', [VendorAuthController::class, 'login'])->name('vendor.login');
    Route::post('/signin', [VendorAuthController::class, 'signin'])->name('vendor.signin');
    Route::get('/register', [VendorAuthController::class, 'register'])->name('vendor.register');
    Route::post('/store', [VendorAuthController::class, 'store'])->name('vendor.store');
});

/*
|--------------------------------------------------------------------------
| Reseller Panel (guard: reseller)
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'reseller', 'middleware' => ['ipcheck', 'check_refer']], function () {
    Route::get('/login', [ResellerAuthController::class, 'login'])->name('reseller.login');
    Route::post('/signin', [ResellerAuthController::class, 'signin'])->name('reseller.signin');
    Route::get('/register', [ResellerAuthController::class, 'register'])->name('reseller.register');
    Route::post('/store', [ResellerAuthController::class, 'store'])->name('reseller.store');
});

Route::group(['prefix' => 'reseller', 'middleware' => ['reseller', 'ipcheck', 'check_refer']], function () {
    Route::get('/dashboard', [ResellerDashboardController::class, 'dashboard'])->name('reseller.dashboard');
    Route::get('/products', [ResellerProductController::class, 'index'])->name('reseller.products.index');
    Route::get('/products/details/{id}', [ResellerProductController::class, 'details'])->name('reseller.products.details');
    Route::get('/products/{id}/download/{index}', [ResellerProductController::class, 'downloadImage'])->name('reseller.products.download');

    // Favourites (wishlist)
    Route::get('/favourites', [ResellerFavouriteController::class, 'index'])->name('reseller.favourites.index');
    Route::post('/favourites/toggle', [ResellerFavouriteController::class, 'toggle'])->name('reseller.favourites.toggle');

    // Cart
    Route::post('/cart/add', [ResellerCartController::class, 'add'])->name('reseller.order.add');
    Route::get('/cart', [ResellerCartController::class, 'index'])->name('reseller.cart.index');
    Route::post('/cart/update', [ResellerCartController::class, 'update'])->name('reseller.cart.update');
    Route::post('/cart/remove', [ResellerCartController::class, 'remove'])->name('reseller.cart.remove');
    Route::post('/cart/clear', [ResellerCartController::class, 'clear'])->name('reseller.cart.clear');

    // Checkout / Orders
    Route::get('/checkout', [ResellerCheckoutController::class, 'checkout'])->name('reseller.checkout');
    Route::post('/order/place', [ResellerCheckoutController::class, 'placeOrder'])->name('reseller.orders.place');
    Route::get('/order/success/{id}', [ResellerCheckoutController::class, 'success'])->name('reseller.orders.success');
    Route::get('/orders', [ResellerCheckoutController::class, 'myOrders'])->name('reseller.orders.index');
    Route::get('/orders/{id}', [ResellerCheckoutController::class, 'show'])->name('reseller.orders.show');
    Route::post('/orders/fraud-check', [ResellerCheckoutController::class, 'fraudCheck'])->name('reseller.orders.fraud_check');
    Route::get('/orders/{id}/edit', [ResellerCheckoutController::class, 'edit'])->name('reseller.orders.edit');
    Route::post('/orders/{id}/update', [ResellerCheckoutController::class, 'update'])->name('reseller.orders.update');
    Route::post('/orders/{id}/cancel', [ResellerCheckoutController::class, 'cancel'])->name('reseller.orders.cancel');

    // Support Tickets
    Route::get('/tickets', [ResellerTicketController::class, 'index'])->name('reseller.tickets.index');
    Route::get('/tickets/create', [ResellerTicketController::class, 'create'])->name('reseller.tickets.create');
    Route::post('/tickets', [ResellerTicketController::class, 'store'])->name('reseller.tickets.store');
    Route::get('/tickets/{id}', [ResellerTicketController::class, 'show'])->name('reseller.tickets.show');
    Route::get('/tickets/{id}/messages', [ResellerTicketController::class, 'messages'])->name('reseller.tickets.messages');
    Route::post('/tickets/{id}/reply', [ResellerTicketController::class, 'reply'])->name('reseller.tickets.reply');

    // Withdraw
    Route::get('/withdraw', [ResellerWithdrawController::class, 'index'])->name('reseller.withdraw.index');
    Route::post('/withdraw', [ResellerWithdrawController::class, 'store'])->name('reseller.withdraw.store');
    Route::get('/profile', [ResellerDashboardController::class, 'profile'])->name('reseller.profile');
    Route::post('/profile/update', [ResellerDashboardController::class, 'profileUpdate'])->name('reseller.profile.update');
    Route::post('/profile/password', [ResellerDashboardController::class, 'passwordUpdate'])->name('reseller.password.update');
    Route::get('/payment-methods', [ResellerDashboardController::class, 'paymentMethods'])->name('reseller.payment_methods.index');
    Route::post('/profile/payment-method/add', [ResellerDashboardController::class, 'addPaymentMethod'])->name('reseller.payment_method.add');
    Route::post('/profile/payment-method/delete', [ResellerDashboardController::class, 'deletePaymentMethod'])->name('reseller.payment_method.delete');
    Route::post('/logout', [ResellerAuthController::class, 'logout'])->name('reseller.logout');
});

Route::group(['prefix' => 'vendor', 'middleware' => ['vendor', 'ipcheck', 'check_refer']], function () {
    Route::get('/dashboard', [VendorDashboardController::class, 'dashboard'])->name('vendor.dashboard');
    Route::get('/profile', [VendorDashboardController::class, 'profile'])->name('vendor.profile');
    Route::post('/profile/update', [VendorDashboardController::class, 'profileUpdate'])->name('vendor.profile.update');
    Route::post('/profile/password', [VendorDashboardController::class, 'passwordUpdate'])->name('vendor.password.update');
    Route::post('/logout', [VendorAuthController::class, 'logout'])->name('vendor.logout');

    // Products — vendor sudhu list, stock, status on/off (add/edit admin kore)
    Route::get('/products', [VendorProductController::class, 'index'])->name('vendor.products.index');
    Route::post('/products/stock', [VendorProductController::class, 'updateStock'])->name('vendor.products.stock');
    Route::post('/products/toggle', [VendorProductController::class, 'toggleStatus'])->name('vendor.products.toggle');
    Route::post('/products/variation-toggle', [VendorProductController::class, 'toggleVariable'])->name('vendor.products.variation_toggle');

    // Orders & Collections
    Route::get('/collection', [VendorOrderController::class, 'collection'])->name('vendor.collection');
    Route::get('/pending-summary', [VendorOrderController::class, 'pendingSummary'])->name('vendor.pending_summary');
    Route::get('/collected', [VendorOrderController::class, 'collected'])->name('vendor.collected');
    Route::post('/collection/mark-collected', [VendorOrderController::class, 'markCollected'])->name('vendor.collection.mark');
    Route::post('/collection/uncollect', [VendorOrderController::class, 'unmarkCollected'])->name('vendor.collection.uncollect');
    Route::get('/returns', [VendorOrderController::class, 'returns'])->name('vendor.returns');
    Route::post('/returns/receive', [VendorOrderController::class, 'markReturnReceived'])->name('vendor.returns.receive');
    Route::get('/payment', [VendorDashboardController::class, 'payment'])->name('vendor.payment');
    Route::post('/payment/withdraw', [VendorDashboardController::class, 'withdrawRequest'])->name('vendor.withdraw.request');
    Route::post('/payout-methods', [VendorDashboardController::class, 'storePayoutMethod'])->name('vendor.payout.store');
    Route::post('/payout-methods/default', [VendorDashboardController::class, 'setDefaultPayoutMethod'])->name('vendor.payout.default');
    Route::post('/payout-methods/delete', [VendorDashboardController::class, 'deletePayoutMethod'])->name('vendor.payout.delete');
    Route::get('/recent-post', [VendorProductController::class, 'recentPost'])->name('vendor.recent_post');

    Route::get('/orders/{slug}', [VendorOrderController::class, 'index'])->name('vendor.orders.index');
    Route::get('/orders/show/{id}', [VendorOrderController::class, 'show'])->name('vendor.orders.show');
});
