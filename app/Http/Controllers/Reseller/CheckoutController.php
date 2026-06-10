<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FraudCheckerConfig;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ResellerCart;
use App\Models\Shipping;
use App\Models\ShippingCharge;
use App\Services\AppService\ProfitLossService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Auth;

class CheckoutController extends Controller
{
    public function __construct(private ProfitLossService $profitLossService)
    {
    }

    private function resellerId(): int
    {
        return Auth::guard('reseller')->id();
    }

    public function checkout()
    {
        $items = ResellerCart::with('productVariable')
            ->where('reseller_id', $this->resellerId())
            ->get();

        if ($items->isEmpty()) {
            Toastr::warning('Cart khali, age product add korun', 'Cart empty');
            return redirect()->route('reseller.products.index');
        }

        if ($this->unavailableCartItems($items)->isNotEmpty()) {
            Toastr::error('Cart e stopped/out of stock variation ache. Age cart update korun.', 'Unavailable variation');
            return redirect()->route('reseller.cart.index');
        }

        $shippingCharges = ShippingCharge::where('status', 1)->get();
        $totalSell   = $items->sum('subtotal');
        $totalMargin = $items->sum('margin');

        return view('resellerPanel.checkout.index', compact('items', 'shippingCharges', 'totalSell', 'totalMargin'));
    }

    public function placeOrder(Request $request)
    {
        $this->validate($request, [
            'name'         => 'required|string|max:155',
            'phone'        => 'required|string|max:55',
            'address'      => 'required|string',
            'shipping_id'  => 'nullable|integer',
        ]);

        $items = ResellerCart::with('productVariable')
            ->where('reseller_id', $this->resellerId())
            ->get();
        if ($items->isEmpty()) {
            Toastr::error('Cart khali', 'Failed');
            return redirect()->route('reseller.products.index');
        }

        if ($this->unavailableCartItems($items)->isNotEmpty()) {
            Toastr::error('Cart e stopped/out of stock variation ache. Age cart update korun.', 'Unavailable variation');
            return redirect()->route('reseller.cart.index');
        }

        $reseller = Auth::guard('reseller')->user();
        $shipping_area = $request->shipping_id ? ShippingCharge::find($request->shipping_id) : null;
        $shippingFee   = $shipping_area ? $shipping_area->amount : 0;

        $totalSell   = $items->sum('subtotal');
        $totalMargin = $items->sum('margin');

        $order = DB::transaction(function () use ($request, $items, $reseller, $shipping_area, $shippingFee, $totalSell, $totalMargin) {

            // customer find/create (phone diye)
            $customer = Customer::where('phone', $request->phone)->first();
            if (!$customer) {
                $customer = new Customer();
                $customer->name     = $request->name;
                $customer->slug     = Str::slug($request->name . '-' . rand(100, 999));
                $customer->phone    = $request->phone;
                $customer->password = bcrypt(rand(111111, 999999));
                $customer->verify   = 1;
                $customer->status   = 'active';
                $customer->save();
            }

            // order
            $order = new Order();
            $order->invoice_id      = rand(100000, 999999);
            $order->ip_address      = $request->ip();
            $order->amount          = $totalSell + $shippingFee;   // customer ja dibe
            $order->discount        = 0;
            $order->shipping_charge = $shippingFee;
            $order->customer_id     = $customer->id;
            $order->reseller_id     = $reseller->id;
            $order->reseller_margin = $totalMargin;
            $order->order_status    = 1;
            $order->note            = $request->note;
            $order->save();

            // shipping
            $shipping = new Shipping();
            $shipping->order_id    = $order->id;
            $shipping->customer_id = $customer->id;
            $shipping->name        = $request->name;
            $shipping->phone       = $request->phone;
            $shipping->address     = $request->address;
            $shipping->area        = $shipping_area ? $shipping_area->name : 'N/A';
            $shipping->save();

            // payment (COD)
            $payment = new Payment();
            $payment->order_id       = $order->id;
            $payment->customer_id    = $customer->id;
            $payment->payment_method = 'cod';
            $payment->amount         = $order->amount;
            $payment->payment_status = 'pending';
            $payment->save();

            // order details — cart theke
            foreach ($items as $item) {
                $product = Product::find($item->product_id);

                $od = new OrderDetails();
                $od->order_id            = $order->id;
                $od->product_id          = $item->product_id;
                $od->product_variable_id = $item->product_variable_id;
                $od->product_name        = $item->product_name;
                $od->purchase_price      = $product->purchase_price ?? 0; // platform cost
                $od->product_color       = $item->color;
                $od->product_size        = $item->size;
                $od->selected_attributes = $item->selected_attributes ?? [];  // dynamic attributes
                $od->type                = $product->type ?? 1;
                $od->sale_price          = $item->sell_price;             // reseller-er bikroy mullo
                $od->qty                 = $item->qty;
                $od->save();
            }

            // cart clear
            ResellerCart::where('reseller_id', $reseller->id)->delete();

            return $order;
        });

        // profit-loss snapshot (existing service)
        try {
            $this->profitLossService->recalculateOrder($order);
        } catch (\Throwable $e) {
            // snapshot fail korle order atke jabe na
        }

        Toastr::success('Order place hoyeche! Invoice #' . $order->invoice_id, 'Success');
        return redirect()->route('reseller.orders.success', $order->id);
    }

    public function success($id)
    {
        $order = Order::with(['orderdetails', 'shipping'])
            ->where('reseller_id', $this->resellerId())
            ->findOrFail($id);

        return view('resellerPanel.checkout.success', compact('order'));
    }

    private function unavailableCartItems($items)
    {
        return $items->filter(function (ResellerCart $item) {
            if (! $item->product_variable_id && empty($item->selected_value_ids)) {
                return false;
            }

            if (! $item->productVariable) {
                return true;
            }

            return ! $item->productVariable->is_available_for_reseller;
        });
    }

    public function myOrders(Request $request)
    {
        $resolvedDateRange = $this->resolveOrderDateRange($request->get('date'));
        $datePreset = $resolvedDateRange ? $request->get('date') : null;
        $activeFilters = [
            'status' => $request->filled('status') ? (int) $request->get('status') : null,
            'start_date' => $datePreset ? null : $request->get('start_date'),
            'end_date' => $datePreset ? null : $request->get('end_date'),
            'date' => $datePreset,
            'date_label' => $resolvedDateRange['label'] ?? null,
        ];

        $ordersQuery = Order::where('reseller_id', $this->resellerId())
            ->with([
                'shipping',
                'status',
                'orderdetails.product.media',
                'orderdetails.product.image',
                'orderdetails.productVariable.media',
                'orderdetails.image',
            ])
            ->withCount('orderdetails');

        if ($activeFilters['status']) {
            $ordersQuery->where('order_status', $activeFilters['status']);
        }

        if ($resolvedDateRange) {
            $ordersQuery->whereDate('created_at', '>=', $resolvedDateRange['start'])
                ->whereDate('created_at', '<=', $resolvedDateRange['end']);
        } else {
            if ($request->filled('start_date')) {
                $ordersQuery->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $ordersQuery->whereDate('created_at', '<=', $request->end_date);
            }
        }

        $orders = $ordersQuery->latest()->paginate(20)->withQueryString();
        $orderStatuses = OrderStatus::orderBy('id')->get();
        $datePresets = [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'last_7_days' => 'Last 7 Days',
            'this_month' => 'This Month',
        ];

        // warm-cache theke courier report (kono API call chhara) — fast first paint.
        // jeta cache-e nei sudhu oitar jonno page-e AJAX call hobe.
        $reports = [];
        $config = FraudCheckerConfig::where('status', 1)->select('url')->first();
        if ($config && $config->url) {
            $phones = $orders->pluck('shipping.phone')->filter()->unique();
            foreach ($phones as $rawPhone) {
                $phone = preg_replace('/\D+/', '', (string) $rawPhone) ?: (string) $rawPhone;
                $cached = Cache::get('fraud_checker:' . md5($phone . '|' . $config->url));
                if ($cached) {
                    $reports[$rawPhone] = $this->courierSummary($cached);
                }
            }
        }

        return view('resellerPanel.orders.index', compact(
            'orders',
            'reports',
            'orderStatuses',
            'datePresets',
            'activeFilters',
        ));
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

    // cached fraud payload theke summary + good/bad status ber kore
    private function courierSummary(array $payload): array
    {
        $courierData = $payload['courierData']
            ?? ($payload['data']['courierData'] ?? ($payload['data'] ?? ($payload['result'] ?? [])));
        $summary = $courierData['summary']
            ?? ($payload['summary'] ?? ($payload['data']['summary'] ?? []));

        $total     = (int) ($summary['total_parcel'] ?? 0);
        $success   = (int) ($summary['success_parcel'] ?? 0);
        $cancelled = (int) ($summary['cancelled_parcel'] ?? 0);
        $ratio     = isset($summary['success_ratio'])
            ? (float) $summary['success_ratio']
            : ($total > 0 ? round($success / $total * 100) : 0);

        if ($total <= 0)      { $label = 'New Customer';   $color = 'secondary'; }
        elseif ($ratio >= 80) { $label = 'Good Customer';  $color = 'success'; }
        elseif ($ratio >= 50) { $label = 'Average';        $color = 'warning'; }
        else                  { $label = 'Risky Customer'; $color = 'danger'; }

        return compact('total', 'success', 'cancelled', 'ratio', 'label', 'color');
    }

    // Order details — admin-er moto full info
    public function show($id)
    {
        $order = Order::with([
            'orderdetails.product.media',
            'orderdetails.product.image',
            'shipping',
            'payment',
            'customer',
            'status',
        ])
            ->where('reseller_id', $this->resellerId())
            ->findOrFail($id);

        return view('resellerPanel.orders.show', compact('order'));
    }

    // Fraud / courier report — customer phone-er courier-wise delivery history
    public function fraudCheck(Request $request)
    {
        $request->validate(['phone' => ['required', 'string']]);

        $phone = preg_replace('/\D+/', '', (string) $request->phone) ?: (string) $request->phone;
        $config = FraudCheckerConfig::where('status', 1)->select('url', 'api_key')->first();

        if (! $config || ! $config->url || ! $config->api_key) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Fraud checker API key not configured',
            ], 422);
        }

        $cacheKey = 'fraud_checker:' . md5($phone . '|' . $config->url);
        if ($cached = Cache::get($cacheKey)) {
            return response()->json(array_merge($cached, ['cached' => true]), 200);
        }

        $token = trim((string) $config->api_key);
        if (! str_starts_with(strtolower($token), 'bearer ')) {
            $token = 'Bearer ' . $token;
        }

        $response = Http::withHeaders([
            'Authorization' => $token,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ])->post(trim((string) $config->url), ['phone' => $phone]);

        $payload = $response->json() ?: [
            'status'  => $response->successful() ? 'success' : 'failed',
            'message' => trim((string) $response->body()) ?: 'Unexpected response from fraud checker',
        ];

        if ($response->successful()) {
            Cache::put($cacheKey, $payload, now()->addMinutes(15));
        }

        return response()->json(array_merge($payload, ['cached' => false]), $response->status());
    }

    // Edit form — sudhu Pending (1) order
    public function edit($id)
    {
        $order = Order::with(['orderdetails', 'shipping'])
            ->where('reseller_id', $this->resellerId())
            ->findOrFail($id);

        if ($order->order_status != 1) {
            Toastr::error('Sudhu Pending order edit kora jay', 'Edit korা jabe na');
            return redirect()->route('reseller.orders.show', $order->id);
        }

        $shippingCharges = ShippingCharge::where('status', 1)->get();

        return view('resellerPanel.orders.edit', compact('order', 'shippingCharges'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::with('orderdetails', 'shipping', 'payment')
            ->where('reseller_id', $this->resellerId())
            ->findOrFail($id);

        if ($order->order_status != 1) {
            Toastr::error('Sudhu Pending order edit kora jay', 'Edit kora jabe na');
            return redirect()->route('reseller.orders.show', $order->id);
        }

        $this->validate($request, [
            'name'                 => 'required|string|max:155',
            'phone'                => 'required|string|max:55',
            'address'              => 'required|string',
            'shipping_id'          => 'nullable|integer',
            'note'                 => 'nullable|string',
            'items'                => 'required|array',
            'items.*.qty'          => 'required|integer|min:1',
            'items.*.sale_price'   => 'required|numeric|min:0',
        ]);

        $shipping_area = $request->shipping_id ? ShippingCharge::find($request->shipping_id) : null;
        $shippingFee   = $shipping_area ? $shipping_area->amount : 0;

        DB::transaction(function () use ($request, $order, $shipping_area, $shippingFee) {
            $itemTotal = 0;
            $marginTotal = 0;

            foreach ($order->orderdetails as $detail) {
                if (! isset($request->items[$detail->id])) {
                    continue;
                }
                $row  = $request->items[$detail->id];
                $qty  = (int) $row['qty'];
                $sell = (float) $row['sale_price'];

                $detail->qty        = $qty;
                $detail->sale_price = $sell;
                $detail->save();

                // margin = (sell - reseller wholesale cost) * qty
                $wholesale = Product::where('id', $detail->product_id)->value('wholesale_price') ?? 0;
                $itemTotal   += $sell * $qty;
                $marginTotal += ($sell - $wholesale) * $qty;
            }

            $order->amount          = $itemTotal + $shippingFee;
            $order->shipping_charge = $shippingFee;
            $order->reseller_margin = $marginTotal;
            $order->note            = $request->note;
            $order->save();

            // shipping update
            if ($order->shipping) {
                $order->shipping->name    = $request->name;
                $order->shipping->phone   = $request->phone;
                $order->shipping->address = $request->address;
                $order->shipping->area    = $shipping_area ? $shipping_area->name : ($order->shipping->area ?? 'N/A');
                $order->shipping->save();
            }

            // payment amount sync
            if ($order->payment) {
                $order->payment->amount = $order->amount;
                $order->payment->save();
            }
        });

        try {
            $this->profitLossService->recalculateOrder($order->fresh());
        } catch (\Throwable $e) {
            // snapshot fail korle atke jabe na
        }

        Toastr::success('Order update hoyeche', 'Success');
        return redirect()->route('reseller.orders.show', $order->id);
    }

    // Cancel — sudhu Pending (1) order
    public function cancel($id)
    {
        $order = Order::where('reseller_id', $this->resellerId())->findOrFail($id);

        if ($order->order_status != 1) {
            Toastr::error('Sudhu Pending order cancel kora jay', 'Cancel kora jabe na');
            return redirect()->route('reseller.orders.show', $order->id);
        }

        $order->order_status = 9; // Cancelled
        $order->save();

        Toastr::success('Order cancel kora hoyeche', 'Cancelled');
        return redirect()->route('reseller.orders.show', $order->id);
    }
}
