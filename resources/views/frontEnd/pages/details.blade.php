@extends('frontEnd.layouts.master')
@section('title', $details?->name)
@php
    $detailsLayout = (int) ($themeCustomization?->product_details_layout ?? 1);
    $productSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $details?->name,
        'image' => [asset($details?->primary_media_image ?? $details?->image?->image ?? 'uploads/logo.png')],
        'description' => $details?->meta_description ?: $details?->name,
        'sku' => (string) $details?->id,
        'brand' => [
            '@type' => 'Brand',
            'name' => $details?->brand?->name ?: $generalsetting?->name,
        ],
        'offers' => [
            '@type' => 'Offer',
            'url' => route('product', $details?->slug),
            'priceCurrency' => 'BDT',
            'price' => $details?->display_new_price ?? 0,
            'availability' => ((float) ($details?->stock ?? 0) > 0)
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
        ],
    ];
@endphp
@push('seo')
    <meta name="app-url" content="{{ route('product', $details?->slug) }}" />
    <meta name="robots" content="index, follow" />
    <meta name="description" content="{{ $details?->meta_description }}" />
    <meta name="keywords" content="{{ $details?->slug }}" />

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product" />
    <meta name="twitter:site" content="{{ $details?->name }}" />
    <meta name="twitter:title" content="{{ $details?->name }}" />
    <meta name="twitter:description" content="{{ $details?->meta_description }}" />
    <meta name="twitter:creator" content="{{ route('home') }}" />
    <meta property="og:url" content="{{ route('product', $details?->slug) }}" />
    <meta name="twitter:image" content="{{ asset($details?->primary_media_image ?? $details?->image?->image ?? 'uploads/logo.png') }}" />

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $details?->name }}" />
    <meta property="og:type" content="product" />
    <meta property="og:url" content="{{ route('product', $details?->slug) }}" />
    <meta property="og:image" content="{{ asset($details?->primary_media_image ?? $details?->image?->image ?? 'uploads/logo.png') }}" />
    <meta property="og:description" content="{{ $details?->meta_description }}" />
    <meta property="og:site_name" content="{{ $details?->name }}" />
    <script type="application/ld+json">
        {!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

@push('css')
@include('frontEnd.partials.product-card-theme')
    <style>
        .chheckout-section {
            padding-top: 12px;
        }

        .mobile-search {
            display: none;
        }

        .footer_nav1 {
            display: none !important;
        }

        .product-hero-shell {
            padding: 0;
            background: transparent;
            border: 0;
            box-shadow: none;
        }

        .product-gallery-panel,
        .product-info-panel,
        .details-content-card,
        .reviews-card-shell {
            background: #fff;
            border: 1px solid #eceff3;
            border-radius: 0;
            box-shadow: none;
            padding: 0;
        }

        .product-gallery-panel {
            border: 0;
        }

        .product-info-panel {
            padding: 0 0 0 18px;
            border: 0;
            background: #fff;
        }

        .zoom-container {
            overflow: hidden;
            position: relative;
            border-radius: 0;
            background: #f6f6f6;
            aspect-ratio: 4 / 4.6;
            line-height: 0;
        }

        .zoom-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            transition: transform 0.35s ease;
        }

        .zoom-container:hover .zoom-image {
            transform: scale(1.08);
        }


        .details_slider,
        .details_slider .owl-stage-outer,
        .details_slider .owl-stage,
        .details_slider .owl-item,
        .details_slider .dimage_item {
            height: 100%;
        }

        .details_slider .dimage_item {
            line-height: 0;
        }

        .details_slider .dimage_item img,
        .details_slider .zoom-image,
        .details_slider .block__pic {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            object-position: center center !important;
            display: block;
            margin: 0 !important;
            padding: 0 !important;
        }

        .indicator-item,
        .indicator-item img {
            line-height: 0;
        }

        .indicator_thumb {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
            gap: 8px;
            margin-top: 8px;
        }

        .indicator-item {
            border-radius: 0;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            background: #fff;
            cursor: pointer;
        }

        .indicator-item img {
            width: 100%;
            height: 74px;
            object-fit: cover;
            display: block;
        }

        .product-info-panel .product-cart > * + * {
            margin-top: 14px;
        }

        .product-info-shell {
            padding: 10px 0 0 28px;
        }

        .product-info-header {
            padding-bottom: 16px;
            border-bottom: 1px solid #eceff3;
            margin-bottom: 18px;
        }

        .product-gallery-column {
            display: flex;
            flex-direction: column;
            gap: 18px;
            min-width: 0;
        }

        .product-info-panel {
            position: sticky;
            top: 18px;
            max-height: calc(100vh - 36px);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .product-info-panel::-webkit-scrollbar {
            width: 6px;
        }

        .product-info-panel::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        .product-info-panel::-webkit-scrollbar-track {
            background: transparent;
        }

        .product-gallery-column .details-content-shell {
            margin-top: 0;
        }

        .product-info-panel form {
            margin-top: 0;
        }

        .product-meta-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .product-meta-chip {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
        }

        .product-share-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .product-share-links a {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid #e2e8f0;
            color: #475569 !important;
            background: #fff;
        }


        .product-share-links a:hover {
            background: #111827;
            border-color: #111827;
            color: #fff !important;
        }

        .product-subinfo {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 8px;
        }

        .product-subinfo-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            font-size: 12px;
            font-weight: 700;
            color: #475569;
        }

        .product-subinfo-item i {
            color: #111827;
        }

        .product-title {
            font-size: 33px;
            line-height: 1.18;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .product-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 16px;
        }

        .product-summary-card {
            padding: 10px 12px;
            border-radius: 0;
            border: 1px solid #eceff3;
            background: #fff;
        }

        .product-summary-card span {
            display: block;
            margin-bottom: 4px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .product-summary-card strong {
            font-size: 13px;
            color: #111827;
            line-height: 1.4;
        }


        .product-form-card {
            border: 1px solid #eceff3;
            border-radius: 0;
            padding: 18px;
            background: #fff;
        }

        .product-form-card + .product-form-card {
            margin-top: 14px;
        }

        .product-price-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 16px 18px;
            border: 1px solid #eceff3;
            border-left: 4px solid #111827;
            background: #fcfcfd;
            margin-bottom: 16px;
        }

        .product-price-caption {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .product-price-note {
            font-size: 12px;
            color: #64748b;
            margin: 0;
        }

        .stock-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            background: #ecfdf3;
            border: 1px solid #bbf7d0;
            color: #166534;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .product-option-stack {
            display: grid;
            gap: 14px;
        }

        .product-attribute-group {
            padding-bottom: 14px;
            border-bottom: 1px solid #f1f5f9;
        }

        .product-attribute-group:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .product-price-box {
            display: inline-flex;
            align-items: baseline;
            gap: 10px;
            padding: 0;
            border-radius: 0;
            background: transparent;
            border: 0;
            margin: 0;
        }

        .product-price-box .details-price {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: #111827;
        }

        .product-price-box .details-price del {
            color: #94a3b8;
            font-size: 17px;
            font-weight: 600;
        }

        .product-attribute-group p,
        .product-form-label {
            margin: 0 0 10px;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }

        .selector {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .selector-item_radio {
            display: none;
        }

        .selector-item_label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 52px;
            min-height: 38px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid #dbe4ee;
            background: #fff;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .selector-item_radio:checked + .selector-item_label {
            background: #111827;
            border-color: #111827;
            color: #fff;
        }

        .selector-item_radio:disabled + .selector-item_label {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .qty-cart {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 18px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .quantity {
            display: inline-flex;
            align-items: center;
            border: 1px solid #dbe4ee;
            border-radius: 999px;
            overflow: hidden;
            background: #fff;
        }

        .quantity .minus,
        .quantity .plus {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            cursor: pointer;
            user-select: none;
        }

        .quantity input {
            width: 56px;
            height: 42px;
            border: 0;
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        .action-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .action-row button {
            min-height: 52px;
            font-size: 14px;
            font-weight: 800;
            border: 0;
            border-radius: 0;
            color: #fff;
            transition: transform 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        .add_cart_btn {
            background: #111827;
        }

        .order_now_btn {
            background: #b91c1c;
            color: #fff;
            border: 1px solid #b91c1c;
        }

        .add_cart_btn:hover,
        .order_now_btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
        }

        .order_now_btn:hover {
            background: #991b1b;
            color: #fff;
        }

        .phone-order-btn {
            width: 100%;
            min-height: 46px;
            margin-top: 12px;
            border-radius: 0;
            border: 1px solid #dbe4ee;
            background: #fff;
            color: #111827;
            font-size: 14px;
            font-weight: 700;
        }

        .store-note-card {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px 18px;
            border-radius: 0;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            margin-top: 16px;
        }

        .store-note-card i {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #dcfce7;
            color: #15803d;
        }

        .store-note-card p {
            margin: 0;
        }

        .store-note-card .btn {
            margin-top: 8px;
            border-radius: 999px;
            padding-left: 14px;
            padding-right: 14px;
        }

        .details-content-shell,
        .reviews-shell,
        .related-product-section {
            margin-top: 28px;
        }

        .section-kicker {
            display: inline-flex;
            align-items: center;
            padding: 6px 11px;
            border-radius: 999px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #c2410c;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 10px;
        }

        .accordion-item {
            border: 1px solid #eceff3 !important;
            border-radius: 0 !important;
            overflow: hidden;
            margin-top: 10px;
        }

        .accordion-button {
            background: #fff;
            color: #111827;
            font-weight: 700;
            box-shadow: none !important;
        }

        .accordion-button:not(.collapsed) {
            background: #f8fafc;
            color: #111827;
        }

        .review-card {
            border: 1px solid #eceff3;
            border-radius: 0;
            padding: 16px;
            background: #fff;
        }

        .review_star {
            color: #f59e0b;
        }

        .details-action-btn,
        .details-review-button {
            min-height: 42px;
            border-radius: 12px;
            padding: 0 16px;
            border: 0;
            background: #111827;
            color: #fff;
            font-weight: 700;
        }

        @media (max-width: 991.98px) {
            .chheckout-section {
                margin-top: 90px;
            }

            .product-title {
                font-size: 26px;
            }

            .product-summary-grid {
                grid-template-columns: 1fr;
            }
        }

        .mobile-details-content {
            display: none;
        }

        @media (max-width: 767px) {
            .footer_nav {
                display: none !important;
            }

            .footer_nav1 {
                display: block !important;
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                background: #fff;
                box-shadow: 0 -2px 12px rgba(15, 23, 42, 0.12);
                z-index: 1000;
                padding: 8px;
            }

            .product-gallery-panel,
            .details-content-card,
            .reviews-card-shell {
                padding: 0;
            }

            .product-info-panel {
                position: static;
                max-height: none;
                overflow: visible;
                padding: 14px 0 0;
                border-top: 1px solid #eceff3;
            }

            .product-info-shell {
                padding: 0;
            }

            .product-title {
                font-size: 22px;
            }

            .action-row {
                grid-template-columns: 1fr;
            }

            .qty-cart {
                flex-direction: column;
                align-items: flex-start;
            }

            .desktop-details-content {
                display: none;
            }

            .mobile-details-content {
                display: block;
                margin-top: 18px;
            }
        }
    </style>


    <link rel="stylesheet" href="{{ asset('frontEnd/css/zoomsl.css') }}">
@endpush

@section('content')
<?php if ($detailsLayout === 2): ?>
    @include('frontEnd.pages.partials.details-layout-2')
<?php elseif ($detailsLayout === 3): ?>
    @include('frontEnd.pages.partials.details-layout-3')
<?php else: ?>
    <div class="container chheckout-section p-3">
        <div class="product-hero-shell">
            <div class="row g-4 align-items-start">
                <div class="col-lg-6 position-relative product-gallery-column">
                    <div class="product-gallery-panel">
              

                <!-- Variable product image -->
                @php
                    $variableImages = collect();

                    foreach ($details?->variables ?? [] as $variable) {
                        if ($variable->media->count() > 0) {
                            foreach ($variable->media as $mediaItem) {
                                $variableImages->push($mediaItem->path);
                            }
                            continue;
                        }

                        foreach ($variable->gallery_images as $galleryImage) {
                            $variableImages->push($galleryImage);
                        }
                    }
                @endphp

                @if ($variableImages->count() > 0)
                    @php
                        $galleryImages = $variableImages;
                        $galleryThumbClass = $galleryImages->count() > 5 ? 'thumb_slider owl-carousel' : '';
                    @endphp
                @else
                    @php
                        $galleryImages = collect($details?->media->count() > 0 ? $details->media : $details?->images);
                        $galleryThumbClass = $galleryImages->count() > 5 ? 'thumb_slider owl-carousel' : '';
                    @endphp
                @endif

                <div class="details_slider owl-carousel">
                    @foreach ($galleryImages as $value)
                        @php
                            $imageValue = is_string($value) ? $value : (data_get($value, 'path', data_get($value, 'image')));
                            $imageUrl = \Illuminate\Support\Str::startsWith($imageValue, ['http://', 'https://']) ? $imageValue : asset($imageValue);
                        @endphp
                        @if ($imageValue)
                            <div class="dimage_item zoom-container">
                                <img src="{{ $imageUrl }}" class="zoom-image block__pic" alt="Product Image">
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="indicator_thumb {{ $galleryThumbClass }}">
                    @foreach ($galleryImages as $key => $value)
                        @php
                            $imageValue = is_string($value) ? $value : (data_get($value, 'path', data_get($value, 'image')));
                            $imageUrl = \Illuminate\Support\Str::startsWith($imageValue, ['http://', 'https://']) ? $imageValue : asset($imageValue);
                        @endphp
                        @if ($imageValue)
                            <div class="indicator-item" data-id="{{ $key }}">
                                <img src="{{ $imageUrl }}" />
                            </div>
                        @endif
                    @endforeach
                </div>



                    </div>

    <div class="details-content-shell desktop-details-content">
        <section class="pro_details_area py-0 details-content-card">
            <div class="section-kicker">Product details</div>
            <div class="accordion" id="productAccordion">

                            <!-- Description -->
                            <div class="accordion-item mt-1">
                                <h2 class="accordion-header" id="headingDesc">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseDesc">
                                        Description
                                    </button>
                                </h2>
                                <div id="collapseDesc" class="accordion-collapse collapse show"
                                    data-bs-parent="#productAccordion">
                                    <div class="accordion-body">
                                        {!! $details?->description !!}
                                    </div>
                                </div>
                            </div>

                            <!-- Reviews -->
                            <div class="accordion-item mt-1">
                                <h2 class="accordion-header" id="headingReview">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseReview">
                                        Care Tips
                                    </button>
                                </h2>
                                <div id="collapseReview" class="accordion-collapse collapse"
                                    data-bs-parent="#productAccordion">
                                    <div class="accordion-body">
                                        {!! $details?->care_tips !!}
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery & Return -->
                            <div class="accordion-item mt-1">
                                <h2 class="accordion-header" id="headingDelivery">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseDelivery">
                                        Delivery & Return Policy
                                    </button>
                                </h2>
                                <div id="collapseDelivery" class="accordion-collapse collapse"
                                    data-bs-parent="#productAccordion">
                                    <div class="accordion-body">
                                        {!! $page?->description !!}
                                    </div>
                                </div>
                            </div>

            </div>
        </section>
    </div>

                </div>
                <div class="col-lg-6">
                    <div class="details_right">
                        <div class="product-info-panel">
                            <div class="product-info-shell">
                    {{-- <div class="breadcrumb">
                    <ul>
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li><span>/</span></li>
                        <li><a href="{{ url('/category/' . $details?->category->slug) }}">{{
                                $details?->category->name }}</a>
                        </li>
                        @if ($details?->subcategory)
                        <li><span>/</span></li>
                        <li><a href="#">{{ $details?->subcategory ?
                                $details?->subcategory->subcategoryName : '' }}</a>
                        </li>
                        @endif @if ($details?->childcategory)
                        <li><span>/</span></li>
                        <li><a href="#">{{ $details?->childcategory->childcategoryName }}</a>
                        </li>
                        @endif
                    </ul>
                </div> --}}

                    <div class="product">
                        <div class="product-cart">
                            <div class="product-info-header">
                                <div class="product-meta-top">
                                    <span class="product-meta-chip">SKU: {{ $details?->product_code ?: 'N/A' }}</span>
                                    <div class="product-share-links">
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                        <a href="http://pinterest.com/pin/create/button/?url={{ url()->current() }}&media={{ url('product_image_url') }}&description={{ $details?->name }}" target="_blank"><i class="fab fa-pinterest-p"></i></a>
                                        <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}" target="_blank"><i class="fab fa-twitter"></i></a>
                                        <a href="mailto:?subject=Check out this product!&body=I thought you might like this: {{ url()->current() }}" target="_blank"><i class="fas fa-envelope"></i></a>
                                    </div>
                                </div>

                                <h1 class="product-title">{{ $details?->name }}</h1>

                                <div class="product-subinfo">
                                    <div class="product-subinfo-item"><i class="fa-solid fa-shield-halved"></i> Authentic product</div>
                                    <div class="product-subinfo-item"><i class="fa-solid fa-truck-fast"></i> Fast delivery available</div>
                                    <div class="product-subinfo-item"><i class="fa-solid fa-money-bill-wave"></i> Cash on delivery</div>
                                </div>
                            </div>

                            <div class="product-summary-grid">
                                <div class="product-summary-card">
                                    <span>Category</span>
                                    <strong>{{ $details?->category?->name ?? 'N/A' }}</strong>
                                </div>
                                <div class="product-summary-card">
                                    <span>Status</span>
                                    <strong>{{ $details?->variable_count > 0 && $details?->type == 0 ? 'Select your options' : 'In stock' }}</strong>
                                </div>
                                <div class="product-summary-card">
                                    <span>Support</span>
                                    <strong>Pickup and delivery support available</strong>
                                </div>
                            </div>

                            <div class="product-price-card">
                                <div>
                                    <div class="product-price-caption">Selling Price</div>
                                    <div class="product-price-box">
                                    @if ($details?->variable_count > 0 && $details?->type == 0)
                                        <p class="details-price">
                                            <del data-old-price-wrapper @if (!$details?->display_old_price) style="display:none;" @endif>৳ <span class="old_price">{{ $details?->display_old_price }}</span></del>
                                            ৳ <span class="new_price">{{ $details?->display_new_price }}</span>
                                        </p>
                                    @else
                                        <p class="details-price">
                                            <del data-old-price-wrapper @if (!$details?->old_price) style="display:none;" @endif>৳ <span class="old_price">{{ $details?->old_price }}</span></del>
                                            ৳ <span class="new_price">{{ $details?->new_price }}</span>
                                        </p>
                                    @endif
                                    </div>
                                    <p class="product-price-note">Price updates automatically when you select available options.</p>
                                </div>
                                <span class="stock-pill">{{ $details?->variable_count > 0 && $details?->type == 0 ? 'Choose options' : 'Ready to order' }}</span>
                            </div>

                            <form action="{{ route('cart.store') }}" method="POST" name="formName">
                                <div class="product-form-card">
                                @csrf
                                <input type="hidden" name="id" value="{{ $details?->id }}" />
                                <div class="product-option-stack">
                                @foreach ($productAttributeGroups as $attributeGroup)
                                    <div class="w-100 product-attribute-group" data-attribute-group="{{ $attributeGroup['attribute_id'] }}">
                                        <div>
                                            <p>{{ $attributeGroup['title'] }} -</p>
                                            <div class="size-container">
                                                <div class="selector">
                                                    @foreach ($attributeGroup['options'] as $option)
                                                        <div class="selector-item">
                                                            <input
                                                                type="radio"
                                                                id="attribute-{{ $attributeGroup['attribute_id'] }}-{{ $loop->index }}"
                                                                value="{{ $option['id'] }}"
                                                                name="attribute_values[{{ $attributeGroup['attribute_id'] }}]"
                                                                class="selector-item_radio emptyalert stock_check attribute-option"
                                                                data-attribute-title="{{ $attributeGroup['title'] }}"
                                                                data-option-title="{{ $option['title'] }}"
                                                                @if (!$option['id']) disabled @endif
                                                            />
                                                            <label for="attribute-{{ $attributeGroup['attribute_id'] }}-{{ $loop->index }}" class="selector-item_label">
                                                                {{ $option['title'] }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @if ($details?->pro_unit)
                                    <div class="pro_unig">
                                        <label>Unit: {{ $details?->pro_unit }}</label>
                                        <input type="hidden" name="pro_unit" value="{{ $details?->pro_unit }}" />
                                    </div>
                                @endif
                                {{-- <div class="pro_brand mt-2">
                                <p>Brand :
                                    {{ $details?->brand ? $details?->brand->name : 'N/A' }}
                                </p>
                            </div> --}}


                                </div>
                                </div>

                                <div class="product-form-card">
                                    <div class="qty-cart">
                                        <span class="product-form-label mb-0">Quantity</span>
                                        <div class="quantity">


                                            <span class="minus">-</span>
                                            <input type="text" name="qty" value="1" />
                                            <span class="plus">+</span>
                                        </div>
                                    </div>



                                    <div class="single_product col-12">
                                        <div class="action-row">
                                            <button type="submit" name="add_cart" onclick="return sendSuccess();" class="add_cart_btn">
                                                Add to cart
                                            </button>

                                            <button type="submit" name="order_now" value="order_now" onclick="return sendSuccess();" class="order_now_btn">
                                                Buy now
                                            </button>
                                        </div>
                                        <button type="button" class="phone-order-btn">Order by phone (01832-883232)</button>
                                    </div>

                                    <div class="footer_nav1">
                                        <div class="col-12">
                                            <div class="action-row">
                                                <button type="submit" name="add_cart" onclick="return sendSuccess();" class="add_cart_btn">
                                                    Add to cart
                                                </button>

                                                <button type="submit" name="order_now" value="order_now" onclick="return sendSuccess();" class="order_now_btn">
                                                    Buy now
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="store-note-card">
                                        <i class="fa-solid fa-store"></i>
                                        <div>
                                            <p class="fw-semibold mb-1 text-success">{{ $generalsetting->pickup_title }}</p>
                                            <p class="text-muted small mb-2">{{ $generalsetting->pickup_description }}</p>
                                            <a href="{{ route('storepage') }}" class="btn btn-outline-success btn-sm">Check availability at other stores</a>
                                        </div>
                                    </div>


                                    <div class="details-content-shell mobile-details-content">
                                        <section class="pro_details_area py-0 details-content-card">
                                            <div class="section-kicker">Product details</div>
                                            <div class="accordion" id="productAccordionMobile">

                                                <div class="accordion-item mt-1">
                                                    <h2 class="accordion-header" id="headingDescMobile">
                                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#collapseDescMobile">
                                                            Description
                                                        </button>
                                                    </h2>
                                                    <div id="collapseDescMobile" class="accordion-collapse collapse show"
                                                        data-bs-parent="#productAccordionMobile">
                                                        <div class="accordion-body">
                                                            {!! $details?->description !!}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="accordion-item mt-1">
                                                    <h2 class="accordion-header" id="headingReviewMobile">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#collapseReviewMobile">
                                                            Care Tips
                                                        </button>
                                                    </h2>
                                                    <div id="collapseReviewMobile" class="accordion-collapse collapse"
                                                        data-bs-parent="#productAccordionMobile">
                                                        <div class="accordion-body">
                                                            {!! $details?->care_tips !!}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="accordion-item mt-1">
                                                    <h2 class="accordion-header" id="headingDeliveryMobile">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#collapseDeliveryMobile">
                                                            Delivery & Return Policy
                                                        </button>
                                                    </h2>
                                                    <div id="collapseDeliveryMobile" class="accordion-collapse collapse"
                                                        data-bs-parent="#productAccordionMobile">
                                                        <div class="accordion-body">
                                                            {!! $page?->description !!}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </section>
                                    </div>


                                    {{-- <div class="col-sm-12">
                                    <div class="mt-md-2 mt-2">
                                        <h4 class="font-weight-bold">
                                            <a class="btn btn-success w-100 " href="tel: {{ $contact?->hotline }}">
                                                <i class="fa fa-phone-square"></i> Call Now
                                            </a>
                                            <a style="background:#117DB8;"
                                                href="https://wa.me/{{ $contact?->hotline }}?text=Hi%20there!"
                                                target="_blank"
                                                class="btn mt-2 w-100 text-white d-flex align-items-center justify-content-center">
                                                <i class="bi bi-whatsapp fs-4 me-2" aria-hidden="true"></i>
                                                Chat on WhatsApp
                                            </a>
                                        </h4>
                                    </div>

                                    <div class="container my-4">
                                        <div class="row g-3 text-center">
                                            <div class="col-6 col-md-3">
                                                <div class="border rounded shadow-sm p-3 h-100 text-center">
                                                    <i class="fa-solid fa-circle-check fa-2x text-success mb-2"></i>
                                                    <p class="mb-0 fw-semibold">100% Authentic
                                                        Product</p>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="border rounded shadow-sm p-3 h-100 text-center">
                                                    <i class="fa-solid fa-truck-fast fa-2x text-primary mb-2"></i>
                                                    <p class="mb-0 fw-semibold">Fast Delivery</p>
                                                </div>
                                            </div>

                                            <div class="col-6 col-md-3">
                                                <div class="border rounded shadow-sm p-3 h-100 text-center">
                                                    <i class="fa-solid fa-money-bill-wave fa-2x text-warning mb-2"></i>
                                                    <p class="mb-0 fw-semibold">Cash on Delivery</p>
                                                </div>
                                            </div>

                                            <div class="col-6 col-md-3">
                                                <div class="border rounded shadow-sm p-3 h-100 text-center">
                                                    <i class="fa-solid fa-box-open fa-2x text-danger mb-2"></i>
                                                    <p class="mb-0 fw-semibold">Secure Packaging</p>
                                                </div>
                                            </div>

                                        </div>

                                    </div>


                                </div> --}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container reviews-shell text-center">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="reviews-card-shell">
                    <div class="section-kicker">Customer Feedback</div>

                    <div class="container">
                        <div class="row text-center">
                            <div class="col-sm-12">
                                <div class="section-head">
                                    <div class="title text-center">
                                        <h2 class="text-center">Reviews ({{ $reviews->count() }})</h2>
                                        <p class="text-center">Get specific details about this product from customers who own it.</p>
                                    </div>
                                    <div class="action">
                                        <div class="text-center">
                                            @if (Auth::guard('customer')->check() && $canReview)
                                                <button type="button" class="details-action-btn question-btn btn-overlay"
                                                    data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                    Write a review
                                                </button>
                                            @elseif (Auth::guard('customer')->check())
                                                <div class="alert alert-light border mb-0">Only verified buyers can review this product.</div>
                                            @else
                                                <a class="details-action-btn question-btn btn-overlay d-inline-flex justify-content-center align-items-center"
                                                    href="{{ route('customer.login') }}">
                                                    Login to review
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if ($reviews->count() > 0)
                                    <div class="customer-review">
                                        <div class="row">
                                            @foreach ($reviews as $key => $review)
                                                <div class="col-sm-12 col-12">
                                                    <div class="review-card">
                                                        <p class="reviewer_name"><i data-feather="message-square"></i>
                                                            {{ $review->name }}</p>
                                                        <p class="review_data">{{ $review->created_at->format('d-m-Y') }}
                                                        </p>
                                                        <p class="review_star">{!! str_repeat('<i class="fa-solid fa-star"></i>', $review->ratting) !!}</p>
                                                        <p class="review_content">{{ $review->review }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="empty-content">
                                        <i class="fa fa-clipboard-list"></i>
                                        <p class="empty-text">This product has no reviews yet. Be the first one to write a
                                            review.</p>
                                    </div>
                                @endif
                                <div class="modal fade" id="exampleModal" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Your review</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="insert-review">
                                                    @if (Auth::guard('customer')->check() && $canReview)
                                                        <form action="{{ route('customer.review') }}" id="review-form"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="product_id"
                                                                value="{{ $details->id }}">
                                                            <div class="fz-12 mb-2">
                                                                <div class="rating">
                                                                    <label title="Excelent">
                                                                        ☆
                                                                        <input required type="radio" name="ratting"
                                                                            value="5" />
                                                                    </label>
                                                                    <label title="Best">
                                                                        ☆
                                                                        <input required type="radio" name="ratting"
                                                                            value="4" />
                                                                    </label>
                                                                    <label title="Better">
                                                                        ☆
                                                                        <input required type="radio" name="ratting"
                                                                            value="3" />
                                                                    </label>
                                                                    <label title="Very Good">
                                                                        ☆
                                                                        <input required type="radio" name="ratting"
                                                                            value="2" />
                                                                    </label>
                                                                    <label title="Good">
                                                                        ☆
                                                                        <input required type="radio" name="ratting"
                                                                            value="1" />
                                                                    </label>
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="message-text"
                                                                    class="col-form-label">Message:</label>
                                                                <textarea required class="form-control radius-lg" name="review" id="message-text"></textarea>
                                                                <span id="validation-message" style="color: red;"></span>
                                                            </div>
                                                            <div class="form-group">
                                                                <button class="details-review-button"
                                                                    type="submit">Submit
                                                                    Review</button>
                                                            </div>

                                                        </form>
                                                    @elseif (Auth::guard('customer')->check())
                                                        <div class="alert alert-light border mb-0">Only verified buyers can review this product.</div>
                                                    @else
                                                        <a class="customer-login-redirect"
                                                            href="{{ route('customer.login') }}">Login
                                                            to Post
                                                            Your Review</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            </div>
        </div>
    </div>























    <section class="related-product-section">
        <div class="container">
            <div class="row mt-2">
                <div class="col-12 text-center">
                    <div class="section-kicker">You may also like</div>
                    <h2 class="fw-bold mb-2" style="font-size: 30px; font-weight: 900; color: #0f172a;">You May Also Like</h2>
                    <p class="text-muted mb-0">More styles from the same category.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="product-inner owl-carousel related_slider">
                        @foreach ($products as $key => $value)
                            @include('frontEnd.partials.product-card', ['product' => $value, 'titleLimit' => 56])
                        @endforeach
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (($recentlyViewedProducts ?? collect())->isNotEmpty())
    <section class="related-product-section">
        <div class="container">
            <div class="row mt-2">
                <div class="col-12 text-center">
                    <div class="section-kicker">Continue browsing</div>
                    <h2 class="fw-bold mb-2" style="font-size: 30px; font-weight: 900; color: #0f172a;">Recently Viewed Products</h2>
                    <p class="text-muted mb-0">Products you checked recently.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="product-inner owl-carousel recently_viewed_details_slider">
                        @foreach ($recentlyViewedProducts as $value)
                            @include('frontEnd.partials.product-card', ['product' => $value, 'titleLimit' => 56])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

<?php endif; ?>

@endsection

@push('script')
    <script src="{{ asset('frontEnd/js/owl.carousel.min.js') }}"></script>

    <script src="{{ asset('frontEnd/js/zoomsl.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            const detailsSliderItems = (typeof window.getSliderItems === 'function')
                ? window.getSliderItems('details_slider', { mobile: 1, tablet: 1, desktop: 1 })
                : { mobile: 1, tablet: 1, desktop: 1 };
            $(".details_slider").owlCarousel({
                margin: 15,
                items: detailsSliderItems.desktop,
                loop: true,
                dots: false,
                nav: false,
                autoplay: false,
            });
            $(".indicator-item,.color-item").on("click", function() {
                var slideIndex = $(this).data('id');
                $('.details_slider').trigger('to.owl.carousel', slideIndex);
            });
        });
        $(document).ready(function() {
            $('#description').show();
            $('.desc-nav-ul li a, .all-reviews-button').click(function(e) {
                e.preventDefault();
                var target = $(this).data('target');
                $('.tab-content').hide();
                $(target).show();
                $('.desc-nav-ul li a').removeClass('active');
                if ($(this).closest('li').length) {
                    $(this).addClass('active');
                }

                $('html, body').animate({
                    scrollTop: $(target).offset().top
                }, 300);
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php
                $productPayload = [
                    'id' => (string) ($details?->id ?? ''),
                    'name' => $details?->name ?? '',
                    'brand' => $details?->brand?->name ?? 'N/A',
                    'category' => $details?->category?->name ?? 'N/A',
                    'price' => (float) ($details?->display_new_price ?? 0),
                    'currency' => 'BDT',
                ];
            ?>
            const product = @json($productPayload);
            const metaConfig = window.frontendConfig?.meta || {};
            const marketingConfig = window.frontendConfig?.marketing || {};
            const marketingRoutes = window.frontendConfig?.marketingRoutes || {};
            const getCookie = (name) => document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]*)'))?.[2] || '';
            const ttclid = new URLSearchParams(window.location.search).get('ttclid') || '';

            // Unique event ID for deduplication (Pixel + CAPI)
            const eventId = `viewcontent_${product.id}_{{ session()->getId() }}_${Date.now()}`;

            console.log('👀 ViewContent triggered:', product, 'EventID:', eventId);

            // --- Facebook Pixel (browser-side) ---
            if (typeof fbq === 'function' && metaConfig.facebookPixelId) {
                fbq('track', 'ViewContent', {
                    content_ids: [product.id],
                    content_name: product.name,
                    content_category: product.category,
                    content_brand: product.brand,
                    currency: product.currency,
                    value: product.price
                }, {
                    eventID: eventId
                });
            }
            // TikTok Pixel tracking
            if (typeof ttq !== 'undefined' && metaConfig.tiktokPixelId) {
                ttq.track('ViewContent', {
                    content_id: product.id,
                    content_name: product.name,
                    content_type: 'product',
                    content_category: product.category,
                    content_brand: product.brand,
                    value: product.price,
                    currency: product.currency
                });
            }
            if (typeof gtag === 'function' && marketingConfig.ga4MeasurementId) {
                gtag('event', 'view_item', {
                    currency: product.currency,
                    value: product.price,
                    items: [{
                        item_id: product.id,
                        item_name: product.name,
                        item_brand: product.brand,
                        item_category: product.category,
                        price: product.price
                    }]
                });
                if (marketingRoutes.googleEventLog) {
                    fetch(marketingRoutes.googleEventLog, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            event_name: 'ViewContent',
                            value: product.price,
                            currency: product.currency,
                            source_url: window.location.href
                        })
                    }).catch(() => null);
                }
            }
            if (metaConfig.tiktokCapiEnabled) {
                fetch('{{ route('tiktok.view_content_capi') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        product_id: product.id,
                        product_name: product.name,
                        brand: product.brand,
                        category: product.category,
                        value: product.price,
                        currency: product.currency,
                        event_source_url: window.location.href,
                        client_user_agent: navigator.userAgent,
                        client_ip_address: "{{ request()->ip() }}",
                        ttp: getCookie('_ttp'),
                        ttclid: ttclid
                    })
                }).catch(err => console.error('TikTok ViewContent fetch error:', err));
            }
            // --- Facebook Conversion API (server-side) ---
            if (metaConfig.facebookCapiEnabled) {
                fetch('{{ route('facebook.view_content_capi') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        event_id: eventId,
                        product_id: product.id,
                        product_name: product.name,
                        brand: product.brand,
                        category: product.category,
                        value: product.price,
                        currency: product.currency,
                        event_source_url: window.location.href,
                        client_user_agent: navigator.userAgent,
                        client_ip_address: "{{ request()->ip() }}",
                        fbp: getCookie('_fbp'),
                        fbc: getCookie('_fbc')
                    })
                })
                .then(async res => {
                    const text = await res.text(); // read raw response
                    try {
                        const data = JSON.parse(text); // try parse JSON
                        console.log('✅ CAPI ViewContent success response:', data);
                    } catch (err) {
                        console.error('❌ CAPI ViewContent error. Raw response:', text);
                    }
                })
                .catch(err => console.error('❌ CAPI ViewContent fetch error:', err));
            }
        });
    </script>






    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php
                $productPayload = [
                    'id' => (string) ($details?->id ?? ''),
                    'name' => $details?->name ?? '',
                    'brand' => $details?->brand?->name ?? 'N/A',
                    'category' => $details?->category?->name ?? 'N/A',
                    'price' => (float) ($details?->display_new_price ?? 0),
                    'currency' => 'BDT',
                ];
            ?>
            const product = @json($productPayload);
            const metaConfig = window.frontendConfig?.meta || {};
            const marketingConfig = window.frontendConfig?.marketing || {};
            const marketingRoutes = window.frontendConfig?.marketingRoutes || {};
            const getCookie = (name) => document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]*)'))?.[2] || '';
            const ttclid = new URLSearchParams(window.location.search).get('ttclid') || '';

            document.querySelectorAll('.add_cart_btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    let qtyInput = document.querySelector('input[name="qty"]');
                    let quantity = qtyInput ? parseInt(qtyInput.value) || 1 : 1;

                    const eventId =
                        `addtocart_${product.id}_{{ session()->getId() }}_${Date.now()}`;

                    console.log('🛒 AddToCart triggered:', {
                        ...product,
                        quantity
                    }, 'EventID:', eventId);

                    // --- Facebook Pixel ---
                    if (typeof fbq === 'function' && metaConfig.facebookPixelId) {
                        fbq('track', 'AddToCart', {
                            content_ids: [product.id],
                            content_name: product.name,
                            content_category: product.category,
                            content_brand: product.brand,
                            currency: product.currency,
                            value: product.price * quantity,
                            contents: [{
                                id: product.id,
                                quantity: quantity,
                                item_price: product.price
                            }]
                        }, {
                            eventID: eventId
                        });
                    }

                    // --- TikTok Pixel ---
                    if (typeof ttq !== 'undefined' && metaConfig.tiktokPixelId) {
                        ttq.track('AddToCart', {
                            content_id: product.id,
                            content_name: product.name,
                            content_category: product.category,
                            quantity: quantity,
                            price: product.price,
                            currency: product.currency
                        });
                        console.log('✅ TikTok AddToCart fired:', {
                            ...product,
                            quantity
                        });
                    }

                    if (typeof gtag === 'function' && marketingConfig.ga4MeasurementId) {
                        gtag('event', 'add_to_cart', {
                            currency: product.currency,
                            value: product.price * quantity,
                            items: [{
                                item_id: product.id,
                                item_name: product.name,
                                item_brand: product.brand,
                                item_category: product.category,
                                price: product.price,
                                quantity: quantity
                            }]
                        });
                        if (marketingRoutes.googleEventLog) {
                            fetch(marketingRoutes.googleEventLog, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    event_name: 'AddToCart',
                                    value: product.price * quantity,
                                    currency: product.currency,
                                    source_url: window.location.href
                                })
                            }).catch(() => null);
                        }
                    }

                    if (metaConfig.tiktokCapiEnabled) {
                        fetch('{{ route('tiktok.add_to_cart_capi') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                product_id: product.id,
                                product_name: product.name,
                                brand: product.brand,
                                category: product.category,
                                value: product.price * quantity,
                                currency: product.currency,
                                quantity: quantity,
                                event_source_url: window.location.href,
                                client_user_agent: navigator.userAgent,
                                client_ip_address: "{{ request()->ip() }}",
                                ttp: getCookie('_ttp'),
                                ttclid: ttclid
                            })
                        }).catch(err => console.error('TikTok AddToCart fetch error:', err));
                    }

                    // --- Facebook CAPI ---
                    if (metaConfig.facebookCapiEnabled) {
                        fetch('{{ route('facebook.add_to_cart_capi') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                event_id: eventId,
                                product_id: product.id,
                                product_name: product.name,
                                brand: product.brand,
                                category: product.category,
                                value: product.price * quantity,
                                currency: product.currency,
                                quantity: quantity,
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
                                console.log('✅ CAPI AddToCart success:', data);
                            } catch (err) {
                                console.error('❌ CAPI AddToCart error. Raw response:',
                                text);
                            }
                        })
                        .catch(err => console.error('❌ CAPI AddToCart fetch error:', err));
                    }
                });
            });
        });
    </script>
































    <!-- Data Layer End-->
    <script>
        $(document).ready(function() {
            const relatedSliderItems = (typeof window.getSliderItems === 'function')
                ? window.getSliderItems('related_slider', { mobile: 2, tablet: 3, desktop: 5 })
                : { mobile: 2, tablet: 3, desktop: 5 };
            $(".related_slider").owlCarousel({
                margin: 10,
                items: relatedSliderItems.desktop,
                loop: true,
                dots: true,
                nav: false,
                autoplay: true,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                responsiveClass: true,
                responsive: {
                    0: {
                        items: relatedSliderItems.mobile,
                        nav: true,
                    },
                    600: {
                        items: relatedSliderItems.tablet,
                    },
                    1000: {
                        items: relatedSliderItems.desktop,
                    },
                },
            });

            $(".recently_viewed_details_slider").owlCarousel({
                margin: 10,
                items: relatedSliderItems.desktop,
                loop: true,
                dots: true,
                nav: false,
                autoplay: true,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                responsiveClass: true,
                responsive: {
                    0: {
                        items: relatedSliderItems.mobile,
                        nav: true,
                    },
                    600: {
                        items: relatedSliderItems.tablet,
                    },
                    1000: {
                        items: relatedSliderItems.desktop,
                    },
                },
            });
            // $('.owl-nav').remove();
        });
    </script>
    <script>
        $(document).ready(function() {
            $(".minus").click(function() {
                var $input = $(this).parent().find("input");
                var count = parseInt($input.val()) - 1;
                count = count < 1 ? 1 : count;
                $input.val(count);
                $input.change();
                return false;
            });
            $(".plus").click(function() {
                var $input = $(this).parent().find("input");
                $input.val(parseInt($input.val()) + 1);
                $input.change();
                return false;
            });
        });
    </script>

    <script>
        function sendSuccess() {
            const groups = document.querySelectorAll('.product-attribute-group');
            for (const group of groups) {
                const checked = group.querySelector('.attribute-option:checked');
                if (!checked) {
                    const title = group.querySelector('.attribute-option')?.dataset.attributeTitle || 'attribute';
                    toastr.warning('Please select ' + title);
                    return false;
                }
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            $(".rating label").click(function() {
                $(".rating label").removeClass("active");
                $(this).addClass("active");
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            const thumbSliderItems = (typeof window.getSliderItems === 'function')
                ? window.getSliderItems('thumb_slider', { mobile: 3, tablet: 4, desktop: 5 })
                : { mobile: 3, tablet: 4, desktop: 5 };
            $(".thumb_slider").owlCarousel({
                margin: 15,
                items: thumbSliderItems.desktop,
                loop: true,
                dots: false,
                nav: true,
                autoplayTimeout: 6000,
                autoplayHoverPause: true,
                responsive: {
                    0: { items: thumbSliderItems.mobile },
                    600: { items: thumbSliderItems.tablet },
                    1000: { items: thumbSliderItems.desktop }
                }
            });
        });
    </script>

    <script type="text/javascript">
        $(".block__pic").imagezoomsl({
            zoomrange: [3, 3]
        });
    </script>
    <script>
        $(document).on('click', '.stock_check', function() {
            const id = {{ $details?->id }};
            const groups = $('.product-attribute-group');
            const selectedValueIds = [];
            let allSelected = true;

            groups.each(function() {
                const checked = $(this).find('.attribute-option:checked');
                if (!checked.length) {
                    allSelected = false;
                    return false;
                }

                const valueId = checked.val();
                if (valueId) {
                    selectedValueIds.push(valueId);
                }
            });

            if (!id || !groups.length || !allSelected) {
                return;
            }

            $.ajax({
                type: 'GET',
                url: "{{ route('stock_check') }}",
                data: {
                    id: id,
                    value_ids: selectedValueIds,
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        const oldPrice = response.product.old_price || '';
                        const newPrice = response.product.new_price || '';

                        $('.stock').html('<p><span>Stock : </span>' + response.product.stock + '</p>');
                        $('.old_price').text(oldPrice);
                        $('.new_price').text(newPrice);

                        $('[data-old-price-wrapper]').each(function() {
                            $(this).toggle(!!oldPrice);
                        });

                        $('.add_cart_btn').prop('disabled', false);
                        $('.order_now_btn').prop('disabled', false);
                    } else {
                        toastr.error('Stock Out', 'Please select another option');
                        $('.stock').empty();
                        $('.add_cart_btn').prop('disabled', true);
                        $('.order_now_btn').prop('disabled', true);
                    }
                },
                error: function() {
                    toastr.error('Server Error', 'Something went wrong');
                }
            });
        });
    </script>
@endpush
