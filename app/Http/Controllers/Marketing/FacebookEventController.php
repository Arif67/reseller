<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\EcomPixel;
use App\Models\MarketingEventLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class FacebookEventController extends Controller
{
    protected ?EcomPixel $pixel = null;

    public function __construct()
    {
        $query = EcomPixel::query()->where('status', 1);

        if (Schema::hasColumn('ecom_pixels', 'provider')) {
            $query->whereIn('provider', ['facebook', 'meta']);
        }

        $this->pixel = $query->latest('id')->first();
    }

    protected function isConfiguredForBrowserPixel(): bool
    {
        return ! empty($this->pixel?->code);
    }

    protected function isConfiguredForCapi(): bool
    {
        return ! empty($this->pixel?->code) && ! empty($this->pixel?->access_token);
    }

    protected function buildUserData(Request $request, array $extra = []): array
    {
        $userData = array_filter(array_merge([
            'client_ip_address' => $request->input('client_ip_address', $request->ip()),
            'client_user_agent' => $request->input('client_user_agent', $request->userAgent()),
            'fbp' => $request->input('fbp'),
            'fbc' => $request->input('fbc'),
        ], $extra), fn ($value) => $value !== null && $value !== '');

        return $userData;
    }

    protected function sendEvent(array $event): array
    {
        if (! $this->isConfiguredForCapi()) {
            return [
                'success' => false,
                'status' => 422,
                'message' => 'Facebook Conversion API is not configured',
                'response' => null,
            ];
        }

        $payload = ['data' => [$event]];

        if (! empty($this->pixel?->test_event_code)) {
            $payload['test_event_code'] = $this->pixel->test_event_code;
        }

        $response = Http::post(
            "https://graph.facebook.com/v20.0/{$this->pixel->code}/events?access_token={$this->pixel->access_token}",
            $payload
        );

        Log::info('Facebook CAPI response', [
            'event_name' => $event['event_name'] ?? null,
            'status' => $response->status(),
            'response' => $response->json() ?: $response->body(),
        ]);

        if (Schema::hasTable('marketing_event_logs')) {
            MarketingEventLog::create([
                'provider' => 'facebook',
                'event_name' => $event['event_name'] ?? 'unknown',
                'event_id' => $event['event_id'] ?? null,
                'status' => $response->successful() ? 'success' : 'failed',
                'order_id' => $event['custom_data']['order_id'] ?? null,
                'value' => $event['custom_data']['value'] ?? null,
                'currency' => $event['custom_data']['currency'] ?? null,
                'source_url' => $event['event_source_url'] ?? null,
                'payload' => json_encode([
                    'request' => $event,
                    'response' => $response->json() ?: $response->body(),
                ], JSON_UNESCAPED_UNICODE),
            ]);
        }

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'message' => $response->successful() ? 'Facebook event sent successfully' : 'Facebook event request failed',
            'response' => $response->json() ?: $response->body(),
        ];
    }

    public function pageViewCAPI(Request $request)
    {
        $result = $this->sendEvent([
            'event_name' => 'PageView',
            'event_time' => $request->input('event_time', time()),
            'event_id' => $request->input('event_id'),
            'action_source' => 'website',
            'event_source_url' => $request->input('event_source_url', url()->current()),
            'user_data' => $this->buildUserData($request),
        ]);

        return response()->json($result, $result['status']);
    }

    public function viewContent(Request $request)
    {
        $result = $this->sendEvent([
            'event_name' => 'ViewContent',
            'event_time' => time(),
            'event_id' => $request->input('event_id'),
            'action_source' => 'website',
            'event_source_url' => $request->input('event_source_url', $request->headers->get('referer')),
            'user_data' => $this->buildUserData($request),
            'custom_data' => array_filter([
                'content_ids' => [$request->input('product_id')],
                'content_name' => $request->input('product_name'),
                'content_type' => 'product',
                'content_category' => $request->input('category'),
                'content_brand' => $request->input('brand'),
                'value' => (float) $request->input('value', 0),
                'currency' => $request->input('currency', 'BDT'),
            ], fn ($value) => $value !== null && $value !== ''),
        ]);

        return response()->json($result, $result['status']);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'event_id' => 'required|string',
            'product_id' => 'required',
            'product_name' => 'required|string',
            'value' => 'required|numeric',
            'currency' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = max((int) $request->input('quantity', 1), 1);
        $value = (float) $request->input('value', 0);

        $result = $this->sendEvent([
            'event_name' => 'AddToCart',
            'event_time' => time(),
            'event_id' => $request->input('event_id'),
            'action_source' => 'website',
            'event_source_url' => $request->input('event_source_url', $request->headers->get('referer')),
            'user_data' => $this->buildUserData($request),
            'custom_data' => array_filter([
                'content_ids' => [(string) $request->input('product_id')],
                'content_name' => $request->input('product_name'),
                'content_category' => $request->input('category'),
                'content_brand' => $request->input('brand'),
                'value' => $value,
                'currency' => $request->input('currency', 'BDT'),
                'contents' => [[
                    'id' => (string) $request->input('product_id'),
                    'quantity' => $quantity,
                    'item_price' => $value / $quantity,
                ]],
            ], fn ($value) => $value !== null && $value !== ''),
        ]);

        return response()->json($result, $result['status']);
    }

    public function beginCheckoutCAPI(Request $request)
    {
        $items = collect($request->input('items', []));

        $result = $this->sendEvent([
            'event_name' => 'InitiateCheckout',
            'event_time' => time(),
            'event_id' => $request->input('event_id'),
            'action_source' => 'website',
            'event_source_url' => $request->input('event_source_url', $request->headers->get('referer')),
            'user_data' => $this->buildUserData($request),
            'custom_data' => [
                'currency' => $request->input('currency', 'BDT'),
                'value' => (float) $request->input('value', 0),
                'content_ids' => $items->pluck('id')->filter()->map(fn ($id) => (string) $id)->values()->all(),
                'content_type' => 'product',
                'contents' => $items->map(function ($item) {
                    return [
                        'id' => (string) ($item['id'] ?? ''),
                        'quantity' => (int) ($item['quantity'] ?? 1),
                        'item_price' => (float) ($item['price'] ?? 0),
                    ];
                })->all(),
            ],
        ]);

        return response()->json($result, $result['status']);
    }

    public function purchaseCAPI(Request $request)
    {
        $items = collect($request->input('items', []));
        $userData = $request->input('user_data', []);

        $hashedUserData = $this->buildUserData($request, array_filter([
            'em' => ! empty($userData['email']) ? hash('sha256', strtolower(trim($userData['email']))) : null,
            'ph' => ! empty($userData['phone']) ? hash('sha256', preg_replace('/\D/', '', $userData['phone'])) : null,
            'fn' => ! empty($userData['name']) ? hash('sha256', strtolower(trim($userData['name']))) : null,
            'ct' => ! empty($userData['area']) ? hash('sha256', strtolower(trim($userData['area']))) : null,
            'country' => ! empty($userData['country']) ? hash('sha256', strtolower(trim($userData['country']))) : null,
        ]));

        $result = $this->sendEvent([
            'event_name' => 'Purchase',
            'event_time' => time(),
            'event_id' => $request->input('event_id'),
            'action_source' => 'website',
            'event_source_url' => $request->input('event_source_url', $request->headers->get('referer')),
            'user_data' => $hashedUserData,
            'custom_data' => [
                'currency' => $request->input('currency', 'BDT'),
                'value' => (float) $request->input('value', 0),
                'content_ids' => $items->pluck('id')->filter()->map(fn ($id) => (string) $id)->values()->all(),
                'content_type' => 'product',
                'contents' => $items->map(function ($item) {
                    return [
                        'id' => (string) ($item['id'] ?? ''),
                        'quantity' => (int) ($item['quantity'] ?? 1),
                        'item_price' => (float) ($item['price'] ?? 0),
                    ];
                })->all(),
                'order_id' => $request->input('transaction_id'),
            ],
        ]);

        return response()->json($result, $result['status']);
    }
}
