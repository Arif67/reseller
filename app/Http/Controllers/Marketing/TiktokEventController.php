<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\EcomPixel;
use App\Models\MarketingEventLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TiktokEventController extends Controller
{
    protected ?EcomPixel $pixel = null;

    public function __construct()
    {
        $query = EcomPixel::query()->where('status', 1);

        if (Schema::hasColumn('ecom_pixels', 'provider')) {
            $query->where('provider', 'tiktok');
        }

        $this->pixel = $query->latest('id')->first();
    }

    protected function isConfigured(): bool
    {
        return ! empty($this->pixel?->code) && ! empty($this->pixel?->access_token);
    }

    protected function sendEvent(string $eventName, array $properties, Request $request): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'status' => 422,
                'message' => 'TikTok Pixel server-side tracking is not configured',
                'response' => null,
            ];
        }

        $payload = [
            'event_source' => 'web',
            'event_source_id' => (string) $this->pixel->code,
            'data' => [[
                'event' => $eventName,
                'event_time' => time(),
                'context' => [
                    'page' => [
                        'url' => $request->input('event_source_url', $request->headers->get('referer', url()->current())),
                    ],
                    'user' => [
                        'ip' => $request->input('client_ip_address', $request->ip()),
                        'user_agent' => $request->input('client_user_agent', $request->userAgent()),
                        'ttclid' => $request->input('ttclid'),
                        'ttp' => $request->input('ttp'),
                    ],
                ],
                'properties' => $properties,
            ]],
        ];

        if (! empty($this->pixel?->test_event_code)) {
            $payload['test_event_code'] = $this->pixel->test_event_code;
        }

        $response = Http::withHeaders([
            'Access-Token' => $this->pixel->access_token,
            'Content-Type' => 'application/json',
        ])->post('https://business-api.tiktok.com/open_api/v1.3/event/track/', $payload);

        Log::info('TikTok Events API response', [
            'event_name' => $eventName,
            'status' => $response->status(),
            'response' => $response->json() ?: $response->body(),
        ]);

        if (Schema::hasTable('marketing_event_logs')) {
            MarketingEventLog::create([
                'provider' => 'tiktok',
                'event_name' => $eventName,
                'event_id' => $request->input('event_id'),
                'status' => $response->successful() ? 'success' : 'failed',
                'order_id' => $request->input('transaction_id'),
                'value' => $properties['value'] ?? null,
                'currency' => $properties['currency'] ?? null,
                'source_url' => $request->input('event_source_url', $request->headers->get('referer')),
                'payload' => json_encode([
                    'request' => $payload,
                    'response' => $response->json() ?: $response->body(),
                ], JSON_UNESCAPED_UNICODE),
            ]);
        }

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'message' => $response->successful() ? 'TikTok event sent successfully' : 'TikTok event request failed',
            'response' => $response->json() ?: $response->body(),
        ];
    }

    public function pageView(Request $request)
    {
        $result = $this->sendEvent('PageView', [], $request);

        return response()->json($result, $result['status']);
    }

    public function viewContent(Request $request)
    {
        $result = $this->sendEvent('ViewContent', array_filter([
            'content_id' => (string) $request->input('product_id'),
            'content_name' => $request->input('product_name'),
            'content_type' => 'product',
            'content_category' => $request->input('category'),
            'content_brand' => $request->input('brand'),
            'value' => (float) $request->input('value', 0),
            'currency' => $request->input('currency', 'BDT'),
        ], fn ($value) => $value !== null && $value !== ''), $request);

        return response()->json($result, $result['status']);
    }

    public function addToCart(Request $request)
    {
        $quantity = max((int) $request->input('quantity', 1), 1);
        $value = (float) $request->input('value', 0);

        $result = $this->sendEvent('AddToCart', [
            'contents' => [[
                'content_id' => (string) $request->input('product_id'),
                'content_name' => $request->input('product_name'),
                'content_category' => $request->input('category'),
                'brand' => $request->input('brand'),
                'quantity' => $quantity,
                'price' => $value,
            ]],
            'currency' => $request->input('currency', 'BDT'),
            'value' => $value,
        ], $request);

        return response()->json($result, $result['status']);
    }

    public function beginCheckout(Request $request)
    {
        $items = collect($request->input('items', []));

        $result = $this->sendEvent('InitiateCheckout', [
            'value' => (float) $request->input('value', 0),
            'currency' => $request->input('currency', 'BDT'),
            'contents' => $items->map(function ($item) {
                return [
                    'content_id' => (string) ($item['id'] ?? ''),
                    'content_name' => $item['name'] ?? '',
                    'content_type' => 'product',
                    'quantity' => (int) ($item['quantity'] ?? 1),
                    'price' => (float) ($item['price'] ?? 0),
                ];
            })->all(),
        ], $request);

        return response()->json($result, $result['status']);
    }

    public function purchase(Request $request)
    {
        $items = collect($request->input('items', []));

        $result = $this->sendEvent('Purchase', [
            'value' => (float) $request->input('value', 0),
            'currency' => $request->input('currency', 'BDT'),
            'contents' => $items->map(function ($item) {
                return [
                    'content_id' => (string) ($item['id'] ?? ''),
                    'content_name' => $item['name'] ?? '',
                    'content_type' => 'product',
                    'quantity' => (int) ($item['quantity'] ?? 1),
                    'price' => (float) ($item['price'] ?? 0),
                ];
            })->all(),
        ], $request);

        return response()->json($result, $result['status']);
    }
}
