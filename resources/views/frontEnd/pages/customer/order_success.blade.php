@extends ('frontEnd.layouts.master')
@section('title', 'Order Success')
@push('css')
<style>
    .order-success-shell {
        padding: 28px 0 40px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
    }

    .order-success-card,
    .order-success-panel {
        background: #fff;
        border: 1px solid #e5edf6;
        border-radius: 24px;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.06);
    }

    .order-success-card {
        padding: 28px;
        text-align: center;
        margin-bottom: 18px;
    }

    .order-success-icon {
        width: 84px;
        height: 84px;
        margin: 0 auto 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: #fff;
        font-size: 34px;
        box-shadow: 0 18px 34px rgba(34, 197, 94, 0.24);
    }

    .order-success-title {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .order-success-copy {
        color: #475569;
        margin: 0 auto;
        max-width: 620px;
        line-height: 1.7;
    }

    .order-success-meta {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-top: 22px;
    }

    .order-success-meta-item {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #f8fafc;
        padding: 14px;
        text-align: left;
    }

    .order-success-meta-item span {
        display: block;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 6px;
    }

    .order-success-meta-item strong {
        color: #0f172a;
        font-size: 15px;
    }

    .order-success-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(300px, .9fr);
        gap: 18px;
        align-items: start;
    }

    .order-success-panel {
        padding: 22px;
    }

    .order-success-panel h5 {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 16px;
    }

    .order-summary-table {
        width: 100%;
        margin: 0;
    }

    .order-summary-table tr + tr td,
    .order-summary-table tr + tr th {
        border-top: 1px solid #edf2f7;
    }

    .order-summary-table td,
    .order-summary-table th {
        padding: 14px 0;
        vertical-align: top;
        background: transparent;
    }

    .order-summary-product {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        color: #0f172a;
        font-weight: 600;
    }

    .order-summary-product-main {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .order-summary-product-thumb {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        flex-shrink: 0;
    }

    .order-summary-product-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .order-summary-product-content {
        min-width: 0;
    }

    .order-summary-product small {
        display: block;
        color: #64748b;
        font-weight: 500;
        margin-top: 4px;
    }

    .order-summary-total th,
    .order-summary-total td {
        font-weight: 800;
        color: #0f172a;
    }

    .order-info-list {
        display: grid;
        gap: 12px;
    }

    .order-info-item {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #f8fafc;
        padding: 14px 16px;
    }

    .order-info-item span {
        display: block;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 6px;
    }

    .order-info-item strong,
    .order-info-item p {
        margin: 0;
        color: #0f172a;
    }

    .order-success-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 22px;
    }

    .order-success-actions .btn {
        min-width: 180px;
        padding: 12px 18px;
        border-radius: 999px;
        font-weight: 700;
    }

    @media (max-width: 991.98px) {
        .order-success-meta,
        .order-success-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .order-success-shell {
            padding-top: 18px;
        }

        .order-success-card,
        .order-success-panel {
            border-radius: 18px;
        }

        .order-success-card,
        .order-success-panel {
            padding: 18px;
        }

        .order-success-title {
            font-size: 22px;
        }

        .order-success-meta {
            gap: 10px;
        }

        .order-success-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush
@section('content')
@php
    $payments = App\Models\Payment::where('order_id', $order->id)->first();
    $shippingInfo = $order->shipping;
    $netTotal = (float) $order->amount - (float) $order->shipping_charge + (float) $order->discount;
@endphp
<section class="order-success-shell">
    <div class="container">
        <div class="order-success-card">
            <div class="order-success-icon">
                <i class="fa-solid fa-check"></i>
            </div>
            <h1 class="order-success-title">Order Confirmed</h1>
            <p class="order-success-copy">
                Your order has been received successfully. One of our representatives will contact you shortly to confirm delivery details.
            </p>

            <div class="order-success-meta">
                <div class="order-success-meta-item">
                    <span>Invoice ID</span>
                    <strong>{{ $order->invoice_id }}</strong>
                </div>
                <div class="order-success-meta-item">
                    <span>Order Date</span>
                    <strong>{{ $order->created_at->format('d M Y') }}</strong>
                </div>
                <div class="order-success-meta-item">
                    <span>Phone</span>
                    <strong>{{ $shippingInfo?->phone ?: 'N/A' }}</strong>
                </div>
                <div class="order-success-meta-item">
                    <span>Grand Total</span>
                    <strong>৳ {{ number_format((float) $order->amount, 2) }}</strong>
                </div>
            </div>

            <div class="order-success-actions">
                <a href="{{ route('home') }}" class="btn btn-dark">Back To Home</a>
                <a href="{{ route('customer.order_track') }}" class="btn btn-outline-dark">Track Order</a>
            </div>
        </div>

        <div class="order-success-grid">
            <div class="order-success-panel">
                <h5>Order Summary</h5>
                <table class="order-summary-table">
                    <tbody>
                        @foreach($order->orderdetails as $value)
                        @php
                            $productImage = $value->productVariable?->primary_media_image
                                ?? $value->product?->primary_media_image
                                ?? $value->image?->image
                                ?? 'uploads/logo.png';
                        @endphp
                        <tr>
                            <td>
                                <div class="order-summary-product">
                                    <div class="order-summary-product-main">
                                        <div class="order-summary-product-thumb">
                                            <img src="{{ asset($productImage) }}" alt="{{ $value->product_name }}">
                                        </div>
                                        <div class="order-summary-product-content">
                                            {{ $value->product_name }}
                                            <small>Qty: {{ $value->qty }}</small>
                                        </div>
                                    </div>
                                    <strong>৳ {{ number_format((float) $value->sale_price * (int) $value->qty, 2) }}</strong>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <th>Net Total</th>
                            <td class="text-end"><strong id="net_total">৳{{ number_format($netTotal, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Shipping Cost</th>
                            <td class="text-end"><strong id="cart_shipping_cost">৳{{ number_format((float) $order->shipping_charge, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Discount</th>
                            <td class="text-end"><strong>৳{{ number_format((float) $order->discount, 2) }}</strong></td>
                        </tr>
                        <tr class="order-summary-total">
                            <th>Grand Total</th>
                            <td class="text-end"><strong id="grand_total">৳{{ number_format((float) $order->amount, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="order-success-panel">
                <h5>Order Details</h5>
                <div class="order-info-list">
                    <div class="order-info-item">
                        <span>Payment Method</span>
                        <strong>{{ $payments?->payment_method ?: 'Cash On Delivery' }}</strong>
                    </div>
                    <div class="order-info-item">
                        <span>Customer</span>
                        <p>{{ $shippingInfo?->name ?: 'N/A' }}</p>
                    </div>
                    <div class="order-info-item">
                        <span>Delivery Address</span>
                        <p>{{ $shippingInfo?->address ?: 'N/A' }}</p>
                        @if($shippingInfo?->area)
                        <p>{{ $shippingInfo->area }}</p>
                        @endif
                    </div>
                    <div class="order-info-item">
                        <span>Next Step</span>
                        <p>Our team will call you shortly for delivery confirmation.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('script')
<script src="{{asset('frontEnd/')}}/js/parsley.min.js"></script>
<script src="{{asset('frontEnd/')}}/js/form-validation.init.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Unique key for this order
        const orderKey = 'purchase_{{ $order->invoice_id }}';

        // Check if this order has already fired
        if (!sessionStorage.getItem(orderKey)) {
            // Mark this order as fired
            sessionStorage.setItem(orderKey, 'true');

            // Collect items
            const items = [
                @foreach ($order->orderdetails as $item)
                {
                    id: "{{ $item->product_id }}",
                    name: {!! json_encode($item->product_name) !!},
                    price: parseFloat({{ $item->sale_price }}),
                    brand: {!! json_encode($item->options->brand ?? 'N/A') !!},
                    category: {!! json_encode($item->options->category ?? 'N/A') !!},
                    attributes: {!! json_encode($item->selected_attributes ?? []) !!},
                    size: {!! json_encode($item->product_size ?? '') !!},
                    color: {!! json_encode($item->product_color ?? '') !!},
                    model: {!! json_encode($item->product_model ?? '') !!},
                    weight: {!! json_encode($item->product_weight ?? '') !!},
                    quantity: {{ $item->qty ?? 1 }}
                }@if (!$loop->last),@endif
                @endforeach
            ];

            const totalValue = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const eventId = `purchase_{{ $order->invoice_id }}_${Date.now()}`;
            const metaConfig = window.frontendConfig?.meta || {};
            const marketingConfig = window.frontendConfig?.marketing || {};
            const getCookie = (name) => document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]*)'))?.[2] || '';
            const ttclid = new URLSearchParams(window.location.search).get('ttclid') || '';

            console.log('💰 Purchase triggered:', items, 'Total:', totalValue, 'EventID:', eventId);

            // --- Facebook Pixel (Browser-side) ---
            if (typeof fbq === 'function' && metaConfig.facebookPixelId) {
                fbq('track', 'Purchase', {
                    value: totalValue,
                    currency: "BDT",
                    contents: items.map(item => ({
                        id: item.id,
                        quantity: item.quantity,
                        item_price: item.price,
                        item_name: item.name
                    }))
                }, { eventID: eventId });
            }


            if (typeof ttq !== 'undefined' && metaConfig.tiktokPixelId) {
                ttq.track('Purchase', {
                    value: totalValue,
                    currency: 'BDT',
                    contents: items.map(item => ({
                        content_id: item.id,
                        content_name: item.name,
                        content_type: 'product',
                        quantity: item.quantity,
                        price: item.price
                    }))
                });
            }

            if (typeof gtag === 'function' && marketingConfig.ga4MeasurementId) {
                gtag('event', 'purchase', {
                    transaction_id: "{{ $order->invoice_id }}",
                    value: totalValue,
                    currency: 'BDT',
                    items: items.map(item => ({
                        item_id: item.id,
                        item_name: item.name,
                        price: item.price,
                        quantity: item.quantity
                    }))
                });
                if (window.frontendConfig?.marketingRoutes?.googleEventLog) {
                    fetch(window.frontendConfig.marketingRoutes.googleEventLog, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            event_name: 'Purchase',
                            order_id: "{{ $order->invoice_id }}",
                            value: totalValue,
                            currency: 'BDT',
                            source_url: window.location.href
                        })
                    }).catch(() => null);
                }
            }

            if (typeof gtag === 'function' && marketingConfig.googleAdsId && marketingConfig.googleAdsConversionLabel) {
                gtag('event', 'conversion', {
                    send_to: `${marketingConfig.googleAdsId}/${marketingConfig.googleAdsConversionLabel}`,
                    value: totalValue,
                    currency: 'BDT',
                    transaction_id: "{{ $order->invoice_id }}"
                });
                if (window.frontendConfig?.marketingRoutes?.googleEventLog) {
                    fetch(window.frontendConfig.marketingRoutes.googleEventLog, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            event_name: 'GoogleAdsConversion',
                            event_id: eventId,
                            order_id: "{{ $order->invoice_id }}",
                            value: totalValue,
                            currency: 'BDT',
                            source_url: window.location.href
                        })
                    }).catch(() => null);
                }
            }

            if (metaConfig.tiktokCapiEnabled) {
                fetch('{{ route("tiktok.purchase_capi") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        transaction_id: "{{ $order->invoice_id }}",
                        value: totalValue,
                        currency: "BDT",
                        items: items,
                        event_source_url: window.location.href,
                        client_user_agent: navigator.userAgent,
                        client_ip_address: "{{ request()->ip() }}",
                        ttp: getCookie('_ttp'),
                        ttclid: ttclid
                    })
                })
                .catch(err => console.error('TikTok Purchase fetch error:', err));
            }

            // --- Facebook Conversion API (Server-side) ---
            if (metaConfig.facebookCapiEnabled) {
                fetch('{{ route("facebook.purchase_capi") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    event_id: eventId,
                    transaction_id: "{{ $order->invoice_id }}",
                    value: totalValue,
                    currency: "BDT",
                    items: items,
                    event_source_url: window.location.href,
                    user_data: {
                        name: {!! json_encode($order->shipping->name ?? '') !!},
                        phone: {!! json_encode($order->shipping->phone ?? '') !!},
                        email: {!! json_encode($order->shipping->email ?? '') !!},
                        address: {!! json_encode($order->shipping->address ?? '') !!},
                        area: {!! json_encode($order->shipping->area ?? '') !!},
                        country: "BD",
                        client_ip_address: "{{ request()->ip() }}",
                        client_user_agent: navigator.userAgent,
                        fbp: getCookie('_fbp'),
                        fbc: getCookie('_fbc')
                    }
                })
            })
            .then(async res => {
                const text = await res.text();
                try {
                    const data = JSON.parse(text);
                    console.log('✅ CAPI Purchase success:', data);
                } catch (err) {
                    console.error('❌ CAPI Purchase error. Raw response:', text);
                }
            })
            .catch(err => console.error('❌ CAPI Purchase fetch error:', err));
            }
        } else {
            console.log('⚠️ Purchase already fired for order {{ $order->invoice_id }}');
        }
    });
</script>






@endpush
