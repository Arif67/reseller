@extends('frontEnd.layouts.master')

@section('title', 'Top Level Online Ecommerce Market in Bangladesh')

@push('seo')
<meta name="app-url" content="{{ route('home') }}" />
<meta name="robots" content="index, follow" />
<meta name="description" content="{{$generalsetting?->meta_description}}" />
<meta name="keywords" content="{{$generalsetting?->meta_tag}}" />

<!-- Open Graph data -->
<meta property="og:title" content="{{$generalsetting?->meta_title}}" />
<meta property="og:type" content="website" />
<meta property="og:url" content="{{ route('home') }}" />
<meta property="og:image" content="{{ asset($generalsetting?->white_logo) }}" />
<meta property="og:description" content="{{$generalsetting?->meta_description}}" />
@endpush

@push('css')
@include('frontEnd.partials.product-card-theme')
<link rel="stylesheet" href="{{ asset('frontEnd/css/home.css?v=1.0.0') }}" />
@endpush

@php
    $defaultSectionOrder = ['home_slider', 'promo_strip', 'marketing_banners', 'recently_viewed_products', 'crazy_deal', 'category_slider', 'category_products', 'best_selling_products', 'featured_products', 'new_popular', 'service_features', 'all_products'];
    $configuredSectionOrder = json_decode($themeCustomization?->home_section_order ?? '', true);
    $homeSectionOrder = is_array($configuredSectionOrder) ? $configuredSectionOrder : $defaultSectionOrder;

    $insertAfterMap = [
        'recently_viewed_products' => 'marketing_banners',
        'promo_strip'              => 'home_slider',
        'all_products'             => 'service_features',
    ];

    foreach ($defaultSectionOrder as $sectionKey) {
        if (! in_array($sectionKey, $homeSectionOrder, true)) {
            $insertAfter = $insertAfterMap[$sectionKey] ?? null;

            if ($insertAfter && ($afterIndex = array_search($insertAfter, $homeSectionOrder, true)) !== false) {
                array_splice($homeSectionOrder, $afterIndex + 1, 0, [$sectionKey]);
                continue;
            }

            $homeSectionOrder[] = $sectionKey;
        }
    }

    $sectionVisibility = [
        'home_slider'   => (int) ($themeCustomization?->show_home_slider ?? 1) === 1,
        'promo_strip'    => true,
        'all_products'   => (int) ($themeCustomization?->show_all_products ?? 1) === 1,
        'marketing_banners' => (int) ($themeCustomization?->show_marketing_banners ?? 1) === 1,
        'crazy_deal' => (int) ($themeCustomization?->show_crazy_deal ?? 1) === 1,
        'category_slider' => (int) ($themeCustomization?->show_category_slider ?? 1) === 1,
        'category_products' => (int) ($themeCustomization?->show_category_products ?? 1) === 1,
        'best_selling_products' => (int) ($themeCustomization?->show_best_selling_products ?? 1) === 1,
        'recently_viewed_products' => ($recentlyViewedProducts ?? collect())->isNotEmpty(),
        'featured_products' => (int) ($themeCustomization?->show_featured_products ?? 1) === 1,
        'new_popular' => (int) ($themeCustomization?->show_new_popular ?? 1) === 1,
        'service_features' => (int) ($themeCustomization?->show_service_features ?? 1) === 1,
    ];

    $sectionViews = [
        'home_slider'  => 'frontEnd.components.home.hero-slider',
        'promo_strip'  => 'frontEnd.components.home.promo-strip',
        'all_products' => 'frontEnd.components.home.all-products',
        'marketing_banners' => 'frontEnd.components.home.marketing-banners',
        'crazy_deal' => 'frontEnd.components.home.crazy-deal',
        'category_slider' => 'frontEnd.components.home.featured-categories',
        'category_products' => 'frontEnd.components.home.partials.featured-category-products',
        'best_selling_products' => 'frontEnd.components.home.best-selling-products',
        'recently_viewed_products' => 'frontEnd.components.home.recently-viewed-products',
        'featured_products' => 'frontEnd.components.home.featured-products',
        'new_popular' => 'frontEnd.components.home.new-popular',
        'service_features' => 'frontEnd.components.home.service-features',
    ];
@endphp

@section('content')
@foreach ($homeSectionOrder as $sectionKey)
    @if (($sectionVisibility[$sectionKey] ?? false) && isset($sectionViews[$sectionKey]))
        @include($sectionViews[$sectionKey])
    @endif
@endforeach
@endsection

@push('script')
<script src="{{ asset('frontEnd/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('frontEnd/js/jquery.syotimer.min.js') }}"></script>
<script src="{{ asset('frontEnd/js/home.js?v=1.0.0') }}"></script>
@endpush
