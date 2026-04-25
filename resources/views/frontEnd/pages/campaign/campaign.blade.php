<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{{ $generalsetting->name }}</title>
        <link rel="shortcut icon" href="{{asset($generalsetting->favicon)}}" type="image/x-icon" />
        @php
            $facebookPixel = collect($pixels ?? [])->first(function ($pixel) {
                return ($pixel->provider ?? 'facebook') === 'facebook' && !empty($pixel->code);
            });
        @endphp
        <!-- fot awesome -->
        <link rel="stylesheet" href="{{ asset('frontEnd/campaign/css') }}/all.css" />
        <!-- core css -->
        <link rel="stylesheet" href="{{ asset('frontEnd/campaign/css') }}/bootstrap.min.css" />
        <link rel="stylesheet" href="{{ asset('backEnd/assets/css/toastr.min.css') }}" />
        <!-- owl carousel -->
        <link rel="stylesheet" href="{{ asset('frontEnd/campaign/css') }}/owl.theme.default.css" />
        <link rel="stylesheet" href="{{ asset('frontEnd/campaign/css') }}/owl.carousel.min.css" />
        <!-- owl carousel -->
        <link rel="stylesheet" href="{{ asset('frontEnd/campaign/css') }}/style.css" />
        <link rel="stylesheet" href="{{ asset('frontEnd/campaign/css') }}/responsive.css" />
        @if($facebookPixel)
        <script>
            !function(f,b,e,v,n,t,s)
            {
                if(f.fbq)return;
                n=f.fbq=function(){
                    n.callMethod ? n.callMethod.apply(n,arguments) : n.queue.push(arguments);
                };
                if(!f._fbq)f._fbq=n;
                n.push=n;
                n.loaded=!0;
                n.version='2.0';
                n.queue=[];
                t=b.createElement(e);
                t.async=!0;
                t.src=v;
                s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s);
            }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

            fbq('init', '{{ $facebookPixel->code }}');
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id={{ $facebookPixel->code }}&ev=PageView&noscript=1" />
        </noscript>
        @endif
        
        <meta name="app-url" content="{{route('campaign',$campaign->slug)}}" />
        <meta name="robots" content="index, follow" />
        <meta name="description" content="{{$campaign->short_description}}" />
        <meta name="keywords" content="{{ $campaign->slug }}" />
        
        <!-- Twitter Card data -->
        <meta name="twitter:card" content="product" />
        <meta name="twitter:site" content="{{$campaign->name}}" />
        <meta name="twitter:title" content="{{$campaign->name}}" />
        <meta name="twitter:description" content="{{ $campaign->short_description}}" />
        <meta name="twitter:creator" content="" />
        <meta property="og:url" content="{{route('campaign',$campaign->slug)}}" />
        <meta name="twitter:image" content="{{asset($campaign->banner)}}" />
        
        <!-- Open Graph data -->
        <meta property="og:title" content="{{$campaign->name}}" />
        <meta property="og:type" content="product" />
        <meta property="og:url" content="{{route('campaign',$campaign->slug)}}" />
        <meta property="og:image" content="{{asset($campaign->banner)}}" />
        <meta property="og:description" content="{{ $campaign->short_description}}" />
        <meta property="og:site_name" content="{{$campaign->name}}" />
        <style>
            :root {
                --campaign-bg: #fff8f1;
                --campaign-card: #ffffff;
                --campaign-line: #f1d8c2;
                --campaign-text: #2f241d;
                --campaign-muted: #7a685a;
                --campaign-accent: #d97706;
                --campaign-accent-dark: #b45309;
                --campaign-soft: #fff1e4;
                --campaign-shadow: 0 24px 55px rgba(146, 64, 14, 0.10);
            }

            body {
                background:
                    radial-gradient(circle at top left, rgba(251, 191, 36, 0.18), transparent 24%),
                    linear-gradient(180deg, #fff7ed 0%, #fffdf8 34%, #fff8f1 100%);
                color: var(--campaign-text);
            }

            .banner-section,
            .short-des,
            .description-section,
            .whychoose-section,
            .review-section,
            .price-section,
            .form_sec {
                padding: 32px 0;
            }

            .campaign_banner,
            .description-inner,
            .whychoose-inner,
            .rev_inn,
            .offer_price,
            .form_inn,
            .checkout-shipping .card,
            .cart_details .card {
                border-radius: 28px;
                border: 1px solid var(--campaign-line);
                background: rgba(255, 255, 255, 0.94);
                box-shadow: var(--campaign-shadow);
                overflow: hidden;
            }

            .campaign_banner {
                padding: 24px;
                background: #fff;
            }

            .campaign-hero-grid {
                display: block;
            }

            .banner_title {
                padding: 0 0 20px;
                text-align: center;
            }

            .banner_title p {
                color: var(--campaign-muted);
                font-size: 16px;
                line-height: 1.7;
                margin: 14px auto 0;
                max-width: 42rem;
            }

            .banner_title h2,
            .description-title h2,
            .whychoose-title h2,
            .rev_title h2,
            .offer_title h2 {
                color: var(--campaign-text);
                font-weight: 800;
                line-height: 1.2;
                letter-spacing: -.02em;
                margin-bottom: 0;
            }

            .banner_title h2 {
                font-size: clamp(28px, 4vw, 46px);
            }

            .campaign-trust-list {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 18px;
                justify-content: center;
            }

            .campaign-trust-list span {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 14px;
                border-radius: 999px;
                background: #fff;
                border: 1px solid #f3d5b4;
                color: var(--campaign-text);
                font-weight: 700;
                font-size: 14px;
            }

            .banner-img img,
            .review_item img {
                border-radius: 22px;
                width: 100%;
                display: block;
                object-fit: cover;
            }

            .banner-img img {
                max-height: 620px;
            }

            .short-des-title,
            .main-description,
            .main-whychoose {
                color: var(--campaign-text);
                line-height: 1.8;
                font-size: 17px;
            }

            .short-des-title img,
            .main-description img,
            .main-whychoose img {
                max-width: 100%;
                height: auto;
                display: block;
                border-radius: 20px;
                margin: 18px auto;
            }

            .short-des-title iframe,
            .main-description iframe,
            .main-whychoose iframe,
            .short-des-title video,
            .main-description video,
            .main-whychoose video {
                max-width: 100%;
                width: 100%;
                border: 0;
                border-radius: 20px;
                margin: 18px auto;
            }

            .short-des-title,
            .description-inner,
            .whychoose-inner,
            .rev_inn,
            .offer_price {
                padding: 26px;
            }

            .content-section-header {
                margin-bottom: 18px;
                text-align: center;
            }

            .content-section-header span {
                display: inline-flex;
                align-items: center;
                width: fit-content;
                padding: 8px 14px;
                border-radius: 999px;
                background: var(--campaign-soft);
                color: var(--campaign-accent-dark);
                font-size: 12px;
                font-weight: 800;
                letter-spacing: .08em;
                text-transform: uppercase;
            }

            .ord_btn {
                text-align: center;
            }

            .order_place,
            .confirm_order {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                border: 0;
                border-radius: 999px;
                padding: 10px 22px;
                background: linear-gradient(135deg, var(--campaign-accent) 0%, var(--campaign-accent-dark) 100%);
                color: #fff !important;
                font-weight: 700;
                font-size: 14px;
                letter-spacing: .01em;
                box-shadow: 0 16px 30px rgba(217, 119, 6, 0.22);
                transition: transform .18s ease, box-shadow .18s ease;
            }

            .order_place:hover,
            .confirm_order:hover {
                transform: translateY(-1px);
                box-shadow: 0 22px 36px rgba(217, 119, 6, 0.26);
            }

            .campaign-price-box {
                display: inline-flex;
                flex-direction: column;
                gap: 10px;
                padding: 18px 22px;
                border-radius: 24px;
                background: linear-gradient(135deg, #fff7ed 0%, #fffbf5 100%);
                border: 1px solid #fed7aa;
                min-width: min(100%, 420px);
            }

            .campaign-old-price {
                color: #9a3412;
                font-size: 16px;
                font-weight: 700;
            }

            .campaign-current-price {
                color: var(--campaign-accent-dark);
                font-size: clamp(28px, 4vw, 38px);
                font-weight: 900;
                line-height: 1.15;
            }

            .form_inn {
                padding: 24px;
            }

            .checkout-shipping .card-header,
            .cart_details .card-header {
                background: linear-gradient(180deg, #fff8ef 0%, #fff4e7 100%);
                border-bottom: 1px solid var(--campaign-line);
                padding: 18px 22px;
            }

            .checkout-shipping .card-body,
            .cart_details .card-body {
                padding: 22px;
            }

            .checkout-shipping label {
                display: block;
                margin-bottom: 8px;
                color: var(--campaign-text);
                font-weight: 700;
            }

            .checkout-shipping .form-control,
            .checkout-shipping select {
                min-height: 48px;
                border-radius: 16px;
                border: 1px solid #e8d4bf;
                background: #fffdfb;
                box-shadow: none;
            }

            .checkout-shipping .form-control:focus,
            .checkout-shipping select:focus {
                border-color: #f59e0b;
                box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.12);
            }

            .selector {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .selector-item_label {
                border-radius: 999px !important;
                border: 1px solid #e8d4bf !important;
                background: #fff !important;
                color: var(--campaign-text) !important;
                padding: 10px 16px !important;
                font-weight: 700;
            }

            .selector-item_radio:checked + .selector-item_label {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
                border-color: #d97706 !important;
                color: #fff !important;
                box-shadow: 0 12px 24px rgba(217, 119, 6, 0.18);
            }

            .cart_table {
                margin-bottom: 0;
                border-color: #f1dfce;
            }

            .cart_table thead th {
                background: #fff7ed;
                color: var(--campaign-text);
                font-weight: 800;
                border-bottom: 1px solid #f1dfce;
            }

            .cart_table tbody td,
            .cart_table tfoot td,
            .cart_table tfoot th {
                vertical-align: middle;
                border-color: #f6e6d6;
            }

            .cart_table tfoot th,
            .cart_table tfoot td {
                background: #fffdfa;
            }

            .qty-cart.vcart-qty .quantity {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px;
                border-radius: 999px;
                background: var(--campaign-soft);
                border: 1px solid #f4cfab;
            }

            .qty-cart.vcart-qty .quantity button {
                width: 32px;
                height: 32px;
                border: 0;
                border-radius: 50%;
                background: #fff;
                color: var(--campaign-text);
                font-weight: 800;
            }

            .qty-cart.vcart-qty .quantity input {
                width: 34px;
                border: 0;
                background: transparent;
                text-align: center;
                font-weight: 700;
                color: var(--campaign-text);
            }

            .potro_font {
                margin: 0;
                font-weight: 800;
                color: var(--campaign-text);
            }

            @media (max-width: 991.98px) {
                .banner-section,
                .short-des,
                .description-section,
                .whychoose-section,
                .review-section,
                .price-section,
                .form_sec {
                    padding: 22px 0;
                }

                .form_inn {
                    padding: 16px;
                }
            }

            @media (max-width: 767.98px) {
                .campaign_banner,
                .description-inner,
                .whychoose-inner,
                .rev_inn,
                .offer_price,
                .form_inn,
                .checkout-shipping .card,
                .cart_details .card {
                    border-radius: 20px;
                }

                .short-des-title,
                .main-description,
                .main-whychoose {
                    font-size: 15px;
                    line-height: 1.7;
                }

                .checkout-shipping .card-body,
                .cart_details .card-body {
                    padding: 16px;
                }

                .campaign-current-price {
                    font-size: 26px;
                }

                .checkout-shipping .form-group:has(.confirm_order) {
                    text-align: center;
                }
            }
        </style>
    </head>

    <body>
         @php
            $subtotal = Cart::instance('shopping')->subtotal();
            $subtotal=str_replace(',','',$subtotal);
            $subtotal=str_replace('.00', '',$subtotal);
            $shipping = Session::get('shipping')?Session::get('shipping'):0;
        @endphp

        <section class="banner-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-sm-10">
                        <div class="campaign_banner">
                            <div class="campaign-hero-grid">
                                <div class="banner_title">
                                    <div class="mb-3">
                                        <span class="badge rounded-pill text-bg-warning px-3 py-2" style="font-size:12px; letter-spacing:.06em;">ORGANIC SPECIAL OFFER</span>
                                    </div>
                                    <h2>{{$campaign->name}}</h2>
                                    <p>Prakritik upadan ar trusted quality diye toiri ekta simple, clean organic product offer.</p>
                                    <div class="campaign-trust-list">
                                        <span><i class="fa-solid fa-leaf"></i> Organic Ingredients</span>
                                        <span><i class="fa-solid fa-circle-check"></i> Trusted Quality</span>
                                        <span><i class="fa-solid fa-truck-fast"></i> Fast Delivery</span>
                                    </div>
                                    <div class="mt-4">
                                        <a href="#order_form" class="order_place">Order Now <i class="fa-solid fa-arrow-down"></i></a>
                                    </div>
                                </div>
                                <div class="banner-img">
                                    <img src="{{asset($campaign->banner)}}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- banner section end -->

        <!-- short-desctiption section start -->
        <section class="short-des">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-sm-8">
                        <div class="short-des-title">
                            <div class="content-section-header">
                                <span>Overview</span>
                            </div>
                            {!! $campaign->short_description !!}
                        </div>
                        <div class="ord_btn">
                            <a href="#order_form" class="order_place">Order the Organic Product <i class="fa-solid fa-arrow-down"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
         <!-- short-desctiption section end -->

        <!-- desctiption section start -->
        <section class="description-section">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="description-inner">
                            <div class="content-section-header">
                                <span>Details</span>
                            </div>
                            <div class="description-title">
                                <h2>{{$campaign->description_title}}</h2>
                            </div>
                            <div class="main-description">
                                {!! $campaign->description !!}
                            </div>
                        </div>
                        <div class="ord_btn mt-5">
                            <a href="#order_form" class="order_place">Order Now <i class="fa-solid fa-arrow-down"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
         <!-- desctiption section end -->

        <!-- desctiption section start -->
        <section class="whychoose-section">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="whychoose-inner">
                            <div class="content-section-header">
                                <span>Why Choose Us</span>
                            </div>
                            <div class="whychoose-title">
                                <h2>Why Buy Our Product?</h2>
                            </div>
                            <div class="main-whychoose">
                                {!! $campaign->why_chooseus !!}
                            </div>
                        </div>
                        <div class="ord_btn my-5">
                            <a href="#order_form" class="order_place">Order Now <i class="fa-solid fa-arrow-down"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
         <!-- desctiption section end -->

         <!-- review section start -->
         @if($campaign->images)
         <section class="review-section">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="rev_inn">
                            <div class="rev_title">
                                <h2>Customer Reviews</h2>
                            </div>
                            <div class="review_slider owl-carousel">
                            @foreach($campaign->images as $key=>$value)
                            <div class="review_item">
                                <img src="{{asset($value->image)}}" alt="">
                            </div>
                            @endforeach
                           </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif
        <!-- review section end -->

        <!-- offer price form end -->
        <section class="price-section">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="offer_price">
                            <div class="offer_title">
                                <h2>Special campaign price e ekhoni order korun.</h2>
                            </div>
                            <div class="product-price">
                                <div class="campaign-price-box">
                                    <div class="old_price campaign-old-price" @if(!$old_price) style="display:none;" @endif>
                                        Previous Price: <del><span class="campaign_old_price">{{$old_price}}</span></del> /=
                                    </div>
                                    <div class="campaign-current-price">
                                        Current Price <span class="campaign_new_price">{{$new_price}}</span>/=
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="form_sec">
        <div class="container">
           <div class="row">
             <div class="col-sm-12">
                <div class="form_inn">
                    <div class="col-sm-12">
                        <div class="row order_by">
                            <div class="col-sm-5">
                                <div class="checkout-shipping" id="order_form">
                                    <form action="{{route('customer.ordersave')}}" method="POST" data-parsley-validate="">
                                    @csrf
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="potro_font">Enter your information below to confirm your order</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group mb-3">
                                                        <label for="name">Your Name *</label>
                                                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{old('name')}}" required>
                                                        @error('name')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <!-- col-end -->
                                                <div class="col-sm-12">
                                                    <div class="form-group mb-3">
                                                        <label for="phone">Your Mobile Number *</label>
                                                        <input type="number" minlength="11" id="number" maxlength="11" pattern="0[0-9]+" title="please enter number only and 0 must first character" title="Please enter an 11-digit number." id="phone" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{old('phone')}}" required>
                                                        @error('phone')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <!-- col-end -->
                                                <div class="col-sm-12">
                                                    <div class="form-group mb-3">
                                                        <label for="address">Your Address *</label>
                                                        <input type="address" id="address" class="form-control @error('address') is-invalid @enderror" name="address" value="{{old('address')}}"  required>
                                                        @error('email')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group mb-3">
                                                        <label for="area">Select Your Area *</label>
                                                        <select type="area" id="area" class="form-control @error('area') is-invalid @enderror" name="area"   required>
                                                            @foreach($shippingcharge as $key=>$value)
                                                            <option value="{{$value->id}}">{{$value->name}}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('email')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <!-- col-end -->
                                                @if($productcolors->count() > 0)
                                                     <div class="pro-color" style="width: 100%;">
                                                        <div class="color_inner">
                                                            <p>Color -</p>
                                                            <div class="size-container">
                                                                <div class="selector">
                                                                    @foreach ($productcolors as $key=>$procolor)
                                                                    <div class="selector-item color-item" data-id="{{$key}}">
                                                                        <input
                                                                            type="radio"
                                                                            id="fc-option{{ $procolor->color }}"
                                                                            value="{{ $procolor->color}}"
                                                                            name="product_color"
                                                                            class="selector-item_radio emptyalert stock_color stock_check" required data-color="{{ $procolor->color}}"
                                                                        />
                                                                        <label for="fc-option{{ $procolor->color }}" class="selector-item_label">{{ $procolor->color}} 
                                                                        </label>
                                                                    </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif 
                                                    @if($productsizes->count() > 0)
                                                        <div class="pro-size" style="width: 100%;">
                                                            <div class="size_inner">
                                                                <p>Size - <span class="attibute-name"></span></p>
                                                                <div class="size-container">
                                                                    <div class="selector">
                                                                        @foreach ($productsizes as $prosize)
                                                                            <div class="selector-item">
                                                                                <input type="radio"
                                                                                    id="f-option{{ $prosize->size }}"
                                                                                    value="{{ $prosize->size}}"
                                                                                    name="product_size"
                                                                                    class="selector-item_radio emptyalert stock_size stock_check" data-size="{{ $prosize->size}}" 
                                                                                    required />
                                                                                <label
                                                                                    for="f-option{{ $prosize->size }}"
                                                                                    class="selector-item_label">{{ $prosize->size}}</label>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <button class="order_place confirm_order" type="submit">Confirm Order</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- card end -->
                                </form>
                                </div>
                            </div>
                            <!-- col end -->
                            <div class="col-sm-7 cust-order-1">
                                <div class="cart_details">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="potro_font">Product Details</h5>
                                        </div>
                                        <div class="card-body cartlist  table-responsive">
                                            <table class="cart_table table table-bordered table-striped text-center mb-0">
                                                <thead>
                                                   <tr>
                                                      <th style="width: 20%;">Remove</th>
                                                      <th style="width: 40%;">Product</th>
                                                      <th style="width: 20%;">Quantity</th>
                                                      <th style="width: 20%;">Price</th>
                                                     </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach(Cart::instance('shopping')->content() as $value)
                                                    <tr>
                                                        <td>
                                                            <a class="cart_remove" data-id="{{$value->rowId}}"><i class="fas fa-trash text-danger"></i></a>
                                                        </td>
                                                        <td class="text-left">
                                                             <a style="font-size: 14px;" href="{{route('product',$value->options->slug)}}"><img src="{{asset($value->options->image)}}" height="30" width="30"> {{Str::limit($value->name,20)}}</a>
                                                        </td>
                                                        <td width="15%" class="cart_qty">
                                                            <div class="qty-cart vcart-qty">
                                                                <div class="quantity">
                                                                    <button class="minus cart_decrement"  data-id="{{$value->rowId}}">-</button>
                                                                    <input type="text" value="{{$value->qty}}" readonly />
                                                                    <button class="plus  cart_increment" data-id="{{$value->rowId}}">+</button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>৳{{$value->price*$value->qty}}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                     <tr>
                                                      <th colspan="3" class="text-end px-4">Subtotal</th>
                                                      <td>
                                                       <span id="net_total"><span class="alinur">৳ </span><strong>{{$subtotal}}</strong></span>
                                                      </td>
                                                     </tr>
                                                     <tr>
                                                      <th colspan="3" class="text-end px-4">Delivery Charge</th>
                                                      <td>
                                                       <span id="cart_shipping_cost"><span class="alinur">৳ </span><strong>{{$shipping}}</strong></span>
                                                      </td>
                                                     </tr>
                                                     <tr>
                                                      <th colspan="3" class="text-end px-4">Grand Total</th>
                                                      <td>
                                                       <span id="grand_total"><span class="alinur">৳ </span><strong>{{$subtotal+$shipping}}</strong></span>
                                                      </td>
                                                     </tr>
                                                    </tfoot>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- col end -->
                            </div>
                    </div>
                </div>

             </div>
            </div>
        </div>
    </section>

        <script src="{{ asset('frontEnd/campaign/js') }}/jquery-2.1.4.min.js"></script>
        <script src="{{ asset('frontEnd/campaign/js') }}/all.js"></script>
        <script src="{{ asset('frontEnd/campaign/js') }}/bootstrap.min.js"></script>
        <script src="{{ asset('frontEnd/campaign/js') }}/owl.carousel.min.js"></script>
        <script src="{{ asset('frontEnd/campaign/js') }}/select2.min.js"></script>
        <script src="{{ asset('frontEnd/campaign/js') }}/script.js"></script>
        <script src="{{ asset('backEnd/assets/js/toastr.min.js') }}"></script>
        {!! Toastr::message() !!} 
        
      
        <!-- bootstrap js -->
        <script>
            $(document).ready(function () {
                const campaignMainSliderItems = (typeof window.getSliderItems === 'function')
                    ? window.getSliderItems('campaign_main_slider', { mobile: 1, tablet: 1, desktop: 1 })
                    : { mobile: 1, tablet: 1, desktop: 1 };
                $(".owl-carousel").owlCarousel({
                    margin: 15,
                    loop: true,
                    dots: false,
                    autoplay: true,
                    autoplayTimeout: 6000,
                    autoplayHoverPause: true,
                    responsive: {
                        0: { items: campaignMainSliderItems.mobile },
                        768: { items: campaignMainSliderItems.tablet },
                        1170: { items: campaignMainSliderItems.desktop }
                    }
                    });
                $('.owl-nav').remove();
            });
        </script>
        <script>
            $(document).ready(function() {
                $('.select2').select2();
            });
        </script>
        <script>
             $("#area").on("change", function () {
                var id = $(this).val();
                $.ajax({
                    type: "GET",
                    data: { id: id },
                    url: "{{route('shipping.charge')}}",
                    dataType: "html",
                    success: function(response){
                        $('.cartlist').html(response);
                    }
                });
            });
        </script>
           <script>
            $(".cart_remove").on("click", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('cart.remove_bn')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                            }
                        },
                    });
                }
            });
            $(".cart_increment").on("click", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('cart.increment_bn')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                            }
                        },
                    });
                }
            });

            $(".cart_decrement").on("click", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('cart.decrement_bn')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                            }
                        },
                    });
                }
            });

        </script>
        <script>
            const reviewSliderItems = (typeof window.getSliderItems === 'function')
                ? window.getSliderItems('review_slider', { mobile: 1, tablet: 2, desktop: 5 })
                : { mobile: 1, tablet: 2, desktop: 5 };
            $('.review_slider').owlCarousel({   
                dots: false,
                arrow: false,
                autoplay: true,
                loop: true,
                margin: 10,
                smartSpeed: 1000,
                mouseDrag: true,
                touchDrag: true,
                items: reviewSliderItems.desktop,
                responsiveClass: true,
                responsive: {
                    300: {
                        items: reviewSliderItems.mobile,
                    },
                    480: {
                        items: reviewSliderItems.tablet,
                    },
                    768: {
                        items: reviewSliderItems.desktop,
                    },
                    1170: {
                        items: reviewSliderItems.desktop,
                    },
                }
            });
        </script>
        <script>
            $(".stock_check").on("click", function () {
                var color = $(".stock_color:checked").data('color');
                var size = $(".stock_size:checked").data('size');
                var id = {{$campaign->product_id}};
                if(id){
                    $.ajax({
                        type: "GET",
                        data: { id:id,color: color ,size:size},
                        url: "{{route('campaign.stock_check')}}",
                        dataType: "json",
                        success: function(status){
                            if(status.status){
                                var oldPrice = status.product && status.product.old_price ? status.product.old_price : '';
                                var newPrice = status.product && status.product.new_price ? status.product.new_price : '';

                                $('.campaign_old_price').text(oldPrice);
                                $('.campaign_new_price').text(newPrice);
                                $('.campaign-old-price').toggle(!!oldPrice);
                                $('.confirm_order').prop('disabled', false);
                                return cart_content();
                            }else{
                                $('.confirm_order').prop('disabled', true);
                                toastr.error('Stock Out',"Please select another color or size");
                            }
                            console.log(status);
                            // return cart_content();
                        }
                    });   
                }  
            });
            function cart_content() {
                $.ajax({
                    type: "GET",
                    url: "{{route('cart.content')}}",
                    success: function (data) {
                        if (data) {
                           $(".cartlist").html(data);
                        } else {
                           $(".cartlist").html(data);
                        }
                    },
                });
            }
        </script>
    </body>
</html>
