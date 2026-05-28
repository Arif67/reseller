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
    <link rel="stylesheet" href="{{ route('frontend.theme_css') }}?v=1.0.0" />
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
        fbq('track', 'PageView');
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

        {{-- Desktop Daraz-style Header --}}
        <div class="main-header d-none d-lg-block">
            @include('frontEnd.components.common.header')
        </div>

        {{-- Offcanvas Category Sidebar (Daraz style) --}}
        <style>
        /* ── Daraz Sidebar ── */
        .dz-sidebar { width: 260px !important; }
        .dz-sidebar-header {
            background: #F85606;
            padding: 0 16px;
            height: 50px;
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0;
        }
        .dz-sidebar-header-title {
            display: flex; align-items: center; gap: 8px;
            color: #fff; font-size: 14px; font-weight: 700; letter-spacing: .3px;
        }
        .dz-sidebar-header-title i { font-size: 15px; }
        .dz-sidebar-close {
            background: rgba(255,255,255,0.2);
            border: 0; outline: none;
            width: 28px; height: 28px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; cursor: pointer; transition: background .15s;
            padding: 0;
        }
        .dz-sidebar-close:hover { background: rgba(255,255,255,0.35); }
        .dz-sidebar-close i { font-size: 13px; }

        /* scrollable body */
        .dz-sidebar-body {
            overflow-y: auto; overflow-x: hidden;
            flex: 1; padding: 0; background: #fff;
        }
        .dz-sidebar-body::-webkit-scrollbar { width: 4px; }
        .dz-sidebar-body::-webkit-scrollbar-track { background: #f5f5f5; }
        .dz-sidebar-body::-webkit-scrollbar-thumb { background: #ddd; border-radius: 4px; }

        /* category item */
        .dz-cat-item {
            border-bottom: 1px solid #f2f2f2;
        }
        .dz-cat-row {
            display: flex; align-items: center;
            padding: 10px 14px;
            cursor: pointer;
            transition: background .12s;
            gap: 10px;
        }
        .dz-cat-row:hover { background: #fff5f0; }
        .dz-cat-row:hover .dz-cat-name { color: #F85606; }
        .dz-cat-item.open > .dz-cat-row { background: #fff5f0; border-left: 3px solid #F85606; }
        .dz-cat-item.open > .dz-cat-row .dz-cat-name { color: #F85606; }
        .dz-cat-item.open > .dz-cat-row .dz-cat-chevron { transform: rotate(180deg); color: #F85606; }

        .dz-cat-img {
            width: 32px; height: 32px; border-radius: 6px;
            object-fit: cover; flex-shrink: 0;
            background: #f5f5f5;
        }
        .dz-cat-name {
            flex: 1; font-size: 13px; font-weight: 600; color: #333;
            text-decoration: none; line-height: 1.3;
            transition: color .12s;
        }
        .dz-cat-chevron {
            font-size: 10px; color: #bbb;
            transition: transform .2s, color .12s;
            flex-shrink: 0;
        }

        /* sub list */
        .dz-subcat-list {
            display: none;
            background: #fafafa;
            border-top: 1px solid #f0f0f0;
            padding: 4px 0;
        }
        .dz-cat-item.open > .dz-subcat-list { display: block; }

        .dz-subcat-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 8px 14px 8px 56px;
            cursor: pointer; gap: 6px;
            transition: background .12s;
        }
        .dz-subcat-row:hover { background: #fff0eb; }
        .dz-subcat-row:hover .dz-subcat-name { color: #F85606; }
        .dz-subcat-item.open > .dz-subcat-row .dz-subcat-name { color: #F85606; }
        .dz-subcat-item.open > .dz-subcat-row .dz-cat-chevron { transform: rotate(180deg); color: #F85606; }

        .dz-subcat-name {
            flex: 1; font-size: 12px; color: #444;
            text-decoration: none; transition: color .12s;
        }

        /* child list */
        .dz-child-list {
            display: none;
            background: #fff;
            border-top: 1px solid #f0f0f0;
            padding: 4px 0;
        }
        .dz-subcat-item.open > .dz-child-list { display: block; }

        .dz-child-link {
            display: block;
            padding: 7px 14px 7px 70px;
            font-size: 11.5px; color: #555;
            text-decoration: none;
            transition: background .12s, color .12s;
        }
        .dz-child-link:hover { background: #fff0eb; color: #F85606; }
        </style>

        <div class="offcanvas offcanvas-start dz-sidebar d-flex flex-column p-0" tabindex="-1" id="sidebar" style="border: none;">
            {{-- Header --}}
            <div class="dz-sidebar-header">
                <div class="dz-sidebar-header-title">
                    <i class="fa-solid fa-bars"></i>
                    All Categories
                </div>
                <button class="dz-sidebar-close" data-bs-dismiss="offcanvas" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="dz-sidebar-body offcanvas-body">
                @foreach ($menucategories as $scategory)
                    <div class="dz-cat-item">
                        <div class="dz-cat-row" @if($scategory->menusubcategories->count() > 0) data-dz-toggle @endif>
                            <img src="{{ asset($scategory->image) }}" alt="{{ $scategory->name }}" class="dz-cat-img">
                            <a href="{{ url('category/' . $scategory->slug) }}" class="dz-cat-name" onclick="event.stopPropagation()">
                                {{ $scategory->name }}
                            </a>
                            @if ($scategory->menusubcategories->count() > 0)
                                <i class="fa-solid fa-chevron-down dz-cat-chevron"></i>
                            @endif
                        </div>

                        @if ($scategory->menusubcategories->count() > 0)
                            <div class="dz-subcat-list">
                                @foreach ($scategory->menusubcategories as $subcategory)
                                    <div class="dz-subcat-item">
                                        <div class="dz-subcat-row" @if($subcategory->menuchildcategories->count() > 0) data-dz-toggle @endif>
                                            <a href="{{ url('subcategory/' . $subcategory->slug) }}" class="dz-subcat-name" onclick="event.stopPropagation()">
                                                {{ $subcategory->subcategoryName }}
                                            </a>
                                            @if ($subcategory->menuchildcategories->count() > 0)
                                                <i class="fa-solid fa-chevron-down dz-cat-chevron"></i>
                                            @endif
                                        </div>
                                        @if ($subcategory->menuchildcategories->count() > 0)
                                            <div class="dz-child-list">
                                                @foreach ($subcategory->menuchildcategories as $childcat)
                                                    <a href="{{ url('products/' . $childcat->slug) }}" class="dz-child-link">
                                                        {{ $childcat->childcategoryName }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        {{-- end offcanvas --}}

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-dz-toggle]').forEach(function (row) {
                row.addEventListener('click', function () {
                    var parent = row.parentElement;
                    var isOpen = parent.classList.contains('open');
                    // close siblings
                    parent.parentElement.querySelectorAll(':scope > .dz-cat-item.open, :scope > .dz-subcat-item.open').forEach(function (el) {
                        if (el !== parent) el.classList.remove('open');
                    });
                    parent.classList.toggle('open', !isOpen);
                });
            });
        });
        </script>
        <!-- header end -->
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
