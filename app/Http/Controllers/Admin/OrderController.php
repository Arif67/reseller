<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\District;
use App\Models\OrderStatus;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Shipping;
use App\Models\ShippingCharge;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Courierapi;
use App\Models\FraudCheckerConfig;
use App\Models\Expense;
use App\Models\ExpenseCategories;
use App\Models\ProductVariable;
use App\Services\AppService\ProductAttributeService;
use App\Services\AppService\ProfitLossService;
use App\Services\Admin\InventoryService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Session;
use Cart;
use Toastr;
use Mail;

use function GuzzleHttp\Promise\all;

class OrderController extends Controller
{
    private function sortCartContent($cartinfo)
    {
        return $cartinfo->sortBy(function ($item) {
            return $item->options->sort_key ?? 0;
        });
    }

    private function cartContext(?Request $request = null): string
    {
        return $request?->get('context') === 'workspace' ? 'workspace' : 'create';
    }

    private function cartInstanceName(string $context): string
    {
        return $context === 'workspace' ? 'workspace_shopping' : 'pos_shopping';
    }

    private function shippingSessionKey(string $context): string
    {
        return $context === 'workspace' ? 'workspace_pos_shipping' : 'pos_shipping';
    }

    private function orderDiscountSessionKey(string $context): string
    {
        return $context === 'workspace' ? 'workspace_pos_discount' : 'pos_discount';
    }

    private function productDiscountSessionKey(string $context): string
    {
        return $context === 'workspace' ? 'workspace_product_discount' : 'product_discount';
    }

    private function clearCartState(string $context): void
    {
        Cart::instance($this->cartInstanceName($context))->destroy();
        Session::forget($this->shippingSessionKey($context));
        Session::forget($this->orderDiscountSessionKey($context));
        Session::forget($this->productDiscountSessionKey($context));
    }

    private function syncProductDiscountSession(string $context): float
    {
        $discount = 0;
        $cartinfo = Cart::instance($this->cartInstanceName($context))->content();

        foreach ($cartinfo as $cart) {
            $discount += ((float) ($cart->options->product_discount ?? 0)) * $cart->qty;
        }

        Session::put($this->productDiscountSessionKey($context), $discount);

        return $discount;
    }

    private function resolveSteadfastEndpoint(string $configuredUrl, string $path): string
    {
        $normalizedUrl = rtrim($configuredUrl, '/');

        if (str_contains($normalizedUrl, '/create_order')) {
            $normalizedUrl = preg_replace('#/create_order(?:/bulk-order)?$#', '', $normalizedUrl) ?? $normalizedUrl;
        }

        if (! str_contains($normalizedUrl, '/api/v1')) {
            $normalizedUrl .= '/api/v1';
        }

        return $normalizedUrl . $path;
    }

    private function ensurePathaoToken(Courierapi $pathaoInfo): ?string
    {
        if (! empty($pathaoInfo->token)) {
            return $pathaoInfo->token;
        }

        if (! $pathaoInfo->client_id || ! $pathaoInfo->client_secret || ! $pathaoInfo->username || ! $pathaoInfo->password) {
            return null;
        }

        $generateToken = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://api-hermes.pathao.com/aladdin/api/v1/issue-token', [
            'client_id' => $pathaoInfo->client_id,
            'client_secret' => $pathaoInfo->client_secret,
            'username' => $pathaoInfo->username,
            'password' => $pathaoInfo->password,
            'grant_type' => $pathaoInfo->grant_type ?: 'password',
        ])->json();

        if (! empty($generateToken['access_token'])) {
            $pathaoInfo->token = $generateToken['access_token'];
            $pathaoInfo->save();

            return $pathaoInfo->token;
        }

        \Log::error('Pathao token generation failed', is_array($generateToken) ? $generateToken : ['response' => $generateToken]);

        return null;
    }

    public function __construct(
        private readonly ProductAttributeService $productAttributeService,
        private readonly ProfitLossService $profitLossService,
        private readonly InventoryService $inventoryService,
    ) {
        $this->middleware('permission:order-list|order-create|order-edit|order-delete', ['only' => ['index', 'search', 'order_workspace', 'order_assign', 'order_status', 'order_print', 'bulk_courier', 'stock_report', 'order_report', 'loss_profit', 'zero_cost_audit']]);
        $this->middleware('permission:order-create', ['only' => ['order_create', 'order_store', 'product_preview', 'catalog_products', 'cart_add', 'cart_content', 'cart_increment', 'cart_decrement', 'cart_remove', 'product_discount', 'cart_details', 'cart_shipping', 'cart_clear']]);
        $this->middleware('permission:order-edit', ['only' => ['order_edit', 'order_update', 'order_pathao', 'fraud_check']]);
        $this->middleware('permission:order-delete', ['only' => ['destroy', 'bulk_destroy']]);
        $this->middleware('permission:order-invoice', ['only' => ['invoice', 'invoice_print']]);
        $this->middleware('permission:order-process', ['only' => ['process', 'order_process', 'order_steadfast', 'pathaocity', 'pathaozone', 'updatePathaoStatus', 'updatePathaoStatusWebhook', 'updateSteadfastStatus', 'updateSteadfastStatusWebhook', 'recalculate_profit_loss_snapshots']]);
    }
   public function search(Request $request)
{
    $keyword = trim((string) $request->keyword);
    $products = collect();
    $exactProduct = null;
    $exactVariant = null;

    if ($keyword === '') {
        return view('backEnd.order.search', compact('products', 'exactProduct', 'exactVariant', 'keyword'));
    }

    $exactVariant = ProductVariable::query()
        ->with(['product.image'])
        ->where('barcode', $keyword)
        ->first();

    if ($exactVariant && $exactVariant->product && (int) $exactVariant->product->status === 1) {
        return view('backEnd.order.search', compact('products', 'exactProduct', 'exactVariant', 'keyword'));
    }

    $exactVariant = null;

    $exactProduct = Product::query()
        ->with(['image'])
        ->where('status', 1)
        ->where(function ($query) use ($keyword) {
            $query->where('pro_barcode', $keyword)
                ->orWhere('product_code', $keyword);
        })
        ->first();

    if ($exactProduct) {
        return view('backEnd.order.search', compact('products', 'exactProduct', 'exactVariant', 'keyword'));
    }

    $products = Product::query()
        ->with(['image', 'variables'])
        ->where('status', 1)
        ->where(function ($query) use ($keyword) {
            $query->where('name', 'LIKE', '%' . $keyword . '%')
                ->orWhere('product_code', 'LIKE', '%' . $keyword . '%')
                ->orWhere('pro_barcode', 'LIKE', '%' . $keyword . '%')
                ->orWhereHas('variables', function ($variantQuery) use ($keyword) {
                    $variantQuery->where('barcode', 'LIKE', '%' . $keyword . '%');
                });
        })
        ->get();

    return view('backEnd.order.search', compact('products', 'exactProduct', 'exactVariant', 'keyword'));
}

    

public function index($slug, Request $request)
{
    $isTodayFilter = $request->get('date') === 'today';
    $resolvedDateRange = $this->resolveOrderDateRange($request->get('date'));
    $activeFilters = [
        'keyword' => trim((string) $request->get('keyword', '')),
        'start_date' => $request->get('start_date'),
        'end_date' => $request->get('end_date'),
        'date' => $request->get('date'),
        'date_label' => $resolvedDateRange['label'] ?? null,
    ];

    if ($slug == 'all') {
        $order_status = (object) [
            'name' => 'All',
            'orders_count' => Order::count(),
        ];
        $show_data = Order::latest()->with([
            'shipping',
            'status',
            'orderdetails.product.image',
            'orderdetails.product.media',
            'orderdetails.productVariable.media',
            'orderdetails.image'
        ]);
        $show_data = $this->applyOrderListFilters($show_data, $request);

        if ($isTodayFilter) {
            $order_status->name = "Today's";
        }

        $order_status->orders_count = (clone $show_data)->count();
        $show_data = $show_data->paginate(50)->withQueryString();
    } else {
        $order_status = OrderStatus::query()
            ->withCount('orders')
            ->where('slug', $slug)
            ->first();

        if (! $order_status) {
            $order_status = OrderStatus::query()
                ->withCount('orders')
                ->get()
                ->first(function (OrderStatus $status) use ($slug) {
                    return Str::slug((string) $status->name) === $slug;
                });
        }

        if (! $order_status) {
            $virtual_map = [
                'processing' => ['processing', 'approved'],
                'on-the-way' => ['on-the-way', 'on the way', 'packed', 'shipped'],
                'completed' => ['completed', 'delivered', 'complete', 'deliveryed'],
                'cancelled' => ['cancelled', 'canceled', 'returned'],
                'in-courier' => ['in-courier', 'in courier', 'out-for-delivery'],
                'didnt-receive-call' => ['didnt-receive-call', 'didn-t-receive-call', 'didnt receive call', 'did not receive call'],
            ];

            if (isset($virtual_map[$slug])) {
                $candidates = $virtual_map[$slug];
                $statusIds = OrderStatus::query()
                    ->get()
                    ->filter(function ($status) use ($candidates) {
                        $s = Str::slug((string) $status->slug);
                        $n = Str::slug((string) $status->name);
                        foreach ($candidates as $candidate) {
                            $normalized = Str::slug($candidate);
                            if ($s === $normalized || $n === $normalized) {
                                return true;
                            }
                        }

                        return false;
                    })
                    ->pluck('id')
                    ->all();

                $order_status = (object) [
                    'name' => ucwords(str_replace('-', ' ', $slug)),
                    'orders_count' => Order::whereIn('order_status', $statusIds)->count(),
                    'id' => null,
                    'status_ids' => $statusIds,
                ];
            }
        }

        abort_if(! $order_status, 404, 'Order status not found.');

        $show_data = isset($order_status->status_ids)
            ? Order::whereIn('order_status', $order_status->status_ids)
            : Order::where(['order_status' => $order_status->id]);

        $show_data = $show_data->latest()->with([
            'shipping',
            'status',
            'orderdetails.product.image',
            'orderdetails.product.media',
            'orderdetails.productVariable.media',
            'orderdetails.image'
        ]);
        $show_data = $this->applyOrderListFilters($show_data, $request);

        if ($isTodayFilter) {
            $order_status->name = "Today's " . $order_status->name;
        }

        $order_status->orders_count = (clone $show_data)->count();
        $show_data = $show_data->paginate(50)->withQueryString();
    }

    $users = User::get();

    $fraudCheckerConfig = FraudCheckerConfig::where('status', 1)
        ->select('id', 'name', 'url', 'api_key', 'status')
        ->first();

    // -------------------------------
    // Pathao Courier API
    // -------------------------------
    $pathaocities = [];
    $pathaozones  = [];
    $pathaoareas  = [];

    $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])
        ->select('id', 'type', 'client_id', 'client_secret', 'username', 'password', 'grant_type', 'url', 'token', 'status')
        ->first();

    if ($pathao_info) {
        $token = $this->ensurePathaoToken($pathao_info);

        if ($token) {
            $pathaostore = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8',
                'Authorization' => 'Bearer ' . $token,
            ])->get('https://api-hermes.pathao.com/aladdin/api/v1/stores');

            $cityResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ])->get('https://api-hermes.pathao.com/aladdin/api/v1/city-list');

            $pathaocities = $cityResponse->json();
        }
    }


    return view('backEnd.order.index', compact(
        'show_data',
        'order_status',
        'users',
        'pathaostore',
        'pathaocities',
        'fraudCheckerConfig',
        'activeFilters',
    ));
}

private function applyOrderListFilters($query, Request $request)
{
    if ($request->filled('keyword')) {
        $keyword = trim((string) $request->keyword);

        $query->where(function ($subQuery) use ($keyword) {
            $subQuery->where('invoice_id', 'LIKE', '%' . $keyword . '%')
                ->orWhereHas('shipping', function ($shippingQuery) use ($keyword) {
                    $shippingQuery->where('phone', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('name', 'LIKE', '%' . $keyword . '%');
                });
        });
    }

    $resolvedDateRange = $this->resolveOrderDateRange($request->get('date'));

    if ($resolvedDateRange) {
        $query->whereDate('created_at', '>=', $resolvedDateRange['start'])
            ->whereDate('created_at', '<=', $resolvedDateRange['end']);
    } else {
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
    }

    return $query;
}

private function resolveOrderDateRange(?string $preset): ?array
{
    return match ($preset) {
        'today' => [
            'start' => now()->toDateString(),
            'end' => now()->toDateString(),
            'label' => 'Today',
        ],
        'yesterday' => [
            'start' => now()->subDay()->toDateString(),
            'end' => now()->subDay()->toDateString(),
            'label' => 'Yesterday',
        ],
        'last_7_days' => [
            'start' => now()->subDays(6)->toDateString(),
            'end' => now()->toDateString(),
            'label' => 'Last 7 Days',
        ],
        'this_month' => [
            'start' => now()->startOfMonth()->toDateString(),
            'end' => now()->endOfMonth()->toDateString(),
            'label' => 'This Month',
        ],
        default => null,
    };
}

   public function pathaocity(Request $request)
{
    $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])
        ->select('id', 'type', 'client_id', 'client_secret', 'username', 'password', 'grant_type', 'url', 'token', 'status')->first();

    if ($pathao_info && ($token = $this->ensurePathaoToken($pathao_info))) {
        $response = Http::withToken($token)
            ->get('https://api-hermes.pathao.com/aladdin/api/v1/cities/' . $request->city_id . '/zone-list');

        return response()->json($response->json());
    } else {
        return response()->json([]);
    }
}

public function pathaozone(Request $request)
{
    $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])
        ->select('id', 'type', 'client_id', 'client_secret', 'username', 'password', 'grant_type', 'url', 'token', 'status')->first();

    if ($pathao_info && ($token = $this->ensurePathaoToken($pathao_info))) {
        $response = Http::withToken($token)
            ->get('https://api-hermes.pathao.com/aladdin/api/v1/zones/' . $request->zone_id . '/area-list');

        return response()->json($response->json());
    } else {
        return response()->json([]);
    }
}

    public function fraud_check(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string'],
        ]);

        $phone = preg_replace('/\D+/', '', (string) $request->phone) ?: (string) $request->phone;
        $fraudCheckerConfig = FraudCheckerConfig::where('status', 1)
            ->select('url', 'api_key')
            ->first();

        if (! $fraudCheckerConfig || ! $fraudCheckerConfig->url || ! $fraudCheckerConfig->api_key) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Fraud checker API key not configured',
            ], 422);
        }

        $cacheKey = 'fraud_checker:' . md5($phone . '|' . $fraudCheckerConfig->url);
        $cachedResponse = Cache::get($cacheKey);

        if ($cachedResponse) {
            return response()->json(array_merge($cachedResponse, [
                'cached' => true,
            ]), 200);
        }

        $token = trim((string) $fraudCheckerConfig->api_key);

        if (! str_starts_with(strtolower($token), 'bearer ')) {
            $token = 'Bearer ' . $token;
        }

        $response = Http::withHeaders([
            'Authorization' => $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post(trim((string) $fraudCheckerConfig->url), [
            'phone' => $phone,
        ]);

        $responsePayload = $response->json() ?: [
            'status' => $response->successful() ? 'success' : 'failed',
            'message' => trim((string) $response->body()) ?: 'Unexpected response from fraud checker',
        ];

        if ($response->successful()) {
            Cache::put($cacheKey, $responsePayload, now()->addMinutes(15));
        }

        return response()->json(array_merge($responsePayload, [
            'cached' => false,
        ]), $response->status());
    }

    public function order_steadfast($order_id)
    {

        try {
            $order = Order::with('shipping')->findOrFail($order_id);
            $steadfast_info = Courierapi::where(['status' => 1, 'type' => 'steadfast'])->first();
     
            if (! $steadfast_info || ! $steadfast_info->api_key || ! $steadfast_info->secret_key || ! $steadfast_info->url) {
                return redirect()->back()->with(['status' => 'failed', 'message' => 'Steadfast courier info not configured properly.']);
            }

            $consignmentData = [
                'invoice' => $order->invoice_id,
                'recipient_name' => $order->shipping->name ?? 'Customer',
                'recipient_phone' => $order->shipping->phone ?? '',
                'recipient_address' => $order->shipping->address ?? '',
                'cod_amount' => $order->amount,
            ];
             
            $endpoint = $this->resolveSteadfastEndpoint($steadfast_info->url, '/create_order');
      
            $response = Http::withHeaders([
                'Api-Key' => trim((string) $steadfast_info->api_key),
                'Secret-Key' => trim((string) $steadfast_info->secret_key),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($endpoint, $consignmentData);

            $responseData = $response->json();
            $responseMessage = is_array($responseData)
                ? ($responseData['message'] ?? null)
                : trim((string) $response->body());
     
            if ($response->successful() && (($responseData['status'] ?? null) == 200 || isset($responseData['consignment']))) {
                $order->update([
                    'order_status' => 5,
                    'courier' => 'steadfast',
                    'tracking_id' => $responseData['consignment']['consignment_id'] ?? $responseData['consignment_id'] ?? null,
                ]);

                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => $responseData['consignment']['tracking_code'] ?? $responseData['message'] ?? 'Tracking created',
                ]);
            }

            return redirect()->back()->with([
                'status' => 'failed',
                'message' => $responseMessage ?: 'Courier API error',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ]);
        }
    }


    public function order_pathao(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer'],
            'pathaostore' => ['required'],
            'pathaocity' => ['required'],
            'pathaozone' => ['required'],
            'pathaoarea' => ['required'],
        ]);

        $order = Order::with('shipping')->find($request->id);
        if (! $order) {
            Toastr::error('Order not found', 'Courier Order Failed');
            return redirect()->back();
        }

        $order_count = OrderDetails::select('order_id')->where('order_id', $order->id)->count();
        $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])->select('id', 'type', 'client_id', 'client_secret', 'username', 'password', 'grant_type', 'url', 'token', 'status')->first();
        $token = $pathao_info ? $this->ensurePathaoToken($pathao_info) : null;

        if (! $pathao_info || ! $token) {
            Toastr::error('Pathao is not configured properly', 'Courier Order Failed');
            return redirect()->back();
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('https://api-hermes.pathao.com/aladdin/api/v1/orders', [
            'store_id'          => $request->pathaostore,
            'merchant_order_id' => $order->invoice_id,
            'sender_name'       => config('app.name', 'Your Store Name'),
            'sender_phone'      => $order->shipping ? $order->shipping->phone : '',
            'recipient_name'    => $order->shipping ? $order->shipping->name : '',
            'recipient_phone'   => $order->shipping ? $order->shipping->phone : '',
            'recipient_address' => $order->shipping ? $order->shipping->address : '',
            'recipient_city'    => $request->pathaocity,
            'recipient_zone'    => $request->pathaozone,
            'recipient_area'    => $request->pathaoarea,
            'delivery_type'     => 48,
            'item_type'         => 2,
            'special_instruction' => substr($order->note ?? 'Product must be checked before delivery', 0, 500),
            'item_quantity'     => $order_count,
            'item_weight'       => 0.5,
            'amount_to_collect' => round($order->amount),
            'item_description'  => 'Order for invoice #' . $order->invoice_id,
        ]);

        if ($response->status() == '200') {
            $order->order_status = 5;
            $order->courier = 'pathao';
            $order->tracking_id = $response['data']['consignment_id'];
            $order->save();
            Toastr::success('order send to pathao successfully');
            return redirect()->back();
        } else {
            Toastr::error($response['message'], 'Courier Order Faild');
            return redirect()->back();
        }
    }

    public function invoice($invoice_id)
    {
        return redirect()->route('admin.order.workspace', ['invoice_id' => $invoice_id, 'tab' => 'invoice']);
    }

    public function invoice_print($invoice_id)
    {
        $order = Order::where(['invoice_id' => $invoice_id])->with([
            'orderdetails.image',
            'orderdetails.product.image',
            'orderdetails.product.media',
            'orderdetails.productVariable.media',
            'payment',
            'shipping',
            'customer',
        ])->firstOrFail();
        return view('backEnd.order.invoice', compact('order'));
    }

    public function process($invoice_id)
    {
        return redirect()->route('admin.order.workspace', ['invoice_id' => $invoice_id, 'tab' => 'manage']);
    }

    public function order_process(Request $request)
    {
        try {
            $link = OrderStatus::find($request->status)->slug;
            $order = Order::find($request->id);
            $courier = $order->order_status;

            DB::transaction(function () use ($request, $order, $courier) {
                $order->order_status = $request->status;
                $order->admin_note = $request->admin_note;
                $order->save();

                $shipping_update = Shipping::where('order_id', $order->id)->first();
                $shippingfee = ShippingCharge::find($request->area);
                if ($shippingfee && (float) $order->shipping_charge !== (float) $shippingfee->amount) {
                    $total = $order->amount + ($shippingfee->amount - $order->shipping_charge);
                    $order->shipping_charge = $shippingfee->amount;
                    $order->amount = $total;
                    $order->save();
                }

                $shipping_update->name = $request->name;
                $shipping_update->phone = $request->phone;
                $shipping_update->address = $request->address;
                $shipping_update->area = $shippingfee->name;
                $shipping_update->save();

                $deliveredStatusIds = array_map('intval', $this->profitLossService->deliveredOrderStatusIds());
                $isDelivered = in_array((int) $request->status, $deliveredStatusIds, true);
                $this->inventoryService->syncOrderDelivery($order, $isDelivered);
                $this->profitLossService->recalculateOrder($order);
            });

            if ($request->status == 5 && $courier != 5) {
                $courier_info = Courierapi::where(['status' => 1, 'type' => 'steadfast'])->first();
                if ($courier_info) {
                    $consignmentData = [
                        'invoice' => $order->invoice_id,
                        'recipient_name' => $order->shipping ? $order->shipping->name : 'InboxHat',
                        'recipient_phone' => $order->shipping ? $order->shipping->phone : '01750578495',
                        'recipient_address' => $order->shipping ? $order->shipping->address : '01750578495',
                        'cod_amount' => $order->amount
                    ];
                    $client = new Client();
                    $response = $client->post('$courier_info->url', [
                        'json' => $consignmentData,
                        'headers' => [
                            'Api-Key' => '$courier_info->api_key',
                            'Secret-Key' => '$courier_info->secret_key',
                            'Accept' => 'application/json',
                        ],
                    ]);

                    $responseData = json_decode($response->getBody(), true);
                }
            }

            Toastr::success('Success', 'Order status change successfully');

            if ($request->filled('workspace_invoice_id')) {
                return redirect()->route('admin.order.workspace', [
                    'invoice_id' => $request->workspace_invoice_id,
                    'tab' => 'manage',
                ]);
            }

            return redirect('admin/order/' . $link);
        } catch (\Throwable $exception) {
            Toastr::error($exception->getMessage(), 'Failed!');
            return redirect()->back()->withInput();
        }
    }

    public function destroy(Request $request)
    {
        $order = Order::find($request->id);

        if (! $order) {
            Toastr::error('Order not found', 'Failed!');
            return redirect()->back();
        }

        $deliveredStatusIds = array_map('intval', $this->profitLossService->deliveredOrderStatusIds());

        DB::transaction(function () use ($order, $deliveredStatusIds) {
            if (in_array((int) $order->order_status, $deliveredStatusIds, true)) {
                $this->inventoryService->syncOrderDelivery($order, false);
            }

            OrderDetails::where('order_id', $order->id)->delete();
            Shipping::where('order_id', $order->id)->delete();
            Payment::where('order_id', $order->id)->delete();
            $order->delete();
        });

        Toastr::success('Success', 'Order delete success successfully');
        return redirect()->back();
    }

    public function order_assign(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'order_ids' => 'required|array|min:1',
                'order_ids.*' => 'integer|exists:orders,id',
            ]);

            $updatedCount = Order::whereIn('id', $validated['order_ids'])
                ->update(['user_id' => $validated['user_id']]);

            if ($updatedCount === 0) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'No orders were assigned.',
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Orders assigned successfully.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            return response()->json([
                'status' => 'failed',
                'message' => collect($exception->errors())->flatten()->first() ?: 'Validation failed.',
                'errors' => $exception->errors(),
            ], 422);
        } catch (\Throwable $exception) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Order assignment failed.',
            ], 500);
        }
    }

    public function order_status(Request $request)
    {
        try {
            $orders = Order::whereIn('id', $request->input('order_ids'))->get();

            $deliveredStatusIds = array_map('intval', $this->profitLossService->deliveredOrderStatusIds());
            $isDelivered = in_array((int) $request->order_status, $deliveredStatusIds, true);

            DB::transaction(function () use ($orders, $request, $isDelivered) {
                foreach ($orders as $order) {
                    $order->order_status = $request->order_status;
                    $order->save();
                    $this->inventoryService->syncOrderDelivery($order, $isDelivered);
                    $this->profitLossService->recalculateOrder($order);
                }
            });

            return response()->json(['status' => 'success', 'message' => 'Order status change successfully']);
        } catch (\Throwable $exception) {
            return response()->json([
                'status' => 'failed',
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    public function bulk_destroy(Request $request)
    {
        $orders_id = $request->order_ids;

        $deliveredStatusIds = array_map('intval', $this->profitLossService->deliveredOrderStatusIds());

        DB::transaction(function () use ($orders_id, $deliveredStatusIds) {
            foreach ($orders_id as $order_id) {
                $order = Order::find($order_id);

                if (! $order) {
                    continue;
                }

                if (in_array((int) $order->order_status, $deliveredStatusIds, true)) {
                    $this->inventoryService->syncOrderDelivery($order, false);
                }

                OrderDetails::where('order_id', $order_id)->delete();
                Shipping::where('order_id', $order_id)->delete();
                Payment::where('order_id', $order_id)->delete();
                $order->delete();
            }
        });

        return response()->json(['status' => 'success', 'message' => 'Order delete successfully']);
    }
    public function order_print(Request $request)
    {
        $orders = Order::whereIn('id', $request->input('order_ids'))->with('orderdetails', 'payment', 'shipping', 'customer')->get();
        $view = view('backEnd.order.print', ['orders' => $orders])->render();
        return response()->json(['status' => 'success', 'view' => $view]);
    }
  public function bulk_courier($slug, Request $request)
{
    $courier_info = Courierapi::where(['status' => 1, 'type' => $slug])->first();

    if ($slug === 'pathao') {
        return $this->processPathaoBulk($courier_info, $request);
    }

    if (! $courier_info || ! $courier_info->api_key || ! $courier_info->secret_key || ! $courier_info->url) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Courier API credentials not found'
        ]);
    }

    $orders_id = $request->order_ids;

    if (! is_array($orders_id) || count($orders_id) == 0) {
        return response()->json([
            'status' => 'failed',
            'message' => 'No orders selected'
        ]);
    }

    $client = new \GuzzleHttp\Client();
    $endpoint = $this->resolveSteadfastEndpoint($courier_info->url, '/create_order/bulk-order');
    $finalResponse = [];
    $successCount = 0;

    foreach ($orders_id as $order_id) {
        $order = Order::with('shipping')->find($order_id);

        if (! $order) {
            $finalResponse[] = [
                'order_id' => $order_id,
                'status' => 'failed',
                'message' => 'Order not found'
            ];
            continue;
        }

        if ((int) $order->order_status === 5) {
            $finalResponse[] = [
                'order_id' => $order_id,
                'status' => 'skipped',
                'message' => 'Order already picked by courier'
            ];
            continue;
        }

        $consignmentData = [
            'invoice' => $order->invoice_id,
            'recipient_name' => $order->shipping->name ?? 'Customer',
            'recipient_phone' => $order->shipping->phone ?? '',
            'recipient_address' => $order->shipping->address ?? '',
            'cod_amount' => $order->amount,
        ];

        try {
            $response = $client->post($endpoint, [
                'json' => [
                    'data' => [$consignmentData]
                ],
                'headers' => [
                    'Api-Key' => $courier_info->api_key,
                    'Secret-Key' => $courier_info->secret_key,
                    'Accept' => 'application/json',
                ]
            ]);

            $responseData = json_decode($response->getBody(), true);
            $consignment = $responseData['consignments'][0] ?? $responseData['consignment'] ?? null;

            if (($responseData['status'] ?? null) == 200) {
                $order->order_status = 5;
                $order->courier = $slug;
                $order->tracking_id = $consignment['consignment_id'] ?? $consignment['tracking_code'] ?? $order->tracking_id;
                $order->save();

                $successCount++;
                $finalResponse[] = [
                    'order_id' => $order_id,
                    'status' => 'success',
                    'message' => $consignment['tracking_code'] ?? 'Order successfully placed to courier'
                ];
            } else {
                $finalResponse[] = [
                    'order_id' => $order_id,
                    'status' => 'failed',
                    'message' => $responseData['message'] ?? 'Courier API failed'
                ];
            }
        } catch (\Exception $e) {
            $finalResponse[] = [
                'order_id' => $order_id,
                'status' => 'failed',
                'message' => $e->getMessage()
            ];
        }
    }

    return response()->json([
        'status' => $successCount > 0 ? 'success' : 'failed',
        'message' => $successCount > 0 ? 'Selected orders sent to courier successfully' : 'Courier dispatch failed',
        'results' => $finalResponse,
    ]);
}

    private function processPathaoBulk($courier_info, Request $request)
    {
        if (! $courier_info) {
            return response()->json(['status' => 'failed', 'message' => 'Pathao configuration not found']);
        }

        $token = $this->ensurePathaoToken($courier_info);
        if (! $token) {
            return response()->json(['status' => 'failed', 'message' => 'Pathao token generation failed']);
        }

        $orders_id = $request->order_ids;
        $store_id = $request->store_id;

        if (! is_array($orders_id) || count($orders_id) == 0) {
            return response()->json(['status' => 'failed', 'message' => 'No orders selected']);
        }

        if (! $store_id) {
            return response()->json(['status' => 'failed', 'message' => 'Please select a Pathao store']);
        }

        $pathaoOrders = [];
        $selectedOrders = Order::with('shipping', 'orderdetails')->whereIn('id', $orders_id)->get();

        foreach ($selectedOrders as $order) {
            if ((int) $order->order_status === 5) {
                continue;
            }

            $itemCount = $order->orderdetails->sum('qty');
            $phone = $order->shipping?->phone ?? '';

            $pathaoOrders[] = [
                "store_id"          => (int) $store_id,
                "merchant_order_id" => (string) $order->invoice_id,
                "recipient_name"    => substr($order->shipping?->name ?? 'Customer', 0, 100),
                "recipient_phone"   => $phone,
                "recipient_address" => substr($order->shipping?->address ?? '', 0, 500),
                "delivery_type"     => 48,
                "item_type"         => 2,
                "special_instruction" => substr($order->note ?? 'Product must be checked before delivery', 0, 500),
                "item_quantity"     => (int) $itemCount,
                "item_weight"       => "0.5",
                "amount_to_collect" => (int) round($order->amount),
                "item_description"  => "Order for invoice #" . $order->invoice_id,
            ];
        }

        if (empty($pathaoOrders)) {
            return response()->json(['status' => 'failed', 'message' => 'No eligible orders found for Pathao bulk dispatch']);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->post('https://api-hermes.pathao.com/aladdin/api/v1/orders/bulk', [
            'orders' => $pathaoOrders
        ]);

        $responseData = $response->json();
        \Log::info('Pathao Bulk API Response', ['response' => $responseData, 'status' => $response->status()]);

        if ($response->successful()) {
            $results = $responseData['data'] ?? [];
            $successCount = 0;
            $failedOrders = [];

            if ($results === true) {
                // Large batches are accepted asynchronously by Pathao
                foreach ($selectedOrders as $order) {
                    $order->order_status = 5;
                    $order->courier = 'pathao';
                    $order->save();
                }

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Pathao bulk order request accepted. Please wait some time for processing.',
                    'response' => $responseData
                ]);
            }

            if (! is_array($results)) {
                \Log::error('Pathao bulk API results is not an array', ['results' => $results]);
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Pathao bulk API returned unexpected data format',
                    'response' => $responseData
                ]);
            }

            foreach ($results as $result) {
                if (isset($result['consignment_id'])) {
                    $order = Order::where('invoice_id', $result['merchant_order_id'])->first();
                    if ($order) {
                        $order->order_status = 5;
                        $order->courier = 'pathao';
                        $order->tracking_id = $result['consignment_id'];
                        $order->save();
                        $successCount++;
                    }
                } else {
                    $failedOrders[] = [
                        'invoice' => $result['merchant_order_id'] ?? 'Unknown',
                        'message' => $result['message'] ?? 'Failed'
                    ];
                }
            }

            if ($successCount > 0) {
                $msg = $successCount . ' orders sent to Pathao successfully.';
                if (count($failedOrders) > 0) {
                    $msg .= ' ' . count($failedOrders) . ' orders failed.';
                }
                return response()->json([
                    'status' => 'success',
                    'message' => $msg,
                    'failed'  => $failedOrders
                ]);
            } else {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'All selected orders failed to send to Pathao.',
                    'failed'  => $failedOrders
                ]);
            }
        }

        return response()->json([
            'status'  => 'failed',
            'message' => $responseData['message'] ?? 'Pathao bulk API failed'
        ]);
    }


    public function order_create()
    {
        Cart::instance('pos_shopping')->destroy();
        Session::forget('pos_shopping');
        Session::forget('pos_discount');
        Session::forget('product_discount');
        $products = Product::select('id', 'name', 'new_price', 'product_code', 'type', 'status')->where(['status' => 1])->get();
        $catalogFilters = $this->resolveCatalogFilters(new Request());
        $catalogProducts = $this->buildCatalogProductsQuery($catalogFilters)->paginate(24);
        $categories = Category::query()
            ->where('status', 1)
            ->withCount(['products as product_count' => function ($query) {
                $query->where('status', 1);
            }])
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
        $cartinfo  = Cart::instance('pos_shopping')->content();
        $shippingcharge = ShippingCharge::where('status', 1)->get();
        return view('backEnd.order.create', compact('products', 'catalogProducts', 'categories', 'catalogFilters', 'cartinfo', 'shippingcharge'));
    }

    private function resolveCatalogFilters(Request $request): array
    {
        $mode = in_array($request->input('mode'), ['recent', 'top'], true) ? $request->input('mode') : 'recent';
        $categoryId = $request->filled('category_id') ? (int) $request->input('category_id') : null;
        $keyword = trim((string) $request->input('keyword', ''));
        $barcodeOnly = (bool) $request->boolean('barcode_only');

        return [
            'mode' => $mode,
            'category_id' => $categoryId,
            'keyword' => $keyword,
            'barcode_only' => $barcodeOnly,
        ];
    }

    private function buildCatalogProductsQuery(array $filters)
    {
        $query = Product::query()
            ->where('status', 1)
            ->with([
                'image',
                'media',
                'variables.selectedValues.attribute',
            ])
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'type', 'variation_pricing_mode', 'description', 'pro_barcode', 'category_id', 'topsale', 'created_at')
            ->when($filters['category_id'] ?? null, fn ($builder, $categoryId) => $builder->where('category_id', $categoryId))
            ->when(($filters['keyword'] ?? '') !== '', function ($builder) use ($filters) {
                $keyword = $filters['keyword'];
                $builder->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('product_code', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('pro_barcode', 'LIKE', '%' . $keyword . '%')
                        ->orWhereHas('variables', function ($variantQuery) use ($keyword) {
                            $variantQuery->where('barcode', 'LIKE', '%' . $keyword . '%');
                        });
                });
            })
            ->when(($filters['barcode_only'] ?? false), function ($builder) {
                $builder->where(function ($subQuery) {
                    $subQuery->whereNotNull('pro_barcode')
                        ->where('pro_barcode', '!=', '')
                        ->orWhereHas('variables', function ($variantQuery) {
                            $variantQuery->whereNotNull('barcode')->where('barcode', '!=', '');
                        });
                });
            });

        return match ($filters['mode'] ?? 'recent') {
            'top' => $query->where('topsale', 1)->orderByDesc('id'),
            default => $query->orderByDesc('id'),
        };
    }

    public function catalog_products(Request $request)
    {
        $filters = $this->resolveCatalogFilters($request);
        $catalogProducts = $this->buildCatalogProductsQuery($filters)->paginate(24);

        return view('backEnd.order.partials.product-browser', compact('catalogProducts', 'filters'));
    }

    public function product_preview(Request $request)
    {
        $product = Product::query()
            ->where(['id' => $request->id, 'status' => 1])
            ->with([
                'image',
                'images',
                'media',
                'variables.selectedValues.attribute',
            ])
            ->firstOrFail();

        return view('backEnd.order.partials.product-preview', compact('product'));
    }

    public function order_store(Request $request)
    {
        $isGuestCustomer = (bool) $request->guest_customer;

        if ($isGuestCustomer) {
            $defaultArea = ShippingCharge::where(['status' => 1, 'pos' => 1])->first();
            if (!$defaultArea) {
                $defaultArea = ShippingCharge::where('status', 1)->first();
            }

            if (!$defaultArea) {
                Toastr::error('No active shipping area found', 'Failed!');
                return redirect()->back();
            }

            $name = 'Guest Customer';
            $phone = '00000000000';
            $address = $defaultArea->name;
            $area = $defaultArea->id;
        } else {
            $this->validate($request, [
                'name' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'area' => 'required',
            ]);
            $name = $request->name;
            $phone = $request->phone;
            $address = $request->address;
            $area = $request->area;
        }

        if (Cart::instance('pos_shopping')->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }

        $request->validate([
            'payment_method' => ['required', 'string', 'in:Cash,Card,bKash,Nagad,Bank Transfer,COD'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $subtotal = Cart::instance('pos_shopping')->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        $discount = (float) Session::get('pos_discount', 0) + (float) Session::get('product_discount', 0);
        $shipping = ShippingCharge::where(['status' => 1, 'id' => $area])->first();
        if (!$shipping) {
            Toastr::error('Invalid shipping area selected', 'Failed!');
            return redirect()->back()->withInput();
        }

        $shippingfee = (float) $shipping->amount;
        $shippingarea = $shipping->name;
        $grandTotal = max(0, ($subtotal + $shippingfee) - $discount);
        $receivedAmount = $request->filled('paid_amount')
            ? max(0, (float) $request->paid_amount)
            : $grandTotal;
        $paidAmount = min($receivedAmount, $grandTotal);
        $paymentStatus = $paidAmount <= 0
            ? 'pending'
            : ($paidAmount + 0.00001 < $grandTotal ? 'partial' : 'paid');

        $exits_customer = Customer::where('phone', $phone)->select('phone', 'id')->first();
        if ($exits_customer) {
            $customer_id = $exits_customer->id;
        } else {
            $password = rand(111111, 999999);
            $store              = new Customer();
            $store->name        = $name;
            $store->slug        = $name;
            $store->phone       = $phone;
            $store->password    = bcrypt($password);
            $store->verify      = 1;
            $store->status      = 'active';
            $store->save();
            $customer_id = $store->id;
        }


        // order data save
        $order                   = new Order();
        $order->invoice_id       = rand(11111, 99999);
        $order->amount           = $grandTotal;
        $order->discount         = $discount ? $discount : 0;
        $order->shipping_charge  = $shippingfee;
        $order->customer_id      =  $customer_id;
        $order->order_status     = 1;
        $order->note             = $request->note;

        if (Schema::hasColumn('orders', 'marketing_source')) {
            $order->marketing_source = $request->marketing_source ?: 'admin';
        }

        $order->save();

        // shipping data save
        $shipping              =   new Shipping();
        $shipping->order_id    =   $order->id;
        $shipping->customer_id =   $customer_id;
        $shipping->name        =   $name;
        $shipping->phone       =   $phone;
        $shipping->address     =   $address;
        $shipping->area        =   $shippingarea;
        $shipping->save();

        // payment data save
        $payment                 = new Payment();
        $payment->order_id       = $order->id;
        $payment->customer_id    = $customer_id;
        $payment->payment_method = $request->payment_method;
        $payment->amount         = $paidAmount;
        $payment->payment_status = $paymentStatus;
        $payment->save();

        // order details data save
        foreach (Cart::instance('pos_shopping')->content() as $cart) {
            $order_details                   =   new OrderDetails();
            $order_details->order_id         =   $order->id;
            $order_details->product_id       =   $cart->id;
            $order_details->product_variable_id = $cart->options->product_variable_id ?? null;
            $order_details->product_name     =   $cart->name;
            $order_details->purchase_price   =   $cart->options->purchase_price;
            $order_details->product_discount =   $cart->options->product_discount;
            $order_details->sale_price       =   $cart->price;
            $order_details->product_color   =   $cart->options->product_color;
            $order_details->product_size    =   $cart->options->product_size;
            $order_details->product_model   =   $cart->options->product_model ?? null;
            $order_details->product_weight  =   $cart->options->product_weight ?? null;
            $order_details->selected_attributes = $cart->options->selected_attributes ?? [];
            $order_details->qty              =   $cart->qty;
            $order_details->save();
        }

        $this->profitLossService->recalculateOrder($order);

        Cart::instance('pos_shopping')->destroy();
        Session::forget('pos_shopping');
        Session::forget('pos_discount');
        Session::forget('product_discount');
        Toastr::success('Thanks, Your order place successfully', 'Success!');
        return redirect('admin/order/pending');
    }
    public function cart_add(Request $request)
    {
        $context = $this->cartContext($request);
        $cartInstance = $this->cartInstanceName($context);

        $selectedValueIds = $this->productAttributeService->sanitizeSelectedValueIds($request->input('attribute_values', []));
        $legacySelections = [
            'color' => $request->color,
            'size' => $request->size,
            'model' => $request->model,
            'weight' => $request->weight,
        ];
        $var_product = null;

        if ($request->filled('variant_barcode')) {
            $var_product = ProductVariable::query()
                ->with('selectedValues.attribute', 'media')
                ->where('barcode', trim((string) $request->variant_barcode))
                ->first();

            $product = Product::with('image', 'media')
                ->select('id', 'name', 'slug', 'new_price', 'old_price', 'purchase_price', 'type', 'variation_pricing_mode', 'stock')
                ->where('id', $var_product?->product_id)
                ->first();

            $selectedValueIds = $var_product ? array_values($this->productAttributeService->getSelectedValueMap($var_product)) : [];
            $resolvedSelections = [
                'color' => $var_product?->color,
                'size' => $var_product?->size,
                'model' => $var_product?->model,
                'weight' => $var_product?->weight,
            ];
        } else {
            $product = Product::with('image', 'media')->select('id', 'name', 'slug', 'new_price', 'old_price', 'purchase_price', 'type', 'variation_pricing_mode', 'stock')->where(['id' => $request->id])->first();
            $var_product = $this->productAttributeService->findVariantForProduct($request->id, $selectedValueIds, $legacySelections);
            $resolvedSelections = $this->productAttributeService->resolveLegacySelectionsFromValueIds($selectedValueIds, $legacySelections);
        }

        if (! $product) {
            return response()->json(['status' => 'notfound', 'message' => 'Product not found'], 404);
        }

        $selectedAttributes = $this->productAttributeService->summarizeSelections($selectedValueIds, $resolvedSelections);
        $qty = max(1, (int) $request->input('qty', 1));
        if ($product->type == 0) {
            $purchase_price = $product->variation_pricing_mode === 'same' ? $product->purchase_price : ($var_product?->purchase_price ?? 0);
            $old_price = $product->variation_pricing_mode === 'same' ? $product->old_price : ($var_product?->old_price ?? 0);
            $new_price = $product->variation_pricing_mode === 'same' ? $product->new_price : ($var_product?->new_price ?? 0);
            $stock = $var_product ? $var_product->stock : 0;
        } else {
            $purchase_price = $product->purchase_price;
            $old_price = $product->old_price;
            $new_price = $product->new_price;
            $stock = $product->stock;
        }

        $cartitem = Cart::instance($cartInstance)->content()->first(function ($item) use ($product, $var_product, $selectedAttributes) {
            if ((int) $item->id !== (int) $product->id) {
                return false;
            }

            $existingVariantId = $item->options->product_variable_id ?? null;
            if ((int) ($existingVariantId ?? 0) !== (int) ($var_product?->id ?? 0)) {
                return false;
            }

            $existingAttributes = $item->options->selected_attributes ?? [];

            return $existingAttributes == $selectedAttributes;
        });

        $cart_qty = $cartitem ? ((int) $cartitem->qty + $qty) : $qty;
        if ($stock < $cart_qty) {
            Toastr::error('Product stock limit over', 'Failed!');
            return response()->json(['status' => 'limitover', 'message' => 'Your stock limit is over']);
        }
        $cartinfo = Cart::instance($cartInstance)->add([
            'id' => $product->id,
            'name' => $product->name,
            'qty' => $qty,
            'price' => $new_price,
            'options' => [
                'slug' => $product->slug,
                'image' => $var_product?->primary_media_image ?? $product->primary_media_image ?? optional($product->image)->image,
                'old_price' => $old_price,
                'purchase_price' => $purchase_price,
                'product_discount' => 0,
                'product_size' => $resolvedSelections['size'],
                'product_color' => $resolvedSelections['color'],
                'product_model' => $resolvedSelections['model'],
                'product_weight' => $resolvedSelections['weight'],
                'product_variable_id' => $var_product?->id,
                'selected_attributes' => $selectedAttributes,
                'type' => $product->type,
                'sort_key' => (int) round(microtime(true) * 1000000),
            ],
        ]);
        //dd($cartinfo);

        return response()->json(compact('cartinfo'));
    }
    public function cart_content(Request $request)
    {
        $context = $this->cartContext($request);
        $cartinfo = $this->sortCartContent(Cart::instance($this->cartInstanceName($context))->content());
        if ($request->get('context') === 'workspace') {
            return view('backEnd.order.partials.workspace-cart-content', compact('cartinfo'));
        }

        return view('backEnd.order.cart_content', compact('cartinfo'));
    }
    public function cart_details(Request $request)
    {
        $context = $this->cartContext($request);
        $cartinfo = Cart::instance($this->cartInstanceName($context))->content();
        $subtotal = Cart::instance($this->cartInstanceName($context))->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        $shipping = Session::get($this->shippingSessionKey($context), 0);
        $productDiscount = $this->syncProductDiscountSession($context);
        $orderDiscount = (float) Session::get($this->orderDiscountSessionKey($context), 0);
        $total_discount = $orderDiscount + $productDiscount;

        return view('backEnd.order.cart_details', compact('cartinfo', 'subtotal', 'shipping', 'total_discount'));
    }
    public function cart_increment(Request $request)
    {
        $context = $this->cartContext($request);
        $currentQty = max((int) $request->qty, 1);
        $qty = $currentQty + 1;
        $cartinfo = Cart::instance($this->cartInstanceName($context))->update($request->id, $qty);
        return response()->json($cartinfo);
    }
    public function cart_decrement(Request $request)
    {
        $context = $this->cartContext($request);
        $currentQty = max((int) $request->qty, 1);
        $qty = max($currentQty - 1, 1);
        $cartinfo = Cart::instance($this->cartInstanceName($context))->update($request->id, $qty);
        return response()->json($cartinfo);
    }
    public function cart_remove(Request $request)
    {
        $context = $this->cartContext($request);
        $remove = Cart::instance($this->cartInstanceName($context))->remove($request->id);
        $cartinfo = Cart::instance($this->cartInstanceName($context))->content();
        return response()->json($cartinfo);
    }
    public function product_discount(Request $request)
    {
        $context = $this->cartContext($request);
        $cartInstance = $this->cartInstanceName($context);
        $cart = Cart::instance($cartInstance)->content()->where('rowId', $request->id)->first();

        if (! $cart) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $cartinfo = Cart::instance($cartInstance)->update($request->id, [
            'options' => [
                'slug' => $cart->options->slug,
                'image' => $cart->options->image,
                'old_price' => $cart->options->old_price,
                'purchase_price' => $cart->options->purchase_price,
                'product_discount' => $request->discount,
                'product_size' => $cart->options->product_size ?? null,
                'product_color' => $cart->options->product_color ?? null,
                'product_model' => $cart->options->product_model ?? null,
                'product_weight' => $cart->options->product_weight ?? null,
                'product_variable_id' => $cart->options->product_variable_id ?? null,
                'selected_attributes' => $cart->options->selected_attributes ?? [],
                'details_id' => $cart->options->details_id ?? null,
                'type' => $cart->options->type ?? null,
                'sort_key' => $cart->options->sort_key ?? null,
            ],
        ]);

        $updatedCart = Cart::instance($cartInstance)->content()->where('id', $cart->id)->sortByDesc('rowId')->first();

        return response()->json([
            'rowId' => $updatedCart->rowId ?? $request->id,
        ]);
    }
    private function resolveOrderDetailVariant(OrderDetails $orderDetail): ?ProductVariable
    {
        if ($orderDetail->product_variable_id) {
            $variant = ProductVariable::find($orderDetail->product_variable_id);
            if ($variant) {
                return $variant;
            }
        }

        return $this->productAttributeService->findVariantForProduct($orderDetail->product_id, [], [
            'color' => $orderDetail->product_color,
            'size' => $orderDetail->product_size,
            'model' => $orderDetail->product_model,
            'weight' => $orderDetail->product_weight,
        ]);
    }

    public function cart_shipping(Request $request)
    {
        $context = $this->cartContext($request);
        $shippingModel = ShippingCharge::where(['status' => 1, 'id' => $request->id])->first();
        if (! $shippingModel) {
            return response()->json(['message' => 'Invalid shipping area'], 422);
        }

        $shipping = $shippingModel->amount;
        Session::put($this->shippingSessionKey($context), $shipping);
        return response()->json($shipping);
    }

    public function cart_clear(Request $request)
    {
        $context = $this->cartContext($request);
        $cartinfo = Cart::instance($this->cartInstanceName($context))->destroy();
        Session::forget($this->shippingSessionKey($context));
        Session::forget($this->orderDiscountSessionKey($context));
        Session::forget($this->productDiscountSessionKey($context));
        return redirect()->back();
    }
    public function order_edit($invoice_id)
    {
        return redirect()->route('admin.order.workspace', ['invoice_id' => $invoice_id, 'tab' => 'manage']);
    }

    public function order_workspace($invoice_id)
    {
        $products = Product::select('id', 'name', 'new_price', 'product_code')->where(['status' => 1])->get();
        $shippingcharge = ShippingCharge::where('status', 1)->get();
        $order = Order::with([
            'orderdetails.image',
            'orderdetails.product.image',
            'orderdetails.product.media',
            'orderdetails.productVariable.media',
            'payment',
            'shipping',
            'customer',
        ])->where('invoice_id', $invoice_id)->firstOrFail();
        $pathaostore = [];
        $pathaocities = [];
        $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])
            ->select('id', 'type', 'client_id', 'client_secret', 'username', 'password', 'grant_type', 'url', 'token', 'status')
            ->first();

        if ($pathao_info && ($token = $this->ensurePathaoToken($pathao_info))) {
            $pathaostore = Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8',
                'Authorization' => 'Bearer ' . $token,
            ])->get('https://api-hermes.pathao.com/aladdin/api/v1/stores');

            $cityResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ])->get('https://api-hermes.pathao.com/aladdin/api/v1/city-list');

            $pathaocities = $cityResponse->json();
        }

        $this->clearCartState('workspace');
        $shippinginfo  = Shipping::where('order_id', $order->id)->first();
        Session::put($this->productDiscountSessionKey('workspace'), $order->discount);
        Session::put($this->shippingSessionKey('workspace'), $order->shipping_charge);
        $orderdetails = OrderDetails::with([
            'image',
            'product.image',
            'product.media',
            'productVariable.media',
        ])->where('order_id', $order->id)->get();
        //  dd($orderdetails);
        foreach ($orderdetails as $ordetails) {
            $resolvedImage = $ordetails->productVariable?->primary_media_image
                ?? $ordetails->product?->primary_media_image
                ?? $ordetails->image?->image
                ?? 'uploads/logo.png';

            $cartinfo = Cart::instance($this->cartInstanceName('workspace'))->add([
                'id' => $ordetails->product_id,
                'name' => $ordetails->product_name,
                'qty' => $ordetails->qty,
                'price' => $ordetails->sale_price,
                'options' => [
                    'image' => $resolvedImage,
                    'purchase_price' => $ordetails->purchase_price,
                    'product_discount' => $ordetails->product_discount,
                    'product_size'  => $ordetails->product_size,
                    'product_color' => $ordetails->product_color,
                    'product_model' => $ordetails->product_model,
                    'product_weight' => $ordetails->product_weight,
                    'product_variable_id' => $ordetails->product_variable_id,
                    'selected_attributes' => $ordetails->selected_attributes ?? [],
                    'details_id' => $ordetails->id,
                    'type' => $ordetails->type,
                    'sort_key' => $ordetails->id,
                ],
            ]);
        }
        $cartinfo  = $this->sortCartContent(Cart::instance($this->cartInstanceName('workspace'))->content());
        $processOrder = Order::where(['invoice_id' => $invoice_id])->select('id', 'invoice_id', 'order_status', 'courier', 'tracking_id')->with('orderdetails')->first();

        return view('backEnd.order.workspace', compact('products', 'cartinfo', 'shippingcharge', 'shippinginfo', 'order', 'processOrder', 'pathaostore', 'pathaocities'));
    }

    public function order_update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'area' => 'required',
        ]);

        $context = $this->cartContext($request);
        $cartInstance = $this->cartInstanceName($context);

        if (Cart::instance($cartInstance)->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }

        $subtotal = Cart::instance($cartInstance)->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        $discount = (float) Session::get($this->orderDiscountSessionKey($context), 0) + (float) Session::get($this->productDiscountSessionKey($context), 0);
        $shippingCharge = ShippingCharge::find($request->area);
        if (! $shippingCharge) {
            Toastr::error('Invalid shipping area selected', 'Failed!');
            return redirect()->back()->withInput();
        }

        $shippingfee = (float) $shippingCharge->amount;
        $shippingarea = $shippingCharge->name;

        $order = Order::find($request->order_id);
        if (! $order) {
            Toastr::error('Order not found', 'Failed!');
            return redirect()->back();
        }

        $cartContent = Cart::instance($cartInstance)->content();

        try {
            DB::transaction(function () use ($request, $subtotal, $shippingfee, $shippingarea, $discount, $order, $cartContent) {
            $customer = Customer::find($order->customer_id);
            $matchingCustomer = Customer::where('phone', $request->phone)
                ->select('id', 'phone')
                ->first();

            if ($matchingCustomer && (! $customer || $matchingCustomer->id !== $customer->id)) {
                $customer = Customer::find($matchingCustomer->id);
            }

            if (! $customer) {
                $password = rand(111111, 999999);
                $customer = new Customer();
                $customer->password = bcrypt($password);
                $customer->verify = 1;
                $customer->status = 'active';
            }

            $customer->name = $request->name;
            $customer->slug = $request->name;
            $customer->phone = $request->phone;
            $customer->save();

            $order->amount = ((float) $subtotal + $shippingfee) - $discount;
            $order->discount = $discount ?: 0;
            $order->shipping_charge = $shippingfee;
            $order->customer_id = $customer->id;
            $order->note = $request->note;

            if (Schema::hasColumn('orders', 'marketing_source')) {
                $order->marketing_source = $request->marketing_source ?: ($order->marketing_source ?: 'admin');
            }

            $order->save();

            $shipping = Shipping::firstOrNew(['order_id' => $order->id]);
            $shipping->customer_id = $customer->id;
            $shipping->name = $request->name;
            $shipping->phone = $request->phone;
            $shipping->address = $request->address;
            $shipping->area = $shippingarea;
            $shipping->save();

            $payment = Payment::firstOrNew(['order_id' => $order->id]);
            $payment->customer_id = $customer->id;
            $payment->amount = $order->amount;
            if (! $payment->payment_method) {
                $payment->payment_method = 'Cash On Delivery';
            }
            if (! $payment->payment_status) {
                $payment->payment_status = 'pending';
            }
            $payment->save();

            $existingDetailIds = $cartContent
                ->pluck('options.details_id')
                ->filter()
                ->map(fn($id) => (int) $id)
                ->all();

            OrderDetails::where('order_id', $order->id)
                ->when(!empty($existingDetailIds), function ($query) use ($existingDetailIds) {
                    $query->whereNotIn('id', $existingDetailIds);
                })
                ->when(empty($existingDetailIds), function ($query) {
                    return $query;
                })
                ->delete();

            foreach ($cartContent as $cart) {
                $detailId = $cart->options->details_id ?? null;
                $order_details = $detailId
                    ? OrderDetails::where('order_id', $order->id)->where('id', $detailId)->first()
                    : null;

                if (! $order_details) {
                    $order_details = new OrderDetails();
                    $order_details->order_id = $order->id;
                }

                $order_details->product_id = $cart->id;
                $order_details->product_variable_id = $cart->options->product_variable_id ?? null;
                $order_details->product_name = $cart->name;
                $order_details->purchase_price = $cart->options->purchase_price;
                $order_details->product_discount = $cart->options->product_discount;
                $order_details->sale_price = $cart->price;
                $order_details->product_color = $cart->options->product_color ?? null;
                $order_details->product_size = $cart->options->product_size ?? null;
                $order_details->product_model = $cart->options->product_model ?? null;
                $order_details->product_weight = $cart->options->product_weight ?? null;
                $order_details->selected_attributes = $cart->options->selected_attributes ?? [];
                $order_details->qty = $cart->qty;
                $order_details->save();
            }

            $this->profitLossService->recalculateOrder($order);
                $deliveredStatusIds = array_map('intval', $this->profitLossService->deliveredOrderStatusIds());
                $order->refresh();

                if (in_array((int) $order->order_status, $deliveredStatusIds, true)) {
                    $this->inventoryService->resyncOrderDelivery($order);
                }
            });
        } catch (\Throwable $exception) {
            Toastr::error($exception->getMessage(), 'Failed!');
            return redirect()->back()->withInput();
        }

        $this->clearCartState($context);
        Toastr::success('Thanks, Your order place successfully', 'Success!');
        return redirect()->route('admin.order.workspace', [
            'invoice_id' => $order->invoice_id,
            'tab' => 'manage',
        ]);
    }


    public function order_report(Request $request)
    {
        $users = User::where('status', 1)->get();
        $orders = OrderDetails::with('shipping', 'order')->whereHas('order', function ($query) {
            $query->where('order_status', 6);
        });
        if ($request->keyword) {
            $orders = $orders->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->user_id) {
            $orders = $orders->whereHas('order', function ($query) use ($request) {
                $query->where('user_id', $request->user_id);
            });
        }
        if ($request->start_date && $request->end_date) {
            $orders = $orders->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        $total_purchases = $orders->sum(\DB::raw('purchase_price * qty'));
        $total_item = $orders->sum('qty');
        $total_sales = $orders->sum(\DB::raw('sale_price * qty'));
        $orders = $orders->paginate(50);
        return view('backEnd.reports.order', compact('orders', 'users', 'total_purchases', 'total_item', 'total_sales'));
    }

    private function buildStockReportMetrics(Product $product): array
    {
        $variables = $product->relationLoaded('allVariables')
            ? $product->allVariables
            : collect();

        if ((int) $product->type === 0 && $variables->isNotEmpty()) {
            $stock = (int) $variables->sum('stock');

            if ($product->usesSharedVariationPricing()) {
                $purchaseTotal = (float) $product->purchase_price * $stock;
                $saleTotal = (float) $product->new_price * $stock;
            } else {
                $purchaseTotal = (float) $variables->sum(function ($variable) {
                    return (float) $variable->purchase_price * (int) $variable->stock;
                });
                $saleTotal = (float) $variables->sum(function ($variable) {
                    return (float) $variable->new_price * (int) $variable->stock;
                });
            }

            return [
                'stock' => $stock,
                'purchase_total' => $purchaseTotal,
                'sale_total' => $saleTotal,
                'variables' => $variables,
            ];
        }

        $stock = (int) ($product->stock ?? 0);

        return [
            'stock' => $stock,
            'purchase_total' => (float) ($product->purchase_price ?? 0) * $stock,
            'sale_total' => (float) ($product->new_price ?? 0) * $stock,
            'variables' => collect(),
        ];
    }

    public function stock_report(Request $request)
    {
        $products = Product::query()
            ->select('id', 'name', 'new_price', 'purchase_price', 'stock', 'type', 'variation_pricing_mode', 'category_id', 'created_at')
            ->where('status', 1);

        if ($request->keyword) {
            $products = $products->where('name', 'LIKE', '%' . $request->keyword . "%");
        }

        if ($request->category_id) {
            $products = $products->where('category_id', $request->category_id);
        }

        if ($request->start_date && $request->end_date) {
            $products = $products->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $metricProducts = (clone $products)
            ->with(['allVariables:id,product_id,size,color,stock,purchase_price,new_price'])
            ->get();

        $total_purchase = 0;
        $total_stock = 0;
        $total_price = 0;

        foreach ($metricProducts as $product) {
            $metrics = $this->buildStockReportMetrics($product);
            $total_purchase += $metrics['purchase_total'];
            $total_stock += $metrics['stock'];
            $total_price += $metrics['sale_total'];
        }

        $products = $products
            ->with(['allVariables:id,product_id,size,color,stock,purchase_price,new_price'])
            ->paginate(50);

        $products->getCollection()->transform(function ($product) {
            $metrics = $this->buildStockReportMetrics($product);
            $product->report_stock = $metrics['stock'];
            $product->report_purchase_total = $metrics['purchase_total'];
            $product->report_total_value = $metrics['sale_total'];
            $product->report_variables = $metrics['variables'];

            return $product;
        });

        $categories = Category::where('status', 1)->get();

        return view('backEnd.reports.stock', compact('products', 'categories', 'total_purchase', 'total_stock', 'total_price'));
    }
    public function expense_report(Request $request)
    {
        $data = Expense::where('status', 1);
        if ($request->keyword) {
            $data = $data->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->category_id) {
            $data = $data->where('expense_cat_id', $request->category_id);
        }
        if ($request->start_date && $request->end_date) {
            $data = $data->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        $data = $data->paginate(50);
        $categories = ExpenseCategories::where('status', 1)->get();
        return view('backEnd.reports.expense', compact('data', 'categories'));
    }
    public function loss_profit(Request $request)
    {
        $supportsProfitColumns = $this->profitLossService->supportsOrderProfitColumns();
        $summary = $this->profitLossService->summarizeCompletedOrders(
            $request->start_date,
            $request->end_date,
        );

        $ordersQuery = Order::query()
            ->with('shipping')
            ->whereIn('order_status', $this->profitLossService->deliveredOrderStatusIds());

        if ($request->keyword) {
            $ordersQuery->where(function ($query) use ($request) {
                $query->where('invoice_id', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhereHas('shipping', function ($shippingQuery) use ($request) {
                        $shippingQuery->where('name', 'LIKE', '%' . $request->keyword . '%')
                            ->orWhere('phone', 'LIKE', '%' . $request->keyword . '%');
                    });
            });
        }

        if ($request->start_date && $request->end_date) {
            $ordersQuery->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->source && Schema::hasColumn('orders', 'marketing_source')) {
            if ($request->source === 'organic') {
                $ordersQuery->whereIn('marketing_source', ['organic', 'direct', 'referral']);
            } else {
                $ordersQuery->where('marketing_source', $request->source);
            }
        }

        if ($request->profit_status && $supportsProfitColumns) {
            $ordersQuery->where('profit_status', $request->profit_status);
        }

        $orders = $ordersQuery->latest()->paginate(30)->withQueryString();

        $orders->getCollection()->transform(function ($order) {
            $metrics = $this->profitLossService->calculateOrderMetrics($order);

            $order->report_product_cost = $metrics['product_cost'];
            $order->report_additional_cost = $metrics['additional_cost'];
            $order->report_gross_profit = $metrics['gross_profit'];
            $order->report_net_profit = $metrics['net_profit'];
            $order->report_profit_status = $metrics['profit_status'];

            return $order;
        });

        $expenseBreakdown = collect();

        if (Schema::hasTable('expenses')) {
            $expenseQuery = DB::table('expenses')
                ->selectRaw('COALESCE(expense_categories.name, "Uncategorized") as category_name, SUM(expenses.amount) as total_amount, COUNT(*) as expense_count');

            if (Schema::hasTable('expense_categories')) {
                $expenseQuery->leftJoin('expense_categories', 'expense_categories.id', '=', 'expenses.expense_cat_id');
            }

            if (Schema::hasColumn('expenses', 'status')) {
                $expenseQuery->where('expenses.status', 1);
            }

            if ($request->start_date && $request->end_date) {
                $expenseQuery->whereBetween('expenses.created_at', [$request->start_date, $request->end_date]);
            }

            $expenseBreakdown = $expenseQuery
                ->groupBy('category_name')
                ->orderByDesc('total_amount')
                ->get();
        }

        $total_expense = $summary['operating_expense'];
        $total_purchase = $summary['product_cost'];
        $total_sales = $summary['sales_revenue'];

        return view('backEnd.reports.loss_profit', compact(
            'summary',
            'total_expense',
            'total_purchase',
            'total_sales',
            'orders',
            'expenseBreakdown',
            'supportsProfitColumns',
        ));
    }

    public function recalculate_profit_loss_snapshots()
    {
        Artisan::call('profit-loss:recalculate-orders');

        Toastr::success('Success', 'Profit/loss snapshots recalculated successfully');

        return redirect()->route('admin.loss_profit');
    }

    public function zero_cost_audit(Request $request)
    {
        $details = OrderDetails::query()
            ->with(['order.shipping'])
            ->where(function ($query) {
                $query->whereNull('purchase_price')
                    ->orWhere('purchase_price', '<=', 0);
            });

        if ($request->keyword) {
            $details->where(function ($query) use ($request) {
                $query->where('product_name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhereHas('order', function ($orderQuery) use ($request) {
                        $orderQuery->where('invoice_id', 'LIKE', '%' . $request->keyword . '%')
                            ->orWhereHas('shipping', function ($shippingQuery) use ($request) {
                                $shippingQuery->where('name', 'LIKE', '%' . $request->keyword . '%')
                                    ->orWhere('phone', 'LIKE', '%' . $request->keyword . '%');
                            });
                    });
            });
        }

        if ($request->status) {
            $details->whereHas('order', function ($query) use ($request) {
                $query->where('order_status', $request->status);
            });
        }

        if ($request->start_date && $request->end_date) {
            $details->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $summary = [
            'line_count' => (clone $details)->count(),
            'order_count' => (clone $details)->distinct('order_id')->count('order_id'),
            'qty_total' => (clone $details)->sum('qty'),
            'sales_total' => (clone $details)->sum(DB::raw('sale_price * qty')),
        ];

        $details = $details->latest()->paginate(30)->withQueryString();
        $orderStatuses = OrderStatus::orderBy('id')->get();

        return view('backEnd.reports.zero_cost_audit', compact('details', 'summary', 'orderStatuses'));
    }
}
