@extends('frontEnd.layouts.master') @section('title', 'Customer Checkout')



@push('css')
    <style>
        .mobile-search {
            display: none;
        }
    </style>
    <style>
        .cart_table img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 8px;
        }

        .cart_table .product-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cart_table .quantity {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .cart_table .quantity input {
            width: 50px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .cart_table .quantity button {
            width: 30px;
            height: 30px;
            border: none;
            background-color: #f0f0f0;
            color: #000;
            font-weight: bold;
            border-radius: 4px;
        }

        .cart_table .quantity button:hover {
            background-color: #d3d3d3;
        }

        .cart_table tfoot th,
        .cart_table tfoot td {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .cart_table tr:hover {
            background-color: #f1f1f1;
        }

        .alinur {
            color: #198754;
            font-weight: 600;
        }

        .cart_remove {
            cursor: pointer;
            font-size: 18px;
        }

        .cart_remove:hover {
            color: #dc3545;
        }

        .cartlist .cart-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border: 1px solid #eee;
        }

        .cartlist .quantity {
            display: flex;
            align-items: center;
        }

        .cartlist .qty-input {
            width: 45px;
            border: none;
            background: #f8f9fa;
            font-weight: 600;
        }

        .cartlist .cart-item-row:hover {
            background-color: #f9f9ff;
            transition: 0.3s;
        }

        .cartlist .btn-sm {
            width: 28px;
            height: 28px;
            line-height: 1;
        }

        .card {
            border-radius: 1rem;
        }

        .alinur {
            color: #007bff;
            font-weight: bold;
        }

        @media (max-width: 991.98px) {
            .chheckout-section {
                margin-top: 20px;
            }
        }

        @media only screen and (min-width: 320px) and (max-width: 767px) {
            .chheckout-section {
                margin-top: 12px;
            }

            .footer_nav {
                display: none !important;
            }

        }
    </style>

    <link rel="stylesheet" href="{{ asset('frontEnd/css/select2.min.css') }}" />
    @endpush @section('content')
    <section class="chheckout-section">
        @php
            $subtotal = Cart::instance('shopping')->subtotal();
            $subtotal = str_replace(',', '', $subtotal);
            $subtotal = str_replace('.00', '', $subtotal);
            $shipping = Session::get('shipping') ? Session::get('shipping') : 0;
            $coupon = Session::get('coupon_amount') ? Session::get('coupon_amount') : 0;
            $discount = Session::get('discount') ? Session::get('discount') : 0;
        @endphp
        <div class="container">
            <div class="row">
                <div class="col-sm-5 cus-order-2">
                    <div class="checkout-shipping">
                        <style>
                            .compact-form .form-label {
                                font-size: 14px;
                                line-height: 1.2;
                                margin-bottom: 4px;
                            }

                            .compact-form input,
                            .compact-form select {
                                padding: 6px 10px;
                                font-size: 14px;
                                height: 36px;
                            }

                            .compact-form .card-header h6 {
                                font-size: 14px;
                                line-height: 1.4;
                                margin-bottom: 0;
                            }

                            .compact-form button {
                                font-size: 14px;
                                padding: 6px 18px;
                            }

                            .compact-form .form-control,
                            .compact-form .form-select {
                                border-radius: 0.25rem;
                            }

                            .compact-form .card-body {
                                padding: 16px;
                            }

                            .compact-form .card-header {
                                padding: 12px 16px;
                            }

                            .compact-form .row {
                                --bs-gutter-y: 0.5rem;
                            }
                        </style>
                        <style>
                            .area-option {
                                border: 1px solid #ddd;
                                border-radius: 8px;
                                padding: 12px 16px;
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                cursor: pointer;
                                transition: all 0.2s ease;
                                background-color: #fff;
                            }

                            .area-option:hover {
                                border-color: #0d6efd;
                                background-color: #f9fbff;
                            }

                            .area-option input[type="radio"] {
                                display: none;
                            }

                            .area-option.selected {
                                border-color: #0d6efd;
                                background-color: #e8f0ff;
                            }

                            .area-name {
                                font-weight: 600;
                                font-size: 15px;
                                color: #333;
                            }

                            .area-price {
                                font-weight: 600;
                                color: #555;
                            }
                        </style>
                        <form action="{{ route('customer.ordersave') }}" method="POST" data-parsley-validate=""
                            class="compact-form">
                            @csrf
                            <input type="hidden" name="marketing_source" id="marketing_source" value="">
                            <input type="hidden" name="utm_source" id="utm_source" value="">
                            <input type="hidden" name="utm_medium" id="utm_medium" value="">
                            <input type="hidden" name="utm_campaign" id="utm_campaign" value="">
                            <input type="hidden" name="utm_term" id="utm_term" value="">
                            <input type="hidden" name="utm_content" id="utm_content" value="">
                            <input type="hidden" name="landing_url" id="landing_url" value="">
                            <input type="hidden" name="referrer_url" id="referrer_url" value="">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light border-bottom">
                                    <h6 class="text-dark fw-bold">
                                        Fill out the form below to place your order
                                    </h6>

                                </div>

                                <div class="card-body">
                                    <div class="row g-2">

                                        {{-- Name --}}
                                        <div class="col-md-12">
                                            <label for="name" class="form-label fw-semibold">Enter Your Name *</label>
                                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                                class="form-control @error('name') is-invalid @enderror" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Phone --}}
                                        <div class="col-md-12">
                                            <label for="phone" class="form-label fw-semibold">Enter Your Number *</label>
                                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                                minlength="11" maxlength="11" pattern="0[0-9]+"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                title="Enter an 11-digit number starting with 0" required>

                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Address --}}
                                        <div class="col-md-12">
                                            <label for="address" class="form-label fw-semibold">Enter Your Adress *</label>
                                            <input type="text" id="address" name="address" value="{{ old('address') }}"
                                                class="form-control @error('address') is-invalid @enderror" required>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Area --}}
                                        {{-- <div class="col-md-12 mb-2">
                                        <label class="form-label fw-semibold mb-2">Select Aria  *</label>

                                        <div class="d-flex flex-column gap-2">
                                            @foreach ($shippingcharge as $key => $value)
                                            <label class="area-option" for="area-{{ $value->id }}">
                                                <div class="area-name">
                                                    <input type="radio" name="area" id="area-{{ $value->id }}"
                                                        value="{{ $value->id }}" required {{ old('area')==$value->id ?
                                                    'checked' : '' }}
                                                    >
                                                    {{ $value->name }}
                                                </div>
                                                <div class="area-price">৳{{ $value->amount }}</div>
                                            </label>
                                            @endforeach
                                        </div>

                                        @error('area')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div> --}}

                                        <div class="col-md-12 mb-2">
                                            <label class="form-label fw-semibold mb-2">Select Area *</label>

                                            <div class="btn-group-vertical w-100" role="group">

                                                @foreach ($shippingcharge as $value)
                                                    <input type="radio" class="btn-check" name="area"
                                                        id="area-{{ $value->id }}" value="{{ $value->id }}"
                                                        autocomplete="off" required
                                                        {{ old('area') == $value->id ? 'checked' : '' }}>

                                                    <label
                                                        class="btn btn-outline-dark d-flex justify-content-between align-items-center mb-2"
                                                        for="area-{{ $value->id }}">
                                                        <span>{{ $value->name }}</span>
                                                        <span>৳{{ $value->amount }}</span>
                                                    </label>
                                                @endforeach

                                            </div>

                                            @error('area')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>





                                        <style>
                                            @keyframes softPulse {
                                                0% {
                                                    transform: scale(1);
                                                }

                                                50% {
                                                    transform: scale(1.03);
                                                }

                                                100% {
                                                    transform: scale(1);
                                                }
                                            }
                                        </style>



                                        <div class="col-md-12 text-center mt-2">
                                            <button type="submit"
                                                style="
                                                 background:#FB5621;
                                                 font-size:20px;
                                                 font-weight:900;
                                                 animation: softPulse 2s infinite ease-in-out;
                                                 "
                                                class="btn orderNowBtn rounded-pill shadow-sm w-100 text-white">
                                                <i class="fa-solid fa-check-circle me-1"></i> Order now
                                            </button>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
                <!-- col end -->
                <div class="col-sm-7 cust-order-1">
                    <div class="cart_details table-responsive-sm">
                        <div class="card">
                            <div class="card-header">
                                <h5>Order Information </h5>
                            </div>
                            <div class="card-body cartlist">
                                <style>
                                    .cart_table img {
                                        width: 60px;
                                        height: 60px;
                                        object-fit: cover;
                                        border-radius: 6px;
                                        margin-right: 8px;
                                    }

                                    .cart_table .product-info {
                                        display: flex;
                                        align-items: center;
                                        gap: 10px;
                                    }

                                    .cart_table .quantity {
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        gap: 6px;
                                    }

                                    .cart_table .quantity input {
                                        width: 50px;
                                        text-align: center;
                                        border: 1px solid #ddd;
                                        border-radius: 4px;
                                    }

                                    .cart_table .quantity button {
                                        width: 30px;
                                        height: 30px;
                                        border: none;
                                        background-color: #f0f0f0;
                                        color: #000;
                                        font-weight: bold;
                                        border-radius: 4px;
                                    }

                                    .cart_table .quantity button:hover {
                                        background-color: #d3d3d3;
                                    }

                                    .cart_table tfoot th,
                                    .cart_table tfoot td {
                                        background-color: #f8f9fa;
                                        font-weight: bold;
                                    }

                                    .cart_table tr:hover {
                                        background-color: #f1f1f1;
                                    }

                                    .alinur {
                                        color: #198754;
                                        font-weight: 600;
                                    }

                                    .cart_remove {
                                        cursor: pointer;
                                        font-size: 18px;
                                    }

                                    .cart_remove:hover {
                                        color: #dc3545;
                                    }
                                </style>

                                <table class="cart_table table table-bordered table-striped text-center mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 10%;"> Delete </th>
                                            <th style="width: 75%;"> Product </th>

                                            <th style="width: 15%;"> Price </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach (Cart::instance('shopping')->content() as $value)
                                            <tr>
                                                <td>
                                                    <a class="cart_remove text-danger" data-id="{{ $value->rowId }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </td>
                                                <td class="text-start">
                                                    <a href="{{ route('product', $value->options->slug) }}"
                                                        class="text-dark text-decoration-none">
                                                        <div class="product-info">
                                                            <img src="{{ asset($value->options->image) }}"
                                                                alt="{{ $value->name }}">
                                                            <div>
                                                                <div class="fw-semibold">{{ Str::limit($value->name, 35) }}
                                                                </div>
                                                                <div class="small text-muted">
                                                                    @php
                                                                        $selectedAttributes = $value->options->selected_attributes ?? [];
                                                                    @endphp
                                                                    @if (!empty($selectedAttributes))
                                                                        @include('frontEnd.components.common.selected-attributes', ['selectedAttributes' => $selectedAttributes])
                                                                    @else
                                                                        @if ($value->options->product_size)
                                                                            <div>Size: {{ $value->options->product_size }}</div>
                                                                        @endif
                                                                        @if ($value->options->product_color)
                                                                            <div>Color: {{ $value->options->product_color }}</div>
                                                                        @endif
                                                                        @if ($value->options->product_model)
                                                                            <div>Model: {{ $value->options->product_model }}</div>
                                                                        @endif
                                                                        @if ($value->options->product_weight)
                                                                            <div>Weight: {{ $value->options->product_weight }}</div>
                                                                        @endif
                                                                    @endif

                                                                    @if ($value->options->writer_id)
                                                                        <div>Writer: {{ $value->options->writer_id }}</div>
                                                                    @endif

                                                                    @if ($value->options->publisher_id)
                                                                        <div>Publisher: {{ $value->options->publisher_id }}</div>
                                                                    @endif
                                                                    @if ($value->options->subject_id)
                                                                        <div>Subject: {{ $value->options->subject_id }}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <div class="quantity">
                                                        <button class="cart_decrement"
                                                            data-id="{{ $value->rowId }}">−</button>
                                                        <input type="text" value="{{ $value->qty }}" readonly />
                                                        <button class="cart_increment"
                                                            data-id="{{ $value->rowId }}">+</button>
                                                    </div>
                                                </td>



                                                <td>
                                                    <span class="alinur">৳</span>
                                                    <strong>{{ $value->price }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-end px-4">Total</th>
                                            <td class="px-4">
                                                <span class="text-dark">৳</span>
                                                <strong>{{ $subtotal }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-end px-4"> Delivary Charge</th>
                                            <td class="px-4">
                                                <span class="text-dark">৳</span>
                                                <strong>{{ $shipping }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-end px-4">Discount</th>
                                            <td class="px-4">
                                                <span class="text-dark">৳</span>
                                                <strong>{{ $discount + $coupon }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-end px-4"> Subtotal </th>
                                            <td class="px-4">
                                                <span class="text-dark">৳</span>
                                                <strong>{{ $subtotal + $shipping - ($discount + $coupon) }}</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <form
                                    action="@if (Session::get('coupon_used')) {{ route('customer.coupon_remove') }} @else {{ route('customer.coupon') }} @endif"
                                    method="POST" class="checkout-coupon-form mt-3">
                                    @csrf

                                    <div class="card border-0 shadow-sm rounded-3">
                                        <div class="card-body p-3">


                                            <div class="input-group">
                                                <input type="text" name="coupon_code"
                                                    class="form-control border-primary shadow-none py-2 px-3 fw-semibold @if (Session::get('coupon_used')) text-success @endif"
                                                    placeholder="@if (Session::get('coupon_used')) {{ Session::get('coupon_used') }} @else Apply Coupon Code @endif"
                                                    @if (Session::get('coupon_used')) readonly @endif>

                                                <button type="submit"
                                                    class="btn @if (Session::get('coupon_used')) btn-danger @else btn-primary @endif px-4 fw-semibold">
                                                    @if (Session::get('coupon_used'))
                                                        Remove
                                                    @else
                                                        Apply
                                                    @endif
                                                </button>
                                            </div>

                                            @if (Session::get('coupon_used'))
                                                <div class="mt-2 text-success small">
                                                    ✅ Coupon "<strong>{{ Session::get('coupon_used') }}</strong>"
                                                    applied successfully!
                                                </div>
                                            @endif

                                            @error('coupon_code')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- col end -->
            </div>
        </div>
    </section>
    @php
        //dd(Cart::instance('shopping')->content());
    @endphp
    @endsection @push('script')
    <script src="{{ asset('frontEnd') }}/js/parsley.min.js"></script>
    <script src="{{ asset('frontEnd') }}/js/form-validation.init.js"></script>
    <script src="{{ asset('frontEnd') }}/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".select2").select2();
        });
    </script>
    <script>
        $(document).on("change", "input[name='area']", function() {
            var id = $(this).val();
            $.ajax({
                type: "GET",
                url: "{{ route('shipping.charge') }}",
                data: {
                    id: id
                },
                success: function(response) {
                    $(".cartlist").html(response);
                },
                error: function(xhr) {
                    console.error("Error loading shipping charge:", xhr);
                }
            });
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const items = [
                @foreach (Cart::instance('shopping')->content() as $cartInfo)
                    {
                        id: "{{ $cartInfo->id }}",
                        name: {!! json_encode($cartInfo->name) !!},
                        price: {{ $cartInfo->price }},
                        attributes: {!! json_encode($cartInfo->options->selected_attributes ?? []) !!},
                        size: {!! json_encode($cartInfo->options->product_size ?? '') !!},
                        color: {!! json_encode($cartInfo->options->product_color ?? '') !!},
                        model: {!! json_encode($cartInfo->options->product_model ?? '') !!},
                        weight: {!! json_encode($cartInfo->options->product_weight ?? '') !!},
                        quantity: {{ $cartInfo->qty ?? 0 }}
                    }
                    @if (!$loop->last)
                        ,
                    @endif
                @endforeach
            ];

            const totalValue = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const metaConfig = window.frontendConfig?.meta || {};
            const marketingConfig = window.frontendConfig?.marketing || {};
            const marketingRoutes = window.frontendConfig?.marketingRoutes || {};
            const getCookie = (name) => document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]*)'))?.[2] || '';
            const ttclid = new URLSearchParams(window.location.search).get('ttclid') || '';

            // Unique event ID for deduplication
            const eventId = `begincheckout_{{ session()->getId() }}_${Date.now()}`;

            console.log('🛒 BeginCheckout triggered:', items, 'Total:', totalValue, 'EventID:', eventId);

            // --- Facebook Pixel (browser-side) ---
            if (typeof fbq === 'function' && metaConfig.facebookPixelId) {
                fbq('track', 'InitiateCheckout', {
                    currency: "BDT",
                    value: totalValue,
                    contents: items.map(item => ({
                        id: item.id,
                        name: item.name,
                        quantity: item.quantity,
                        price: item.price
                    }))
                }, {
                    eventID: eventId
                });
            }


            if (typeof ttq !== 'undefined' && metaConfig.tiktokPixelId) {
                ttq.track('InitiateCheckout', {
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
                gtag('event', 'begin_checkout', {
                    currency: 'BDT',
                    value: totalValue,
                    items: items.map(item => ({
                        item_id: item.id,
                        item_name: item.name,
                        price: item.price,
                        quantity: item.quantity
                    }))
                });
                if (marketingRoutes.googleEventLog) {
                    fetch(marketingRoutes.googleEventLog, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            event_name: 'InitiateCheckout',
                            value: totalValue,
                            currency: 'BDT',
                            source_url: window.location.href
                        })
                    }).catch(() => null);
                }
            }

            if (metaConfig.tiktokCapiEnabled) {
                fetch('{{ route('tiktok.begin_checkout_capi') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        currency: "BDT",
                        value: totalValue,
                        items: items,
                        event_source_url: window.location.href,
                        client_user_agent: navigator.userAgent,
                        client_ip_address: "{{ request()->ip() }}",
                        ttp: getCookie('_ttp'),
                        ttclid: ttclid
                    })
                })
                .catch(err => console.error('TikTok BeginCheckout fetch error:', err));
            }

            // --- Facebook Conversion API (server-side) ---
            if (metaConfig.facebookCapiEnabled) {
                fetch('{{ route('facebook.begin_checkout_capi') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        event_id: eventId,
                        currency: "BDT",
                        value: totalValue,
                        items: items,
                        event_source_url: window.location.href,
                        client_user_agent: navigator.userAgent,
                        client_ip_address: "{{ request()->ip() }}",
                        fbp: getCookie('_fbp'),
                        fbc: getCookie('_fbc')
                    })
                })
                .then(async res => {
                    const text = await res.text();
                    try {
                        const data = JSON.parse(text);
                        console.log('✅ CAPI BeginCheckout success response:', data);
                    } catch (err) {
                        console.error('❌ CAPI BeginCheckout error. Raw response:', text);
                    }
                })
                .catch(err => console.error('❌ CAPI BeginCheckout fetch error:', err));
            }

            function syncCheckoutLead() {
                if (!marketingRoutes.abandonedCartSync || !marketingConfig.abandonedCartEnabled) {
                    return;
                }

                const utm = JSON.parse(localStorage.getItem('utm_campaign_data') || '{}');
                fetch(marketingRoutes.abandonedCartSync, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: document.querySelector('input[name="name"]')?.value || '',
                        phone: document.querySelector('input[name="phone"]')?.value || '',
                        email: document.querySelector('input[name="email"]')?.value || '',
                        landing_url: utm.landing_url || window.location.href,
                        utm_source: utm.utm_source || '',
                        utm_medium: utm.utm_medium || '',
                        utm_campaign: utm.utm_campaign || '',
                        utm_term: utm.utm_term || '',
                        utm_content: utm.utm_content || ''
                    })
                }).catch(() => null);
            }

            function populateOrderAttributionFields() {
                const utm = JSON.parse(localStorage.getItem('utm_campaign_data') || '{}');

                document.querySelector('input[name="marketing_source"]')?.setAttribute('value', utm.marketing_source || 'direct');
                document.querySelector('input[name="utm_source"]')?.setAttribute('value', utm.utm_source || '');
                document.querySelector('input[name="utm_medium"]')?.setAttribute('value', utm.utm_medium || '');
                document.querySelector('input[name="utm_campaign"]')?.setAttribute('value', utm.utm_campaign || '');
                document.querySelector('input[name="utm_term"]')?.setAttribute('value', utm.utm_term || '');
                document.querySelector('input[name="utm_content"]')?.setAttribute('value', utm.utm_content || '');
                document.querySelector('input[name="landing_url"]')?.setAttribute('value', utm.landing_url || window.location.href);
                document.querySelector('input[name="referrer_url"]')?.setAttribute('value', utm.referrer_url || document.referrer || '');
            }

            populateOrderAttributionFields();

            ['name', 'phone', 'email'].forEach(field => {
                document.querySelector(`input[name="${field}"]`)?.addEventListener('blur', syncCheckoutLead);
            });
        });
    </script>
@endpush
