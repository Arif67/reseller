<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    @php
        // Guard with isset(): shared view data is only injected on HTTP requests.
        $generalsetting = $generalsetting ?? null;
        $contact = $contact ?? null;
        $socialicons = $socialicons ?? [];

        $siteName = $generalsetting?->name ?? 'ShopBase BD';
        $logo = $generalsetting?->white_logo ?? $generalsetting?->favicon;
        $hotline = $contact?->hotline ?? '09647300100';
        $hotmail = $contact?->hotmail ?? 'support@shopbasebd.com';
        $address = $contact?->address ?? 'Mohammadpur, Dhaka-1207';
    @endphp

    <title>@yield('title', $siteName . ' — দেশের সর্ববৃহৎ ড্রপশিপিং ও রিসেলিং প্ল্যাটফর্ম')</title>
    <meta name="description"
        content="@yield('description', 'বিনা পুঁজিতে ঘরে বসে অনলাইন ব্যবসা শুরু করুন। দেশের সর্ববৃহৎ ড্রপশিপিং এবং রিসেলিং প্ল্যাটফর্মে যুক্ত হয়ে আয় করুন।')" />

    @if ($generalsetting?->favicon)
        <link rel="shortcut icon" href="{{ asset($generalsetting->favicon) }}" />
    @endif

    <!-- Fonts & icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        :root {
            --brand: #FF6A00;
            --brand-dark: #E85D00;
            --brand-soft: #FFF3EA;
            --ink: #1F2933;
            --muted: #6B7280;
            --line: #ECECEC;
            --radius: 16px;
            --shadow: 0 10px 30px rgba(31, 41, 51, .08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Hind Siliguri', system-ui, sans-serif;
            color: var(--ink);
            background: #fff;
            line-height: 1.6;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .container {
            width: min(1180px, 92%);
            margin: 0 auto;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 26px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: .2s ease;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--brand);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--brand-dark);
            transform: translateY(-2px);
        }

        .btn-ghost {
            background: transparent;
            color: var(--brand);
            border-color: var(--brand);
        }

        .btn-ghost:hover {
            background: var(--brand);
            color: #fff;
        }

        .btn-light {
            background: #fff;
            color: var(--brand);
        }

        .btn-light:hover {
            transform: translateY(-2px);
        }

        .btn-dark {
            background: var(--ink);
            color: #fff;
        }

        .btn-dark:hover {
            background: #0f1720;
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 8px 14px;
            font-size: 13.5px;
        }

        .section {
            padding: 72px 0;
        }

        .section--soft {
            background: var(--brand-soft);
        }

        .section-head {
            text-align: center;
            max-width: 640px;
            margin: 0 auto 48px;
        }

        .section-head .eyebrow {
            color: var(--brand);
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            font-size: 13px;
        }

        .section-head h2 {
            font-size: clamp(24px, 4vw, 36px);
            margin: 8px 0 12px;
            font-weight: 700;
        }

        .section-head p {
            color: var(--muted);
        }

        /* ===== Top bar ===== */
        .topbar {
            background: var(--ink);
            color: #fff;
            font-size: 13.5px;
        }

        .topbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            flex-wrap: wrap;
            gap: 6px;
        }

        .topbar a {
            opacity: .9;
        }

        .topbar a:hover {
            opacity: 1;
            color: var(--brand);
        }

        .topbar .tb-links {
            display: flex;
            gap: 14px;
            align-items: center;
        }

        .topbar .tb-sep {
            opacity: .4;
        }

        /* ===== Navbar ===== */
        .nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #fff;
            border-bottom: 1px solid var(--line);
        }

        .nav .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 0;
            gap: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 20px;
            color: var(--brand);
        }

        .brand img {
            height: 40px;
            width: auto;
        }

        .nav-menu {
            display: flex;
            gap: 28px;
            font-weight: 500;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            color: var(--brand);
        }

        .nav-cta {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        /* ===== Auth pill tabs (Reseller / Vendor) ===== */
        .auth-tabs {
            display: flex;
            background: #f1f3f5;
            border-radius: 50px;
            padding: 5px;
            margin: 0 auto 22px;
            max-width: 320px;
        }

        .auth-tab {
            flex: 1;
            text-align: center;
            padding: 10px 0;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14.5px;
            color: var(--muted);
            transition: .2s ease;
        }

        .auth-tab:hover {
            color: var(--brand);
        }

        .auth-tab.active {
            background: var(--brand);
            color: #fff;
            box-shadow: 0 4px 12px rgba(255, 106, 0, .3);
        }

        .auth-tab.active:hover {
            color: #fff;
        }

        .nav-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--ink);
            cursor: pointer;
        }

        /* Mobile sidebar pieces — hidden on desktop, activated in the responsive block */
        .nav-close {
            display: none;
            position: absolute;
            top: 16px;
            right: 18px;
            background: none;
            border: none;
            font-size: 24px;
            line-height: 1;
            color: var(--ink);
            cursor: pointer;
        }

        .nav-menu .nav-auth {
            display: none;
        }

        .nav-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            opacity: 0;
            visibility: hidden;
            transition: opacity .3s ease, visibility .3s ease;
            /* below the .nav header (z-index:50) so the sidebar, which lives
               inside the header's stacking context, stays above the overlay */
            z-index: 45;
        }

        .nav-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        body.nav-open {
            overflow: hidden;
        }

        /* ===== Hero ===== */
        .hero {
            background: #FFE790;
            padding: 70px 0 80px;
        }

        .hero .container {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            align-items: center;
            gap: 40px;
        }

        .hero .badge {
            display: inline-block;
            background: var(--brand-soft);
            color: var(--brand-dark);
            font-weight: 600;
            padding: 7px 16px;
            border-radius: 50px;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .hero h1 {
            font-size: clamp(30px, 5vw, 50px);
            line-height: 1.18;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .hero h1 span {
            color: var(--brand);
        }

        .hero p {
            color: var(--muted);
            font-size: 17px;
            margin-bottom: 28px;
        }

        .hero-actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .hero-visual {
            background: linear-gradient(135deg, var(--brand) 0%, #FF9A4D 100%);
            border-radius: 28px;
            padding: 36px;
            color: #fff;
            box-shadow: var(--shadow);
        }

        .hero-visual .hv-icon {
            font-size: 54px;
            margin-bottom: 14px;
        }

        .hv-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-top: 22px;
        }

        .hv-grid div {
            background: rgba(255, 255, 255, .15);
            border-radius: 14px;
            padding: 16px;
            text-align: center;
            backdrop-filter: blur(4px);
        }

        .hv-grid b {
            display: block;
            font-size: 22px;
        }

        .hv-grid small {
            opacity: .9;
        }

        /* ===== Stats ===== */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
            text-align: center;
        }

        .stat {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 28px 16px;
            box-shadow: var(--shadow);
        }

        .stat b {
            display: block;
            font-size: 32px;
            color: var(--brand);
            font-weight: 700;
        }

        .stat span {
            color: var(--muted);
        }

        /* ===== Cards grid ===== */
        .grid {
            display: grid;
            gap: 22px;
        }

        .grid-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        .grid-4 {
            grid-template-columns: repeat(4, 1fr);
        }

        .grid-6 {
            grid-template-columns: repeat(6, 1fr);
        }

        .card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 26px;
            transition: .25s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
            border-color: #FFD9BF;
        }

        .card .ic {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: var(--brand-soft);
            color: var(--brand);
            font-size: 24px;
            margin-bottom: 16px;
        }

        .card h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .card p {
            color: var(--muted);
            font-size: 14.5px;
        }

        /* Category tiles */
        .cat {
            text-align: center;
            padding: 22px 10px;
        }

        .cat .ic {
            margin: 0 auto 12px;
        }

        .cat .thumb {
            width: 66px;
            height: 66px;
            border-radius: 14px;
            overflow: hidden;
            margin: 0 auto 12px;
            background: var(--brand-soft);
            display: grid;
            place-items: center;
        }

        .cat .thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cat span {
            font-weight: 500;
            font-size: 14px;
        }

        /* ===== Category/Subcategory browser ===== */
        .cat-row {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 14px;
            margin-bottom: 22px;
        }

        .cat-tile {
            text-align: center;
            padding: 16px 8px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff;
            transition: .2s ease;
        }

        .cat-tile:hover {
            border-color: #FFD9BF;
            transform: translateY(-3px);
        }

        .cat-tile.active {
            border-color: var(--brand);
            background: var(--brand-soft);
        }

        .cat-tile .thumb,
        .cat-tile .ic {
            width: 54px;
            height: 54px;
            border-radius: 12px;
            margin: 0 auto 8px;
            background: var(--brand-soft);
            display: grid;
            place-items: center;
            overflow: hidden;
        }

        .cat-tile .ic {
            color: var(--brand);
            font-size: 22px;
        }

        .cat-tile .thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cat-tile span {
            font-size: 13px;
            font-weight: 500;
            display: block;
        }

        .subcat-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 26px;
        }

        .chip {
            padding: 8px 18px;
            border-radius: 50px;
            border: 1px solid var(--line);
            background: #fff;
            font-size: 14px;
            font-weight: 500;
            transition: .2s ease;
        }

        .chip:hover {
            border-color: var(--brand);
            color: var(--brand);
        }

        .chip.active {
            background: var(--brand);
            color: #fff;
            border-color: var(--brand);
        }

        .browse-title {
            font-size: 20px;
            margin-bottom: 18px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .product-card {
            display: flex;
            flex-direction: column;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(31, 41, 51, .05);
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 36px rgba(31, 41, 51, .14);
            border-color: #FFD9BF;
        }

        .product-card .pc-img {
            position: relative;
            aspect-ratio: 1 / 1;
            background: #f6f7f9;
            overflow: hidden;
        }

        .product-card .pc-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .35s ease;
        }

        .product-card:hover .pc-img img {
            transform: scale(1.06);
        }

        .product-card .pc-body {
            padding: 14px 16px 16px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .product-card h4 {
            font-size: 14.5px;
            font-weight: 600;
            line-height: 1.45;
            margin-bottom: 10px;
            color: var(--ink);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 42px;
        }

        .pc-price {
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-bottom: 14px;
        }

        .pc-label {
            font-size: 11.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--muted);
        }

        .pc-price b {
            color: var(--brand);
            font-size: 20px;
            line-height: 1.1;
        }

        .pc-btn {
            margin-top: auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 14px;
            border-radius: 10px;
            background: var(--brand-soft);
            color: var(--brand-dark);
            font-weight: 600;
            font-size: 13.5px;
            transition: background .2s ease, color .2s ease;
        }

        .product-card:hover .pc-btn {
            background: var(--brand);
            color: #fff;
        }

        @media (max-width: 900px) {
            .cat-row {
                grid-template-columns: repeat(4, 1fr);
            }

            .product-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 560px) {
            .cat-row {
                grid-template-columns: repeat(3, 1fr);
            }

            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* ===== Steps ===== */
        .step {
            position: relative;
        }

        .step .num {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--brand);
            color: #fff;
            display: grid;
            place-items: center;
            font-weight: 700;
            margin-bottom: 14px;
        }

        /* ===== Testimonials ===== */
        .quote {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 26px;
        }

        .quote .stars {
            color: #FFB100;
            margin-bottom: 10px;
        }

        .quote p {
            color: #374151;
            margin-bottom: 18px;
        }

        .quote .who {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .quote .who .av {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--brand-soft);
            color: var(--brand);
            display: grid;
            place-items: center;
            font-weight: 700;
        }

        .quote .who b {
            display: block;
        }

        .quote .who small {
            color: var(--muted);
        }

        /* ===== CTA band ===== */
        .cta-band {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: #fff;
            text-align: center;
            border-radius: 24px;
            padding: 54px 24px;
        }

        .cta-band h2 {
            font-size: clamp(24px, 4vw, 34px);
            margin-bottom: 12px;
        }

        .cta-band p {
            opacity: .92;
            max-width: 560px;
            margin: 0 auto 26px;
        }

        /* ===== Page banner (inner pages) ===== */
        .page-banner {
            background: var(--brand-soft);
            padding: 50px 0;
            text-align: center;
        }

        .page-banner .crumb {
            color: var(--brand);
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .page-banner h1 {
            font-size: clamp(26px, 4vw, 42px);
            margin: 8px 0 6px;
        }

        .page-banner p {
            color: var(--muted);
            max-width: 640px;
            margin: 0 auto;
        }

        /* ===== Footer ===== */
        .footer {
            background: var(--ink);
            color: #cbd2d9;
            padding: 56px 0 0;
        }

        .footer .fcols {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr 1.2fr;
            gap: 32px;
        }

        .footer h4 {
            color: #fff;
            margin-bottom: 16px;
            font-size: 16px;
        }

        .footer a {
            display: block;
            padding: 5px 0;
            color: #cbd2d9;
        }

        .footer a:hover {
            color: var(--brand);
        }

        .footer .brand {
            color: #fff;
            margin-bottom: 14px;
        }

        .footer .social {
            display: flex;
            gap: 10px;
            margin-top: 14px;
        }

        .footer .social a {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .1);
            display: grid;
            place-items: center;
        }

        .footer .social a:hover {
            background: var(--brand);
            color: #fff;
        }

        .footer .contact-line {
            display: flex;
            gap: 10px;
            padding: 6px 0;
        }

        .footer .contact-line i {
            color: var(--brand);
            margin-top: 4px;
        }

        .copyright {
            border-top: 1px solid rgba(255, 255, 255, .1);
            margin-top: 40px;
            padding: 20px 0;
            text-align: center;
            font-size: 14px;
        }

        /* ===== Responsive ===== */
        @media (max-width: 900px) {
            .hero .container {
                grid-template-columns: 1fr;
            }

            .grid-4,
            .grid-6 {
                grid-template-columns: repeat(3, 1fr);
            }

            .grid-3 {
                grid-template-columns: repeat(2, 1fr);
            }

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer .fcols {
                grid-template-columns: 1fr 1fr;
            }

            .nav-menu {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                right: auto;
                width: min(82vw, 320px);
                background: #fff;
                flex-direction: column;
                align-items: stretch;
                gap: 2px;
                padding: 70px 22px 28px;
                box-shadow: 6px 0 24px rgba(0, 0, 0, .16);
                transform: translateX(-100%);
                transition: transform .3s ease;
                overflow-y: auto;
                z-index: 60;
            }

            .nav-menu.open {
                transform: translateX(0);
            }

            .nav-menu a {
                padding: 13px 4px;
                border-bottom: 1px solid var(--line);
                font-size: 16px;
            }

            .nav-close {
                display: block;
            }

            .nav-overlay {
                display: block;
            }

            .nav-menu .nav-auth {
                display: flex;
                flex-direction: column;
                gap: 10px;
                margin-top: 20px;
            }

            .nav-menu .nav-auth .btn {
                width: 100%;
                text-align: center;
            }

            /* CTA buttons live inside the sidebar on mobile */
            .nav-cta .btn {
                display: none;
            }

            .nav-toggle {
                display: block;
            }
        }

        @media (max-width: 560px) {

            .grid-3,
            .grid-4,
            .grid-6 {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer .fcols {
                grid-template-columns: 1fr;
            }

            .topbar .tb-links {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Top bar -->
    <div class="topbar">
        <div class="container">
            <div class="tb-info">
                <i class="fa-solid fa-phone"></i> হটলাইন: {{ $hotline }}
            </div>
            <div class="tb-links">
                <a href="{{ Route::has('reseller.login') ? route('reseller.login') : '#' }}"><i
                        class="fa-solid fa-right-to-bracket"></i> লগইন</a>
                <a href="{{ Route::has('reseller.register') ? route('reseller.register') : '#' }}"><i
                        class="fa-solid fa-user-plus"></i> রেজিস্ট্রেশন</a>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <header class="nav">
        <div class="container">
            <a href="{{ route('home') }}" class="brand">
                @if ($logo)
                    <img src="{{ asset($logo) }}" alt="{{ $siteName }}" />
                @else
                    <i class="fa-solid fa-store"></i> {{ $siteName }}
                @endif
            </a>

            <nav class="nav-menu" id="navMenu">
                <button class="nav-close" id="navClose" aria-label="মেনু বন্ধ করুন"><i class="fa-solid fa-xmark"></i></button>
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">হোম</a>
                <a href="{{ route('landing.about') }}" class="{{ request()->routeIs('landing.about') ? 'active' : '' }}">আমাদের সম্পর্কে</a>
                <a href="{{ route('landing.services') }}" class="{{ request()->routeIs('landing.services') ? 'active' : '' }}">সার্ভিস</a>
                <a href="{{ route('landing.products') }}" class="{{ request()->routeIs('landing.products') ? 'active' : '' }}">প্রোডাক্ট</a>
                <a href="{{ route('landing.how') }}" class="{{ request()->routeIs('landing.how') ? 'active' : '' }}">যেভাবে কাজ করবেন</a>
                <a href="{{ route('landing.contact') }}" class="{{ request()->routeIs('landing.contact') ? 'active' : '' }}">যোগাযোগ</a>

                <div class="nav-auth">
                    <a href="{{ Route::has('reseller.login') ? route('reseller.login') : '#' }}"
                        class="btn btn-ghost">লগইন</a>
                    <a href="{{ Route::has('reseller.register') ? route('reseller.register') : '#' }}"
                        class="btn btn-primary">রেজিস্ট্রেশন</a>
                </div>
            </nav>

            <div class="nav-cta">
                <a href="{{ Route::has('reseller.login') ? route('reseller.login') : '#' }}"
                    class="btn btn-ghost">লগইন</a>
                <a href="{{ Route::has('reseller.register') ? route('reseller.register') : '#' }}"
                    class="btn btn-primary">রেজিস্ট্রেশন</a>
                <button class="nav-toggle" id="navToggle" aria-label="Menu"><i class="fa-solid fa-bars"></i></button>
            </div>
        </div>
    </header>
    <div class="nav-overlay" id="navOverlay"></div>

    @yield('content')

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="fcols">
                <div>
                    <div class="brand">
                        @if ($logo)
                            <img src="{{ asset($logo) }}" alt="{{ $siteName }}" style="height:40px" />
                        @else
                            <strong style="font-size:22px">{{ $siteName }}</strong>
                        @endif
                    </div>
                    <p>বিনা পুঁজিতে ঘরে বসে অনলাইন ব্যবসা শুরু করার দেশের সর্ববৃহৎ ড্রপশিপিং ও রিসেলিং প্ল্যাটফর্ম।</p>
                    <div class="social">
                        @forelse ($socialicons ?? [] as $value)
                            <a href="{{ $value->link }}" target="_blank"><i class="{{ $value->icon }}"></i></a>
                        @empty
                            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#"><i class="fa-brands fa-youtube"></i></a>
                            <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
                        @endforelse
                    </div>
                </div>

                <div>
                    <h4>মেনু</h4>
                    <a href="{{ route('landing.about') }}">আমাদের সম্পর্কে</a>
                    <a href="{{ route('landing.services') }}">সার্ভিস</a>
                    <a href="{{ route('landing.products') }}">প্রোডাক্ট</a>
                    <a href="{{ route('landing.how') }}">যেভাবে কাজ করবেন</a>
                    <a href="{{ route('landing.contact') }}">যোগাযোগ</a>
                </div>

                <div>
                    <h4>লিগ্যাল</h4>
                    <a href="#">টার্মস ও কন্ডিশন</a>
                    <a href="#">রিটার্ন ও রিফান্ড পলিসি</a>
                    <a href="#">প্রাইভেসি পলিসি</a>
                    <a href="#">ডকুমেন্টেশন</a>
                </div>

                <div>
                    <h4>যোগাযোগ</h4>
                    <div class="contact-line"><i class="fa-solid fa-location-dot"></i><span>{{ $address }}</span>
                    </div>
                    <div class="contact-line"><i class="fa-solid fa-phone"></i><a
                            href="tel:{{ $hotline }}">{{ $hotline }}</a></div>
                    <div class="contact-line"><i class="fa-solid fa-envelope"></i><a
                            href="mailto:{{ $hotmail }}">{{ $hotmail }}</a></div>
                </div>
            </div>

            <div class="copyright">
                Designed &amp; Developed by
                <a href="https://codexlabbd.com" target="_blank" rel="noopener"
                    style="color:var(--brand);font-weight:600">codexlabbd.com</a>
            </div>
        </div>
    </footer>

    <script>
        const toggle = document.getElementById('navToggle');
        const menu = document.getElementById('navMenu');
        const overlay = document.getElementById('navOverlay');
        const closeBtn = document.getElementById('navClose');

        const openMenu = () => {
            menu?.classList.add('open');
            overlay?.classList.add('open');
            document.body.classList.add('nav-open');
        };
        const closeMenu = () => {
            menu?.classList.remove('open');
            overlay?.classList.remove('open');
            document.body.classList.remove('nav-open');
        };

        toggle?.addEventListener('click', openMenu);
        closeBtn?.addEventListener('click', closeMenu);
        overlay?.addEventListener('click', closeMenu);
        menu?.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMenu));
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeMenu();
        });
    </script>
    @stack('scripts')
</body>

</html>
