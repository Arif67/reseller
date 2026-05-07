<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Pixel Code -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @php
        $resolvedSearchFontColor = $themeCustomization?->search_font_color;

        if (! $resolvedSearchFontColor) {
            $searchButtonBackground = $themeCustomization?->navbar_background_color ?? '#e6e1e1';
            $normalizedSearchButtonBackground = ltrim($searchButtonBackground, '#');

            if (strlen($normalizedSearchButtonBackground) === 3) {
                $normalizedSearchButtonBackground = collect(str_split($normalizedSearchButtonBackground))
                    ->map(fn ($part) => $part . $part)
                    ->implode('');
            }

            $resolvedSearchFontColor = '#FFFFFF';

            if (preg_match('/^[A-Fa-f0-9]{6}$/', $normalizedSearchButtonBackground)) {
                $red = hexdec(substr($normalizedSearchButtonBackground, 0, 2));
                $green = hexdec(substr($normalizedSearchButtonBackground, 2, 2));
                $blue = hexdec(substr($normalizedSearchButtonBackground, 4, 2));
                $luminance = (0.299 * $red) + (0.587 * $green) + (0.114 * $blue);
                $resolvedSearchFontColor = $luminance > 186 ? '#111111' : '#000000';
            }
        }

        $facebookPixel = collect($pixels ?? [])->first(function ($pixel) {
            return ($pixel->provider ?? 'facebook') === 'facebook';
        });

        $facebookPixelId = $facebookPixel?->code;

        $tiktokPixel = collect($pixels ?? [])->first(function ($pixel) {
            return ($pixel->provider ?? null) === 'tiktok';
        });

        $routeName = request()->route()?->getName();
        $defaultTitle = $generalsetting?->meta_title ?: $generalsetting?->name ?: config('app.name');
        $defaultDescription = $generalsetting?->meta_description ?: 'Buy genuine products online in Bangladesh with fast delivery and trusted service.';
        $defaultKeywords = $generalsetting?->meta_tag ?: 'Bangladesh ecommerce, online shopping, accessories';
        $defaultImagePath = $generalsetting?->white_logo ?: $generalsetting?->favicon ?: 'uploads/logo.png';
        $details = $details ?? null;
        $category = $category ?? null;
        $subcategory = $subcategory ?? null;
        $childcategory = $childcategory ?? null;
        $campaign = $campaign ?? null;

        $seoTitle = trim($__env->yieldContent('title')) ?: $defaultTitle;
        $seoDescription = $details?->meta_description
            ?? $category?->meta_description
            ?? $subcategory?->meta_description
            ?? $childcategory?->meta_description
            ?? $campaign?->short_description
            ?? $defaultDescription;
        $seoKeywords = $details?->slug
            ?? $category?->slug
            ?? $subcategory?->slug
            ?? $childcategory?->slug
            ?? $defaultKeywords;
        $seoImagePath = $details?->image?->image
            ?? $category?->image
            ?? $subcategory?->image
            ?? $childcategory?->image
            ?? $defaultImagePath;
        $seoImage = asset($seoImagePath);
        $seoUrl = request()->fullUrl();
        $canonicalUrl = request()->url();
        $googleSiteVerification = $generalsetting?->google_site_verification ?? env('GOOGLE_SITE_VERIFICATION');
        $organizationSocialLinks = collect($socialicons ?? [])->pluck('link')->filter()->values()->all();
        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $generalsetting?->name,
            'url' => route('home'),
            'logo' => asset($defaultImagePath),
            'sameAs' => $organizationSocialLinks,
        ];
        $websiteSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $generalsetting?->name,
            'url' => route('home'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/search') . '?keyword={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];

        $noIndexRoutes = [
            'search',
            'cart.*',
            'customer.*',
            'payment_success',
            'payment_cancel',
            'quickview',
            'livesearch',
        ];

        $defaultRobots = $generalsetting?->default_robots ?? 'index, follow';
        $robotsDirectives = collect($noIndexRoutes)->contains(fn ($pattern) => request()->routeIs($pattern))
            ? 'noindex, nofollow'
            : $defaultRobots;
        $marketingConfig = $marketingToolConfig ?? null;
        $defaultSliderItemSettings = [
            'hotdeals_slider' => ['mobile' => 3, 'tablet' => 3, 'desktop' => 6],
            'hotdeals_slider1' => ['mobile' => 2, 'tablet' => 3, 'desktop' => 6],
            'hotdeals_slider111' => ['mobile' => 2, 'tablet' => 3, 'desktop' => 4],
            'showcase_product_slider' => ['mobile' => 2, 'tablet' => 3, 'desktop' => 5],
            'product_slider' => ['mobile' => 2, 'tablet' => 3, 'desktop' => 5],
            'product_slider2' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 4],
            'product_sliders3' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 6],
            'product_slider_category' => ['mobile' => 2, 'tablet' => 3, 'desktop' => 4],
            'featured_category_layout2_slider' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 8],
            'details_slider' => ['mobile' => 1, 'tablet' => 1, 'desktop' => 1],
            'related_slider' => ['mobile' => 2, 'tablet' => 3, 'desktop' => 5],
            'thumb_slider' => ['mobile' => 3, 'tablet' => 4, 'desktop' => 5],
            'product_slider12' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 6],
            'campaign_main_slider' => ['mobile' => 1, 'tablet' => 1, 'desktop' => 1],
            'review_slider' => ['mobile' => 1, 'tablet' => 2, 'desktop' => 5],
        ];
        $configuredSliderItemSettings = json_decode($themeCustomization?->slider_item_settings ?? '', true);
        $sliderItemSettings = is_array($configuredSliderItemSettings) ? $configuredSliderItemSettings : [];
        foreach ($defaultSliderItemSettings as $sliderKey => $breakpoints) {
            foreach ($breakpoints as $breakpoint => $count) {
                $sliderItemSettings[$sliderKey][$breakpoint] = (int) ($sliderItemSettings[$sliderKey][$breakpoint] ?? $count);
            }
        }
    @endphp
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($seoDescription), 160, '') }}" />
    <meta name="keywords" content="{{ $seoKeywords }}" />
    <meta name="robots" content="{{ $robotsDirectives }}" />
    <meta name="author" content="{{ $generalsetting?->name }}" />
    <link rel="canonical" href="{{ $canonicalUrl }}" />

    @if ($googleSiteVerification)
    <meta name="google-site-verification" content="{{ $googleSiteVerification }}" />
    @endif

    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($seoDescription), 200, '') }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:site_name" content="{{ $generalsetting?->name }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($seoDescription), 200, '') }}">
    <meta name="twitter:image" content="{{ $seoImage }}">

    <script type="application/ld+json">
        {!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <style>
        :root {
            --theme-top-bar-bg: {{ $themeCustomization?->top_bar_background_color ?? '#FF5722' }};
            --theme-search-bar-bg: {{ $themeCustomization?->search_bar_background_color ?? '#000000' }};
            --theme-navbar-bg: {{ $themeCustomization?->navbar_background_color ?? '#e6e1e1' }};
            --theme-track-order-bg: {{ $themeCustomization?->track_order_background_color ?? '#677279' }};
            --theme-track-order-color: {{ $themeCustomization?->track_order_font_color ?? '#FFFFFF' }};
            --theme-login-bg: {{ $themeCustomization?->login_background_color ?? '#677279' }};
            --theme-login-color: {{ $themeCustomization?->login_font_color ?? '#FFFFFF' }};
            --theme-cart-bg: {{ $themeCustomization?->cart_background_color ?? '#677279' }};
            --theme-cart-color: {{ $themeCustomization?->cart_font_color ?? '#FFFFFF' }};
            --theme-toggle-color: {{ $themeCustomization?->toggle_font_color ?? '#000000' }};
            --theme-search-icon-color: {{ $resolvedSearchFontColor }};
            --product-card-btn-bg: {{ $themeCustomization?->product_card_button_background_color ?? '#DC2626' }};
            --product-card-btn-hover: {{ $themeCustomization?->product_card_button_hover_background_color ?? '#B91C1C' }};
            --product-card-btn-color: {{ $themeCustomization?->product_card_button_font_color ?? '#FFFFFF' }};
            --product-card-badge-bg: {{ $themeCustomization?->product_card_badge_background_color ?? '#DC2626' }};
            --product-card-badge-color: {{ $themeCustomization?->product_card_badge_font_color ?? '#FFFFFF' }};
            --product-card-price-color: {{ $themeCustomization?->product_card_price_color ?? '#111827' }};
            --card-bg: {{ $themeCustomization?->card_bg_color ?? '#ffffff' }};
            --card-border: {{ $themeCustomization?->card_border_color ?? '#e2e8f0' }};
            --card-title: {{ $themeCustomization?->card_title_color ?? '#0f172a' }};
            --card-old-price: {{ $themeCustomization?->card_old_price_color ?? '#94a3b8' }};
            --card-image-bg: {{ $themeCustomization?->card_image_bg_color ?? '#f8fafc' }};
            --theme-container-width: {{ (int) ($themeCustomization?->container_max_width ?? 1520) }}px;
            --card-radius: {{ $themeCustomization?->card_radius ?? 12 }};
            --card-body-padding: {{ $themeCustomization?->card_body_padding ?? 12 }};
            --card-shadow-opacity: {{ $themeCustomization?->card_shadow_opacity ?? 0.12 }};
            --theme-view-details-bg: {{ $themeCustomization?->view_details_bg_color ?? '#f8fafc' }};
            --theme-view-details-color: {{ $themeCustomization?->view_details_font_color ?? '#0f172a' }};
            --theme-primary: {{ $themeCustomization?->primary_color ?? '#0f172a' }};
            --theme-accent: {{ $themeCustomization?->accent_color ?? '#dc2626' }};
            --theme-surface: {{ $themeCustomization?->surface_color ?? '#ffffff' }};
            --theme-section-bg: {{ $themeCustomization?->section_bg_color ?? '#f8fafc' }};
            --theme-text: {{ $themeCustomization?->text_color ?? '#0f172a' }};
            --theme-muted: {{ $themeCustomization?->muted_text_color ?? '#64748b' }};
            --theme-footer-top-bg: {{ $themeCustomization?->footer_top_background_color ?? '#020617' }};
            --theme-footer-text: {{ $themeCustomization?->footer_text_color ?? '#CBD5E1' }};
            --theme-footer-heading: {{ $themeCustomization?->footer_heading_color ?? '#FFFFFF' }};
            --theme-footer-link: {{ $themeCustomization?->footer_link_color ?? '#F8FAFC' }};
            --theme-footer-link-hover: {{ $themeCustomization?->footer_link_hover_color ?? '#F59E0B' }};
            --theme-footer-accent: {{ $themeCustomization?->footer_accent_color ?? '#F59E0B' }};
            --theme-footer-bottom-bg: {{ $themeCustomization?->footer_bottom_background_color ?? '#000000' }};
            --theme-footer-bottom-text: {{ $themeCustomization?->footer_bottom_text_color ?? '#E2E8F0' }};
        }
    </style>

    <link rel="shortcut icon" href="{{ asset($generalsetting?->favicon) }}" alt="{{ $generalsetting?->name }}" />
    @stack('seo')
    @stack('css')
    <link rel="stylesheet" href="{{ asset('frontEnd/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontEnd/css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontEnd/css/woodmart-font.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontEnd/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontEnd/css/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontEnd/css/owl.theme.default.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontEnd/css/mobile-menu.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontEnd/css/select2.min.css') }}" />
    <!-- toastr css -->
    <link rel="stylesheet" href="{{ asset('backEnd/') }}/assets/css/toastr.min.css" />

    <link rel="stylesheet" href="{{ asset('frontEnd/css/wsit-menu.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontEnd/css/style.css?v=1.0.22') }}" />
    <link rel="stylesheet" href="{{ asset('frontEnd/css/responsive.css?v=1.0.22') }}" />
    <link rel="stylesheet" href="{{ asset('frontEnd/css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontEnd/css/master-layout.css') }}?v=1.0.2" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />

    @if ($facebookPixelId)
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init','{{ $facebookPixelId }}');
    </script>
    <noscript>
        <img height="1" width="1" class="meta-pixel-noscript"
            src="https://www.facebook.com/tr?id={{ $facebookPixelId }}&ev=PageView&noscript=1" />
    </noscript>
    @endif

    @if (!empty($marketingConfig?->ga4_measurement_id) || !empty($marketingConfig?->google_ads_id))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $marketingConfig?->ga4_measurement_id ?: $marketingConfig?->google_ads_id }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        @if (!empty($marketingConfig?->ga4_measurement_id))
        gtag('config', '{{ $marketingConfig->ga4_measurement_id }}');
        @endif
        @if (!empty($marketingConfig?->google_ads_id))
        gtag('config', '{{ $marketingConfig->google_ads_id }}');
        @endif
    </script>
    @endif

    @if (!empty($marketingConfig?->clarity_project_id))
    <script>
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "{{ $marketingConfig->clarity_project_id }}");
    </script>
    @endif

</head>

@php
    $hideViewDetails = (int) ($themeCustomization?->show_view_details ?? 1) !== 1;
@endphp
<body class="gotop frontend-body {{ $hideViewDetails ? 'hide-view-details' : '' }}">


    @php
        $subtotal = Cart::instance('shopping')->subtotal();
    @endphp
    <div class="mobile-menu">
        <div class="mobile-menu-logo">
            <div class="logo-image">
                <img src="{{ asset($generalsetting?->white_logo) }}" alt="" />
            </div>
            <div class="mobile-menu-close">
                <i class="fa fa-times"></i>
            </div>
        </div>
        <ul class="first-nav">
            @foreach ($menucategories as $scategory)
                <li class="parent-category">
                    <a href="{{ url('category/' . $scategory?->slug) }}" class="menu-category-name">
                        <img src="{{ asset($scategory->image) }}" alt="" class="side_cat_img" />
                        {{ $scategory->name }}
                    </a>
                    @if ($scategory?->menusubcategories->count() > 0)
                        <span class="menu-category-toggle">
                            <i class="fa fa-chevron-down"></i>
                        </span>
                    @endif
                    <ul class="second-nav submenu-collapsed">
                        @foreach ($scategory?->menusubcategories as $subcategory)
                            <li class="parent-subcategory">
                                <a href="{{ url('subcategory/' . $subcategory?->slug) }}"
                                    class="menu-subcategory-name">{{ $subcategory->subcategoryName }}</a>
                                @if ($subcategory?->menuchildcategories->count() > 0)
                                    <span class="menu-subcategory-toggle"><i class="fa fa-chevron-down"></i></span>
                                @endif
                                <ul class="third-nav submenu-collapsed">
                                    @foreach ($subcategory?->menuchildcategories as $childcat)
                                        <li class="childcategory"><a href="{{ url('products/' . $childcat->slug) }}"
                                                class="menu-childcategory-name">{{ $childcat->childcategoryName }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>
    <header id="navbar_top">
        <div class="mobile-header navbar navbar-light fixed-top shadow-sm">
            <div class="mobile-logo">
                <div class="menu-bar">
                    <a class="toggle">
                        <i class="fa-solid fa-bars"></i>
                    </a>
                </div>
                <div class="menu-logo">
                    <a href="{{ route('home') }}"><img src="{{ asset($generalsetting?->white_logo) }}"
                            alt="" /></a>
                </div>
                <div class="menu-bag">
                    <a href="{{ route('customer.checkout') }}" class="margin-shopping">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="mobilecart-qty">{{ Cart::instance('shopping')->count() }}</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="mobile-search mobile-search-offset">
            <form id="searchForm" action="{{ route('search') }}">
                <input type="text" placeholder="Search Product ... " value=""
                    class="msearch_keyword msearch_click src" name="keyword" />
                <button type="submit" class="search-submit-btn" aria-label="Search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
            </form>
            <div class="search_result"></div>
        </div>

        <div class="main-header">
            <section class="site-top-bar text-white py-2">
                <div class="container-fluid">
                    <marquee behavior="scroll" direction="left" scrollamount="6">
                        @foreach ($topheader as $top)
                            <a href="{{ $top->link }}" class="text-white text-decoration-none me-5">
                                {{ $top->title }}
                            </a>
                        @endforeach
                    </marquee>
                </div>
            </section>

            <div class="logo-area p-3 desktop-brand-bar">
                <div class="custom-container">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="logo-header desktop-brand-grid">
                                <div class="main-logo desktop-brand-logo">
                                    <a href="{{ route('home') }}"><img
                                            src="{{ asset($generalsetting?->white_logo) }}" alt="" /></a>
                                </div>
                                <div class="main-search desktop-brand-search">
                                    <form id="MainSearch" class="desktop-brand-form" action="{{ route('search') }}">
                                        <input type="text" placeholder="Search Product..."
                                            class="search_keyword search_click mainsrc" name="keyword" />

                                        <button type="submit" class="search-submit-btn" aria-label="Search">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                    </form>

                                    <div class="search_result"></div>
                                </div>

                                <div class="header-list-items desktop-brand-actions">
                                    <div class="helpline-wrapper">
                                        <div class="header-action-menu">
                                            <div class="action-item action-track">
                                                <a href="{{ route('customer.order_track') }}" title="Track Your Order">
                                                    <i class="fa-solid fa-truck-fast"></i>
                                                </a>
                                            </div>

                                            @if (Auth::guard('customer')->user())
                                                <div class="action-item action-login">
                                                    <a href="{{ route('customer.account') }}"
                                                        title="{{ Str::limit(Auth::guard('customer')->user()->name, 14) }}">
                                                        <i class="woodmart woodmart-user"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="action-item action-login">
                                                    <a href="{{ route('customer.login') }}" title="Login / Sign Up">
                                                        <i class="woodmart woodmart-user"></i>
                                                    </a>
                                                </div>
                                            @endif

                                            <div class="action-item action-cart" id="cart-qty">
                                                <a href="">
                                                    <i class="woodmart woodmart-cart"></i>
                                                    <span>{{ Cart::instance('shopping')->count() }}</span>
                                                </a>

                                                <div class="cshort-summary">
                                                    <ul>
                                                        @foreach (Cart::instance('shopping')->content() as $key => $value)
                                                            <li>
                                                                <a href=""><img
                                                                        src="{{ asset($value->options->image) }}"
                                                                        alt="" /></a>
                                                            </li>
                                                            <li><a
                                                                    href="">{{ Str::limit($value->name, 30) }}</a>
                                                            </li>
                                                            <li>Qty: {{ $value->qty }}</li>
                                                            <li>
                                                                <p>৳{{ $value->price }}</p>
                                                                <button class="remove-cart cart_remove"
                                                                    data-id="{{ $value->rowId }}"><i
                                                                        class="fa-regular fa-trash-can trash_icon"
                                                                        title="Delete this item"></i></button>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                    <p><strong>Total : Tk {{ $subtotal }}</strong></p>
                                                    <a href="{{ route('customer.checkout') }}" class="go_cart"> Order
                                                        Now </a>
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
            <div class="menu-area">
                <div class="custom-container">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="catagory_menu">
                                <div class="offcanvas offcanvas-start shadow-sm offcanvas-sidebar" tabindex="-1" id="sidebar">
                                        <div class="offcanvas-header py-2 px-3 border-bottom">
                                            <h6 class="offcanvas-title mb-0 text-uppercase">Categories</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                                aria-label="Close"></button>
                                        </div>

                                        <div class="offcanvas-body p-0 bg-light category-drawer">
                                            <ul class="list-group rounded-0 drawer-group">
                                                @foreach ($menucategories as $scategory)
                                                    <li
                                                        class="list-group-item py-2 px-3 text-dark bg-white border-0 border-bottom drawer-item">
                                                        <div class="d-flex justify-content-between align-items-center drawer-item-head">
                                                            <a href="{{ url('category/' . $scategory->slug) }}"
                                                                class="text-decoration-none text-dark d-flex align-items-center small fw-semibold drawer-link">
                                                                <img src="{{ asset($scategory->image) }}"
                                                                    alt="" class="me-2 drawer-link-image" width="20"
                                                                    height="20" />
                                                                {{ $scategory->name }}
                                                            </a>
                                                            @if ($scategory->menusubcategories->count() > 0)
                                                                <span class="text-muted drawer-trigger" role="button"
                                                                    data-submenu-toggle>
                                                                    <i class="fa fa-chevron-down small"></i>
                                                                </span>
                                                            @endif
                                                        </div>

                                                        @if ($scategory->menusubcategories->count() > 0)
                                                            <ul class="list-group list-group-flush ms-3 mt-2 bg-white rounded submenu-collapsed drawer-submenu drawer-submenu-level-2">
                                                                @foreach ($scategory->menusubcategories as $subcategory)
                                                                    <li class="list-group-item py-1 px-2 border-0 drawer-submenu-item">
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center drawer-item-head">
                                                                            <a href="{{ url('subcategory/' . $subcategory->slug) }}"
                                                                                class="text-decoration-none text-dark small drawer-sublink">
                                                                                {{ $subcategory->subcategoryName }}
                                                                            </a>
                                                                            @if ($subcategory->menuchildcategories->count() > 0)
                                                                                <span class="text-muted drawer-trigger"
                                                                                    role="button"
                                                                                    data-submenu-toggle>
                                                                                    <i
                                                                                        class="fa fa-chevron-down small"></i>
                                                                                </span>
                                                                            @endif
                                                                        </div>

                                                                        @if ($subcategory->menuchildcategories->count() > 0)
                                                                            <ul class="list-group list-group-flush ms-3 mt-2 bg-white submenu-collapsed drawer-submenu drawer-submenu-level-3">
                                                                                @foreach ($subcategory->menuchildcategories as $childcat)
                                                                                    <li
                                                                                        class="list-group-item py-1 ps-3 pe-2 border-0 drawer-child-item">
                                                                                        <a href="{{ url('products/' . $childcat->slug) }}"
                                                                                            class="text-decoration-none text-dark small drawer-child-link">
                                                                                            {{ $childcat->childcategoryName }}
                                                                                        </a>
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>{{-- end offcanvas --}}
                                <ul class="menu-list">
                                    <li class="cat_bar desktop-category-item">
                                      
                                            <a style="margin-top: -13px;" class="text-dark menu-category-link" type="button" data-bs-toggle="offcanvas"
                                                data-bs-target="#sidebar" aria-controls="sidebar">
                                                <span class="wd-tools-icon"></span>
                                            </a>
                                         
                                        
                                    </li>
                                    <li class="cat_bar desktop-category-item">
                                     
                                        <a class="text-dark menu-category-link" href="{{ route('home') }}"> <i
                                                class="fas fa-home"></i> Home </a>
                                    </li>

                                    @foreach ($menucategories as $scategory)
                                        <li class="cat_bar desktop-category-item">
                                            <a class="text-dark menu-category-link"
                                                href="{{ url('category/' . $scategory->slug) }}">
                                                <span class="cat_head">{{ $scategory->name }}</span>
                                                @if ($scategory->menusubcategories->count() > 0)
                                                    <i class="fa-solid fa-angle-down cat_down"></i>
                                                @endif
                                            </a>
                                            @if ($scategory->menusubcategories->count() > 0)
                                                <ul class="Cat_menu desktop-submenu">
                                                    @foreach ($scategory->menusubcategories as $subcat)
                                                        <li class="Cat_list cat_list_hover desktop-submenu-item">
                                                            <a class="text-dark desktop-submenu-link"
                                                                href="{{ url('subcategory/' . $subcat->slug) }}">
                                                                <span>{{ Str::limit($subcat->subcategoryName, 25) }}</span>
                                                                @if ($subcat->menuchildcategories->count() > 0)
                                                                    <i class="fa-solid fa-chevron-right cat_down"></i>
                                                                @endif
                                                            </a>
                                                            @if ($subcat->menuchildcategories->count() > 0)
                                                                <ul class="child_menu desktop-child-menu">
                                                                    @foreach ($subcat->menuchildcategories as $childcat)
                                                                        <li class="child_main desktop-child-item">
                                                                            <a class="text-dark desktop-child-link"
                                                                                href="{{ url('products/' . $childcat->slug) }}">
                                                                                {{ $childcat->childcategoryName }}
                                                                            </a>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>


                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- main-header end -->
    </header>
    <div id="content">
        @yield('content')
    </div>
    <!-- content end -->
    <footer class="storefront-footer">
        <div class="footer-top-section footer-top py-5 px-3">
            <div class="custom-container">
                <div class="row gy-1">

                    <!-- Column 1: Logo & Description -->
                    <div class="col-md-3">
                        <!-- Desktop -->
                        <div class="d-none d-md-block text-md-start text-center">
                            <a href="{{ route('home') }}">

                                <img src="{{ asset($generalsetting?->white_logo) }}" alt="Logo"
                                    class="img-fluid mb-3 footer-logo-desktop" />
                            </a>
                            <h3 class="small footer-text"> {!! $generalsetting?->description !!}</h3>


                        </div>

                        <!-- Mobile Accordion -->
                        <div class="accordion d-md-none" id="footerAccordion1">
                            <div class="accordion-item footer-accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed footer-accordion-button py-2" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse1">
                                        About
                                    </button>
                                </h2>
                                <div id="collapse1" class="accordion-collapse collapse"
                                    data-bs-parent="#footerAccordion1">
                                    <div class="accordion-body footer-accordion-body py-2">
                                        <a href="{{ route('home') }}">
                                            <img src="{{ asset($generalsetting?->white_logo) }}" alt="Logo"
                                                class="img-fluid mb-2 footer-logo-mobile" />
                                        </a>
                                        <h4 class="small opacity-75 mb-0 footer-text">{!! $generalsetting?->description !!}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <!-- Desktop -->
                        <div class="d-none d-md-block">
                            <h5 class="fw-bold mb-3 text-uppercase footer-heading">Contact Us</h5>
                            <p class="mb-2">
                                <a href="#"
                                    class="footer-link text-decoration-none d-inline-flex align-items-center">
                                    <i class="fa-solid fa-map me-2 footer-icon-muted"></i> {{ $contact?->address }}
                                </a>
                            </p>

                            <p class="mb-2">
                                <i class="fa-solid fa-mobile-screen-button me-2 footer-icon-accent"></i>
                                <a href="tel:{{ $contact?->hotline }}"
                                    class="text-decoration-none footer-link">
                                    {{ $contact?->hotline }}
                                </a>
                            </p>


                            <p><i class="fa-solid fa-envelope me-2 footer-icon-accent"></i>
                                <a href="mailto:{{ $contact?->hotmail }}"
                                    class="text-decoration-none footer-link">{{ $contact?->hotmail }}</a>
                            </p>
                        </div>

                        <!-- Mobile Accordion -->
                        <div class="accordion d-md-none" id="footerAccordion2">
                            <div class="accordion-item footer-accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed footer-accordion-button py-2" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse2">
                                        Contact Us
                                    </button>
                                </h2>
                                <div id="collapse2" class="accordion-collapse collapse"
                                    data-bs-parent="#footerAccordion2">
                                    <div class="accordion-body footer-accordion-body py-2">
                                        <p class="mb-2 footer-text"><i
                                                class="fa-solid fa-map me-2 footer-icon-muted"></i>{{ $contact?->address }}
                                        </p>

                                        <p class="mb-2"><i class="fa-solid fa-headphones me-2 footer-icon-accent"></i>
                                            <a href="tel:{{ $contact?->hotline }}"
                                                class="text-decoration-none footer-link">{{ $contact?->hotline }}</a>
                                        </p>
                                        <p class="mb-0"><i class="fa-solid fa-envelope me-2 footer-icon-accent"></i>
                                            <a href="mailto:{{ $contact?->hotmail }}"
                                                class="text-decoration-none footer-link">{{ $contact?->hotmail }}</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Useful Links -->
                    <div class="col-md-3">
                        <!-- Desktop -->
                        <div class="d-none d-md-block">
                            <h5 class="fw-bold mb-3 text-uppercase footer-heading">Useful Links</h5>
                            <div class="useful-links">
                                <p class="mb-2"><a href="{{ route('contact') }}"
                                        class="text-decoration-none footer-link">Contact Us</a></p>
                                @foreach ($cmnmenu as $page)
                                    <p class="mb-2"><a href="{{ route('page', ['slug' => $page->slug]) }}"
                                            class="text-decoration-none footer-link">{{ $page->name }}</a>
                                    </p>
                                @endforeach
                            </div>
                        </div>

                        <!-- Mobile Accordion -->
                        <div class="accordion d-md-none" id="footerAccordion3">
                            <div class="accordion-item footer-accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed footer-accordion-button py-2" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse3">
                                        Useful Links
                                    </button>
                                </h2>
                                <div id="collapse3" class="accordion-collapse collapse"
                                    data-bs-parent="#footerAccordion3">
                                    <div class="accordion-body footer-accordion-body py-2">
                                        <p class="mb-2"><a href="{{ route('contact') }}"
                                                class="text-decoration-none footer-link">Contact Us</a></p>
                                        @foreach ($cmnmenu as $page)
                                            <p class="mb-2"><a href="{{ route('page', ['slug' => $page->slug]) }}"
                                                    class="text-decoration-none footer-link">{{ $page->name }}</a>
                                            </p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 4: Social -->
                    <div class="col-md-3">
                        <!-- Desktop -->
                        <div class="d-none d-md-block">
                            <h5 class="fw-bold mb-3 text-uppercase footer-heading">Stay Connected</h5>

                            <div class="d-flex flex-column gap-2 mt-2">
                                <a href="#" target="_blank">
                                    <img src="{{ asset('app.png') }}" alt="Google Play Store" class="store-badge">
                                </a>
                                <a href="#" target="_blank">
                                    <img src="{{ asset('google.png') }}" alt="Apple App Store" class="store-badge">
                                </a>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mb-3 mt-2">
                                @foreach ($socialicons as $value)
                                    <a href="{{ $value->link }}" target="_blank"
                                        class="d-flex align-items-center justify-content-center rounded-circle social-icon"
                                        style="background: {{ $value->color }};">
                                        <i class="{{ $value->icon }}"></i>
                                    </a>
                                @endforeach
                            </div>

                        </div>

                        <!-- Mobile Accordion -->
                        <div class="accordion d-md-none" id="footerAccordion4">
                            <div class="accordion-item footer-accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed footer-accordion-button py-2" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse4">
                                        Stay Connected
                                    </button>
                                </h2>
                                <div id="collapse4" class="accordion-collapse collapse"
                                    data-bs-parent="#footerAccordion4">
                                    <div class="accordion-body footer-accordion-body py-2">
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            @foreach ($socialicons as $value)
                                                <a href="{{ $value->link }}" target="_blank"
                                                    class="d-flex align-items-center justify-content-center rounded-circle social-icon"
                                                    style="background: {{ $value->color }};">
                                                    <i class="{{ $value->icon }}"></i>
                                                </a>
                                            @endforeach
                                        </div>
                                        <!-- App Store / Play Store buttons -->
                                        <div class="d-flex flex-column gap-2 mt-2">
                                            <a href="#" class="w-100" target="_blank">
                                                <img class="w-100 store-badge" src="{{ asset('app.png') }}" alt="Google Play Store">
                                            </a>
                                            <a href="#" class="w-100" target="_blank">
                                                <img class="w-100 store-badge" src="{{ asset('google.png') }}" alt="Apple App Store">
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div><div class="footer-bottom footer-bottom-section py-3">
            <div class="container">
                <div class="row align-items-center gy-3">
                    <div class="col-md-9 text-center text-md-start">
                        <p class="mb-0 footer-bottom-copy">
                            © <span class="footer-accent-text">{{ request()->getHost() }}</span> — All rights reserved.
                            <br class="d-md-none">
                            Developed with <span class="footer-heart">❤️</span> by
                            <a href="https://codexlabbd.com" target="_blank"
                                class="fw-bold footer-bottom-link text-decoration-none">
                                CodexLab BD
                            </a>
                        </p>
                    </div>


                    <!-- Right: Payment Methods -->
                    <div class="col-md-3 text-center text-md-end">
                        <img src="{{ asset('frontEnd/images/payment2.png') }}" alt="Payment Methods"
                            class="img-fluid footer-payment-image">
                    </div>
                </div>
            </div>
        </div>
    </footer><div class="fixed-contact-toggle">
        <!-- Toggle Button -->


        <!-- Hidden Contact Buttons -->
        <div class="fixed-contact-buttons" id="contactButtons">
            <!-- WhatsApp -->
            <a href="https://api.whatsapp.com/send?phone={{ $contact?->hotline }}" target="_blank"
                title="Chat on WhatsApp">
                <i class="fa-brands fa-whatsapp"></i>
            </a>

            <!-- Messenger -->
            <a href="https://m.me/{{ $contact?->facebook_page }}" target="_blank" class="messenger"
                title="Chat on Messenger">
                <i class="fa-brands fa-facebook-messenger"></i>
            </a>

            <!-- Phone -->
            <a href="tel:{{ $contact?->hotline }}" class="phone" title="Call Now">
                <i class="fa-solid fa-phone"></i>
            </a>

        </div>
        <div class="message-toggle" data-contact-toggle title="Contact Options">
            <i class="fa-solid fa-comment-dots"></i>
        </div>
    </div>

    <div class="footer_nav">
        <ul>
            <li>
                <a class="toggle">
                    <span>
                        <i class="fa-solid fa-bars"></i>
                    </span>
                    <span>Category</span>
                </a>
            </li>
            <li>
                <a href="{{ $contact?->facebook }}">
                    <span>
                        <i class="fa-brands fa-facebook"></i>
                    </span>
                    <span>Facebook</span>
                </a>
            </li>

            <li class="mobile_home mobile-home-highlight">
                <a href="{{ route('home') }}">
                    <span><i class="fa-solid fa-home"></i></span> <span>Home</span>
                </a>
            </li>

            <li>
                <a href="{{ route('customer.checkout') }}">
                    <span>
                        <i class="fa-solid fa-cart-shopping"></i>
                    </span>
                    <span>Cart (<b class="mobilecart-qty">{{ Cart::instance('shopping')->count() }}</b>)</span>
                </a>
            </li>
            @if (Auth::guard('customer')->user())
                <li>
                    <a href="{{ route('customer.account') }}">
                        <span>
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span>Account</span>
                    </a>
                </li>
            @else
                <li>
                    <a href="{{ route('customer.login') }}">
                        <span>
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span>Login</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>


    <div class="scrolltop">
        <div class="scroll">
            <i class="fa fa-angle-up"></i>
        </div>
    </div>

    <!-- /. fixed sidebar -->

    <div id="custom-modal"></div>
    <div id="page-overlay"></div>


        <script src="{{ asset('frontEnd/js/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ asset('frontEnd/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('frontEnd/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('frontEnd/js/mobile-menu.js') }}"></script>
    <script src="{{ asset('frontEnd/js/wsit-menu.js') }}"></script>
    <script src="{{ asset('frontEnd/js/mobile-menu-init.js') }}"></script>
    <script src="{{ asset('frontEnd/js/wow.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
    <script src="{{ asset('backEnd/assets/js/toastr.min.js') }}"></script>
    <script>
        window.frontendConfig = {
            csrfToken: @json(csrf_token()),
            clientIp: @json(request()->ip()),
            routes: {
                quickView: @json(route('quickview')),
                cartStore: @json(route('cart.store')),
                cartRemove: @json(route('cart.remove')),
                cartIncrement: @json(route('cart.increment')),
                cartDecrement: @json(route('cart.decrement')),
                cartCount: @json(route('cart.count')),
                mobileCartCount: @json(route('mobile.cart.count')),
                shippingCharge: @json(route('shipping.charge')),
                liveSearch: @json(route('livesearch')),
                districts: @json(route('districts')),
                facebookPageView: @json(route('facebook.pageview_capi')),
                tiktokPageView: @json(route('tiktok.pageview_capi')),
                addToCartBase: @json(url('add-to-cart'))
            },
            meta: {
                facebookPixelId: @json($facebookPixel?->code ?? ''),
                facebookCapiEnabled: @json(!empty($facebookPixel?->code) && !empty($facebookPixel?->access_token)),
                tiktokPixelId: @json($tiktokPixel?->code ?? ''),
                tiktokCapiEnabled: @json(!empty($tiktokPixel?->code) && !empty($tiktokPixel?->access_token))
            },
            marketing: {
                ga4MeasurementId: @json($marketingConfig?->ga4_measurement_id ?? ''),
                googleAdsId: @json($marketingConfig?->google_ads_id ?? ''),
                googleAdsConversionLabel: @json($marketingConfig?->google_ads_conversion_label ?? ''),
                utmTrackingEnabled: @json((bool) ($marketingConfig?->utm_tracking_enabled ?? false)),
                abandonedCartEnabled: @json((bool) ($marketingConfig?->abandoned_cart_enabled ?? false)),
                visitorAnalyticsEnabled: @json((bool) ($marketingConfig?->status ?? false))
            },
            marketingRoutes: {
                abandonedCartSync: @json(route('marketing.abandoned_cart.sync')),
                visitorAnalyticsLog: @json(route('marketing.visitor_analytics.log')),
                merchantFeed: @json(route('merchant.feed')),
                googleEventLog: @json(route('marketing.google_event_log'))
            },
            sliderItems: @json($sliderItemSettings)
        };

        window.getSliderItems = function(key, fallback) {
            const sliderItems = window.frontendConfig?.sliderItems || {};
            const defaults = fallback || { mobile: 2, tablet: 3, desktop: 4 };
            const config = sliderItems[key] || {};

            return {
                mobile: Math.max(1, parseInt(config.mobile ?? defaults.mobile, 10)),
                tablet: Math.max(1, parseInt(config.tablet ?? defaults.tablet, 10)),
                desktop: Math.max(1, parseInt(config.desktop ?? defaults.desktop, 10))
            };
        };

        (function () {
            const marketingConfig = window.frontendConfig?.marketing || {};
            const marketingRoutes = window.frontendConfig?.marketingRoutes || {};

            if (!marketingConfig.visitorAnalyticsEnabled || !marketingRoutes.visitorAnalyticsLog) {
                return;
            }

            const pagePath = window.location.pathname || '/';
            const visitKey = `visitor-analytics:${pagePath}`;

            if (sessionStorage.getItem(visitKey)) {
                return;
            }

            sessionStorage.setItem(visitKey, '1');

            fetch(marketingRoutes.visitorAnalyticsLog, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.frontendConfig?.csrfToken || ''
                },
                body: JSON.stringify({
                    page_path: pagePath,
                    page_url: window.location.href,
                    referrer_url: document.referrer || ''
                })
            }).catch(function () {
                sessionStorage.removeItem(visitKey);
            });
        })();
    </script>
    <script src="{{ asset('frontEnd/js/master-layout.js') }}?v=1.0.1"></script>
    {!! Toastr::message() !!}
    @stack('script')
</body>

</html>
