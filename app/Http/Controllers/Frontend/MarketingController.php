<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCartLead;
use App\Models\MarketingEventLog;
use App\Models\MarketingToolConfig;
use App\Models\Product;
use App\Models\VisitorAnalytic;
use App\Services\Marketing\VisitorLocationService;
use Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MarketingController extends Controller
{
    public function __construct(
        private readonly VisitorLocationService $visitorLocationService
    ) {
    }

    public function merchantFeed()
    {
        $config = Schema::hasTable('marketing_tool_configs')
            ? MarketingToolConfig::first()
            : null;

        abort_if(! $config || ! $config->merchant_feed_enabled, 404);

        $products = Product::with(['image', 'brand', 'category'])
            ->select('id', 'name', 'slug', 'meta_description', 'new_price', 'stock', 'brand_id', 'status', 'category_id', 'is_catalog')
            ->where('status', 1)
            ->where('is_catalog', 1)
            ->latest('id')
            ->get();

        return response()
            ->view('frontEnd.marketing.merchant-feed', compact('products', 'config'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function syncAbandonedCart(Request $request)
    {
        if (! Schema::hasTable('marketing_tool_configs') || ! Schema::hasTable('abandoned_cart_leads')) {
            return response()->json(['success' => false, 'message' => 'Marketing tables are not ready'], 422);
        }

        $config = MarketingToolConfig::first();

        if (! $config || ! $config->abandoned_cart_enabled) {
            return response()->json(['success' => false, 'message' => 'Abandoned cart tracking is disabled'], 422);
        }

        $cartContent = Cart::instance('shopping')->content();
        $sessionId = $request->session()->getId();
        $customer = Auth::guard('customer')->user();
        $cartTotal = (float) str_replace(',', '', Cart::instance('shopping')->subtotal());

        $lead = AbandonedCartLead::firstOrNew(['session_id' => $sessionId]);
        $lead->customer_id = $customer?->id;
        $lead->recovery_token = $lead->recovery_token ?: Str::random(40);
        $lead->name = $request->input('name') ?: ($customer?->name ?: $lead->name);
        $lead->phone = $request->input('phone') ?: ($customer?->phone ?: $lead->phone);
        $lead->email = $request->input('email') ?: ($customer?->email ?: $lead->email);
        $lead->cart_payload = json_encode($cartContent->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'qty' => $item->qty,
                'price' => $item->price,
                'slug' => $item->options->slug ?? null,
                'options' => method_exists($item->options, 'toArray') ? $item->options->toArray() : (array) $item->options,
            ];
        })->values()->all(), JSON_UNESCAPED_UNICODE);
        $lead->cart_count = (int) $cartContent->count();
        $lead->cart_total = $cartTotal;
        $lead->landing_url = $request->input('landing_url');
        $lead->utm_source = $request->input('utm_source');
        $lead->utm_medium = $request->input('utm_medium');
        $lead->utm_campaign = $request->input('utm_campaign');
        $lead->utm_term = $request->input('utm_term');
        $lead->utm_content = $request->input('utm_content');
        $lead->last_activity_at = now();
        $lead->status = $lead->cart_count > 0 ? 'active' : 'cleared';
        $lead->save();

        return response()->json(['success' => true]);
    }

    public function logVisitor(Request $request)
    {
        if (! Schema::hasTable('marketing_tool_configs') || ! Schema::hasTable('visitor_analytics')) {
            return response()->json(['success' => false, 'message' => 'Visitor analytics tables are not ready'], 422);
        }

        $config = MarketingToolConfig::where('status', 1)->first();

        if (! $config) {
            return response()->json(['success' => false, 'message' => 'Marketing suite is disabled'], 422);
        }

        $sessionId = $request->session()->getId();
        $pagePath = '/' . ltrim((string) $request->input('page_path', '/'), '/');
        $pageUrl = $request->input('page_url');
        $referrerUrl = $request->input('referrer_url');
        $ipAddress = $this->getClientIp($request);
        $location = $this->visitorLocationService->resolve($ipAddress, $request->headers->all());
        $visitDate = now()->toDateString();

        $record = VisitorAnalytic::query()->firstOrNew([
            'session_id' => $sessionId,
            'page_path' => $pagePath,
            'visit_date' => $visitDate,
        ]);

        $record->ip_address = $ipAddress;
        $record->country = $location['country'] ?? $record->country;
        $record->region = $location['region'] ?? $record->region;
        $record->district = $location['district'] ?? $record->district;
        $record->city = $location['city'] ?? $record->city;
        $record->page_url = $pageUrl ?: $record->page_url;
        $record->referrer_url = $referrerUrl ?: $record->referrer_url;
        $record->device_type = $this->resolveDeviceType((string) $request->userAgent());
        $record->first_seen_at = $record->first_seen_at ?: now();
        $record->last_seen_at = now();
        $record->view_count = $record->exists ? ((int) $record->view_count + 1) : 1;
        $record->save();

        return response()->json([
            'success' => true,
            'district' => $record->district,
        ]);
    }

    public function recoverCart(string $token)
    {
        abort_if(! Schema::hasTable('abandoned_cart_leads'), 404);

        $lead = AbandonedCartLead::where('recovery_token', $token)->firstOrFail();
        $items = json_decode($lead->cart_payload ?: '[]', true);

        Cart::instance('shopping')->destroy();

        foreach ($items as $item) {
            Cart::instance('shopping')->add([
                'id' => $item['id'] ?? null,
                'name' => $item['name'] ?? 'Product',
                'qty' => $item['qty'] ?? 1,
                'price' => $item['price'] ?? 0,
                'options' => $item['options'] ?? [],
            ]);
        }

        $lead->update([
            'status' => 'recovered',
            'recovered_at' => now(),
        ]);

        return redirect()->route('customer.checkout');
    }

    public function logGoogleEvent(Request $request)
    {
        if (! Schema::hasTable('marketing_event_logs')) {
            return response()->json(['success' => false], 422);
        }

        MarketingEventLog::create([
            'provider' => 'google',
            'event_name' => $request->input('event_name', 'unknown'),
            'event_id' => $request->input('event_id'),
            'status' => 'success',
            'order_id' => $request->input('order_id'),
            'value' => $request->input('value'),
            'currency' => $request->input('currency'),
            'source_url' => $request->input('source_url', url()->current()),
            'payload' => json_encode($request->all(), JSON_UNESCAPED_UNICODE),
        ]);

        return response()->json(['success' => true]);
    }

    protected function getClientIp(Request $request): string
    {
        $forwardedFor = $request->header('X-Forwarded-For');

        if ($forwardedFor) {
            return trim(explode(',', $forwardedFor)[0]);
        }

        $realIp = $request->header('X-Real-IP');

        if ($realIp) {
            return trim($realIp);
        }

        return (string) $request->ip();
    }

    protected function resolveDeviceType(string $userAgent): string
    {
        $agent = strtolower($userAgent);

        if (str_contains($agent, 'tablet') || str_contains($agent, 'ipad')) {
            return 'tablet';
        }

        if (str_contains($agent, 'mobile') || str_contains($agent, 'android') || str_contains($agent, 'iphone')) {
            return 'mobile';
        }

        return 'desktop';
    }
}
