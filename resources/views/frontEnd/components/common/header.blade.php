<style>
/* ══════════════════════════════════════════
   Daraz-style Header
══════════════════════════════════════════ */
:root {
    --dz-orange: #F85606;
    --dz-orange-dark: #d94800;
    --dz-white: #ffffff;
    --dz-topbar-bg: #F85606;
    --dz-logo-bg: #ffffff;
    --dz-nav-bg: #ffffff;
    --dz-nav-border: #f0f0f0;
    --dz-text-dark: #333333;
    --dz-text-muted: #888;
}

/* ── override sticky header bg ── */
#navbar_top { background: #fff !important; }
.main-header { background: transparent !important; }

/* ── TOP BAR ── */
.dz-topbar {
    background: #F85606 !important;
    height: 36px;
    display: flex;
    align-items: center;
}
.dz-topbar .dz-container {
    max-width: 1520px;
    margin: 0 auto;
    padding: 0 15px;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0;
}
.dz-top-item {
    position: relative;
    display: flex;
    align-items: center;
    height: 36px;
    padding: 0 12px;
    font-size: 12px;
    color: #fff;
    cursor: pointer;
    white-space: nowrap;
    border-right: 1px solid rgba(255,255,255,0.25);
    transition: background .15s;
}
.dz-top-item:last-child { border-right: 0; }
.dz-top-item:hover { background: rgba(0,0,0,0.12); }
.dz-top-item a { color: #fff; text-decoration: none; }
.dz-top-item a:hover { color: #fff; }
.dz-top-item > span { display: flex; align-items: center; gap: 4px; }

/* dropdowns */
.dz-top-popup {
    display: none;
    position: absolute;
    top: 36px;
    right: 0;
    background: #fff;
    border: 1px solid #e8e8e8;
    border-radius: 4px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    z-index: 9999;
    min-width: 180px;
}
.dz-top-item:hover .dz-top-popup { display: block; }

/* app popup */
.dz-app-popup {
    padding: 16px;
    text-align: center;
    width: 200px;
}
.dz-app-popup .ap-title {
    font-size: 13px;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}
.dz-app-popup .ap-qr img { width: 90px; height: 90px; border: 1px solid #eee; border-radius: 6px; padding: 4px; }
.dz-app-popup .ap-qr-ph {
    width: 90px; height: 90px; margin: 0 auto 8px;
    border: 2px dashed var(--dz-orange); border-radius: 6px;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    color: var(--dz-orange); font-size: 9px; gap: 4px; background: #fff8f5;
}
.dz-app-popup .ap-qr-ph i { font-size: 22px; }
.dz-app-popup .ap-tip { font-size: 10px; color: #888; margin-bottom: 10px; }
.dz-app-popup .ap-btns { display: flex; flex-direction: column; gap: 6px; }
.dz-app-popup .ap-btn {
    display: flex; align-items: center; gap: 7px;
    background: #1a1a1a; color: #fff; border-radius: 7px; padding: 7px 10px;
    text-decoration: none; font-size: 9px;
    transition: background .15s;
}
.dz-app-popup .ap-btn:hover { background: #000; color: #fff; text-decoration: none; }
.dz-app-popup .ap-btn i { font-size: 16px; flex-shrink: 0; }
.dz-app-popup .ap-btn .ab-sub { display: block; font-size: 8px; opacity: .7; }
.dz-app-popup .ap-btn .ab-label { display: block; font-size: 10px; font-weight: 700; }

/* help popup */
.dz-help-popup { width: 200px; }
.dz-help-popup ul { list-style: none; margin: 0; padding: 6px 0; }
.dz-help-popup ul li a {
    display: flex; align-items: center; gap: 8px;
    padding: 9px 16px; font-size: 12px; color: #333;
    text-decoration: none; transition: background .12s;
}
.dz-help-popup ul li a:hover { background: #fff5f0; color: var(--dz-orange); }
.dz-help-popup ul li a i { width: 16px; text-align: center; color: var(--dz-orange); font-size: 12px; }

/* account popup */
.dz-account-popup { width: 200px; }
.dz-account-popup ul { list-style: none; margin: 0; padding: 6px 0; }
.dz-account-popup ul li a {
    display: flex; align-items: center; gap: 8px;
    padding: 9px 16px; font-size: 12px; color: #333;
    text-decoration: none; transition: background .12s;
}
.dz-account-popup ul li a:hover { background: #fff5f0; color: var(--dz-orange); }
.dz-account-popup ul li a i { width: 16px; text-align: center; color: var(--dz-orange); font-size: 12px; }

/* ── LOGO BAR ── */
.dz-logobar {
    background: #ffffff !important;
    border-bottom: 1px solid #f0f0f0 !important;
    padding: 12px 0;
}
.dz-logobar .dz-container {
    max-width: 1520px;
    margin: 0 auto;
    padding: 0 15px;
    display: flex;
    align-items: center;
    gap: 20px;
}
.dz-logo { flex-shrink: 0; }
.dz-logo img { height: 48px; max-width: 160px; object-fit: contain; display: block; }

/* search */
.dz-search { flex: 1 1 0; min-width: 0; }
.dz-search-form {
    display: flex;
    border: 2px solid var(--dz-orange);
    border-radius: 4px;
    overflow: hidden;
    height: 44px;
}
.dz-search-form input {
    flex: 1; border: 0; outline: none;
    padding: 0 16px; font-size: 14px; color: #333;
    background: #fff;
}
.dz-search-form input::placeholder { color: #aaa; }
.dz-search-form button {
    background: #F85606 !important; border: 0; outline: none;
    color: #fff !important; cursor: pointer; transition: background .15s; white-space: nowrap;
    display: flex; align-items: center; justify-content: center; gap: 6px; flex-shrink: 0;
}
.dz-search-form button:hover { background: #d94800 !important; }
/* full style */
.dz-search-btn-full    { padding: 0 22px; font-size: 13px; font-weight: 700; letter-spacing: 0.5px; }
/* compact style */
.dz-search-btn-compact { padding: 0 14px; font-size: 12px; font-weight: 600; }
.dz-search-btn-compact i { font-size: 13px; }
/* icon style */
.dz-search-btn-icon    { padding: 0 14px; font-size: 16px; }
.dz-search-result {
    position: absolute; top: 100%; left: 0; right: 0;
    background: #fff; border: 1px solid #e0e0e0; border-top: 0;
    border-radius: 0 0 4px 4px; z-index: 999; max-height: 400px; overflow-y: auto;
}

/* cart */
.dz-cart {
    flex-shrink: 0; position: relative; cursor: pointer;
}
.dz-cart a {
    display: flex; align-items: center; gap: 8px;
    text-decoration: none; color: var(--dz-text-dark); padding: 6px 10px;
    border-radius: 6px; transition: background .15s;
}
.dz-cart a:hover { background: #fff5f0; }
.dz-cart-icon { position: relative; }
.dz-cart-icon i { font-size: 24px; color: var(--dz-orange); }
.dz-cart-count {
    position: absolute; top: -6px; right: -8px;
    background: var(--dz-orange); color: #fff;
    font-size: 10px; font-weight: 700;
    width: 18px; height: 18px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    line-height: 1;
}
.dz-cart-label { font-size: 12px; font-weight: 600; color: var(--dz-text-dark); }
.dz-cart-label span { display: block; font-size: 10px; color: #888; font-weight: 400; }

/* search wrapper position */
.dz-search-wrap { position: relative; }

/* ── NAV BAR ── */
.dz-navbar {
    background: #F85606 !important;
    border-bottom: none !important;
}
.dz-navbar .dz-container {
    max-width: 1520px;
    margin: 0 auto;
    padding: 0 15px;
    display: flex;
    align-items: stretch;
}

/* All categories button */
.dz-allcat-btn {
    display: flex; align-items: center; gap: 8px;
    background: rgba(0,0,0,0.18) !important; color: #fff !important;
    padding: 0 18px; font-size: 13px; font-weight: 700;
    cursor: pointer; white-space: nowrap; border: 0;
    text-decoration: none; transition: background .15s;
    min-height: 44px;
}
.dz-allcat-btn:hover { background: rgba(0,0,0,0.30) !important; color: #fff !important; }
.dz-allcat-btn i { font-size: 15px; }

/* nav links */
.dz-nav-links {
    display: flex; align-items: stretch;
    list-style: none; margin: 0; padding: 0; flex: 1;
}
.dz-nav-links > li {
    position: relative; display: flex; align-items: center;
}
.dz-nav-links > li > a {
    display: flex; align-items: center; gap: 4px;
    padding: 0 14px; font-size: 13px; font-weight: 600;
    color: #fff !important; text-decoration: none;
    height: 44px; white-space: nowrap;
    transition: color .15s, background .15s;
    border-bottom: 3px solid transparent;
}
.dz-nav-links > li > a:hover,
.dz-nav-links > li > a.active {
    color: #fff !important;
    border-bottom-color: rgba(255,255,255,0.8);
    background: rgba(0,0,0,0.12);
}
.dz-nav-links > li > a i { font-size: 10px; opacity: .8; }

/* sub dropdown */
.dz-nav-links > li:hover > .dz-nav-dropdown { display: block; }
.dz-nav-dropdown {
    display: none; position: absolute; top: 100%; left: 0;
    background: #fff; border: 1px solid #e8e8e8;
    border-radius: 0 0 6px 6px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.10);
    z-index: 9999; min-width: 200px;
    list-style: none; margin: 0; padding: 6px 0;
}
.dz-nav-dropdown li a {
    display: flex; align-items: center; justify-content: space-between;
    padding: 9px 16px; font-size: 12px; color: #333 !important;
    text-decoration: none; transition: background .12s;
    white-space: nowrap;
}
.dz-nav-dropdown li a:hover { background: #fff5f0; color: var(--dz-orange) !important; }
.dz-nav-dropdown li a i { font-size: 10px; opacity: .5; }

/* child dropdown */
.dz-nav-dropdown li { position: relative; }
.dz-nav-dropdown li:hover > .dz-nav-child { display: block; }
.dz-nav-child {
    display: none; position: absolute; left: 100%; top: 0;
    background: #fff; border: 1px solid #e8e8e8;
    border-radius: 0 6px 6px 6px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.10);
    z-index: 9999; min-width: 180px;
    list-style: none; margin: 0; padding: 6px 0;
}
.dz-nav-child li a {
    display: block; padding: 8px 16px; font-size: 12px;
    color: #333 !important; text-decoration: none; transition: background .12s;
    white-space: nowrap;
}
.dz-nav-child li a:hover { background: #fff5f0; color: var(--dz-orange) !important; }

/* Track order pill */
.dz-track-link {
    margin-left: auto; display: flex; align-items: center;
}
.dz-track-link a {
    display: flex; align-items: center; gap: 6px;
    padding: 0 16px; font-size: 12px; color: #fff !important;
    text-decoration: none; font-weight: 600; height: 44px;
    white-space: nowrap;
}
.dz-track-link a:hover { color: rgba(255,255,255,0.85) !important; }

/* ── MOBILE ── */
.dz-header-desktop { display: block; }
.dz-header-mobile { display: none; }

/* mobile header override */
.mobile-header { background: #F85606 !important; }
.mobile-header .toggle i,
.mobile-header .mobilecart-qty { color: #fff !important; }
.mobile-header .fa-cart-shopping { color: #fff !important; }
.mobile-search { background: #fff !important; border-bottom: 2px solid #F85606 !important; }
.mobile-search form button { background: #F85606 !important; color: #fff !important; }

@media (max-width: 991.98px) {
    .dz-topbar { display: none; }
    .dz-navbar { display: none; }
    .dz-logobar { padding: 8px 0; }
    .dz-logo img { height: 38px; }
    .dz-search-form { height: 40px; }
    .dz-cart-label { display: none; }
}
@media (max-width: 767.98px) {
    .dz-logobar .dz-container { gap: 10px; }
    .dz-search-form button { padding: 0 14px; font-size: 12px; }
}
</style>

{{-- ══ TOP BAR ══ --}}
<div class="dz-topbar d-none d-lg-flex">
    <div class="dz-container">

        {{-- Save More on App --}}
        <div class="dz-top-item">
            <span><i class="fa-solid fa-mobile-screen-button"></i> Save More on App</span>
            <div class="dz-top-popup dz-app-popup">
                <div class="ap-title">Download the App</div>
                @if($generalsetting?->app_qr_code)
                    <div class="ap-qr mb-2"><img src="{{ asset($generalsetting->app_qr_code) }}" alt="QR"></div>
                @else
                    <div class="ap-qr-ph mb-2"><i class="fa-solid fa-qrcode"></i><span>QR Code</span></div>
                @endif
                <p class="ap-tip">Scan to download the app</p>
                <div class="ap-btns">
                    <a href="{{ $generalsetting?->android_app_link ?? '#' }}" class="ap-btn">
                        <i class="fa-brands fa-google-play"></i>
                        <div><span class="ab-sub">Get it on</span><span class="ab-label">Google Play</span></div>
                    </a>
                    <a href="{{ $generalsetting?->ios_app_link ?? '#' }}" class="ap-btn">
                        <i class="fa-brands fa-apple"></i>
                        <div><span class="ab-sub">Download on the</span><span class="ab-label">App Store</span></div>
                    </a>
                </div>
            </div>
        </div>

        {{-- Become a Seller --}}
        <div class="dz-top-item">
            <a href="{{ route('home') }}">Become a Seller</a>
        </div>

        {{-- Help & Support --}}
        <div class="dz-top-item">
            <span><i class="fa-regular fa-circle-question"></i> Help &amp; Support</span>
            <div class="dz-top-popup dz-help-popup">
                <ul>
                    <li><a href="{{ route('contact') }}"><i class="fa-solid fa-headset"></i> Help Center</a></li>
                    <li><a href="{{ route('contact') }}"><i class="fa-solid fa-comment-dots"></i> Contact Support</a></li>
                    <li><a href="{{ route('home') }}"><i class="fa-solid fa-truck-fast"></i> Shipping &amp; Delivery</a></li>
                    <li><a href="{{ route('home') }}"><i class="fa-regular fa-clipboard-list"></i> My Orders</a></li>
                    <li><a href="{{ route('home') }}"><i class="fa-solid fa-wallet"></i> Payment</a></li>
                    <li><a href="{{ route('home') }}"><i class="fa-solid fa-rotate-left"></i> Returns &amp; Refunds</a></li>
                </ul>
            </div>
        </div>

        {{-- Login / Account --}}
        @if(Auth::guard('customer')->check())
            <div class="dz-top-item">
                <span><i class="fa-regular fa-user"></i> {{ Str::limit(Auth::guard('customer')->user()->name, 12) }}</span>
                <div class="dz-top-popup dz-account-popup">
                    <ul>
                        <li><a href="{{ route('customer.account') }}"><i class="fa-regular fa-user"></i> My Account</a></li>
                        <li><a href="{{ route('customer.order') }}"><i class="fa-regular fa-clipboard-list"></i> My Orders</a></li>
                        <li><a href="{{ route('customer.order_track') }}"><i class="fa-solid fa-truck-fast"></i> Track Order</a></li>
                        <li><a href="{{ route('customer.logout') }}"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        @else
            <div class="dz-top-item">
                <a href="{{ route('customer.login') }}">Login</a>
            </div>
            <div class="dz-top-item">
                <a href="{{ route('customer.register') }}">Sign Up</a>
            </div>
        @endif

    </div>
</div>

{{-- ══ LOGO BAR ══ --}}
<div class="dz-logobar">
    <div class="dz-container">

        {{-- Logo --}}
        <div class="dz-logo">
            <a href="{{ route('home') }}">
                <img src="{{ asset($generalsetting?->white_logo) }}" alt="{{ $generalsetting?->name }}">
            </a>
        </div>

        {{-- Search --}}
        <div class="dz-search dz-search-wrap">
            <form id="MainSearch" class="dz-search-form" action="{{ route('search') }}" autocomplete="off">
                <input type="text" name="keyword" placeholder="Search products..."
                    class="search_keyword search_click mainsrc"
                    value="{{ request('keyword') }}">
                @php $searchBtnStyle = $themeCustomization?->search_button_style ?? 'full'; @endphp
                @if($searchBtnStyle === 'icon')
                    <button type="submit" class="dz-search-btn-icon" aria-label="Search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                @elseif($searchBtnStyle === 'compact')
                    <button type="submit" class="dz-search-btn-compact">
                        <i class="fa-solid fa-magnifying-glass"></i> Search
                    </button>
                @else
                    <button type="submit" class="dz-search-btn-full">SEARCH</button>
                @endif
            </form>
            <div class="search_result dz-search-result"></div>
        </div>

        {{-- Cart --}}
        <div class="dz-cart">
            <a href="{{ route('customer.checkout') }}" id="cart-qty">
                <div class="dz-cart-icon">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="dz-cart-count">{{ Cart::instance('shopping')->count() }}</span>
                </div>
                <div class="dz-cart-label d-none d-lg-block">
                    <span>Cart</span>
                    {{ Cart::instance('shopping')->count() }} item(s)
                </div>
            </a>
        </div>

    </div>
</div>

{{-- ══ NAV BAR ══ --}}
<nav class="dz-navbar d-none d-lg-block">
    <div class="dz-container">

        {{-- All Categories Button --}}
        <a class="dz-allcat-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
            <i class="fa-solid fa-bars"></i> All Categories
        </a>

        {{-- Nav Links --}}
        <ul class="dz-nav-links">
            <li>
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i> Home
                </a>
            </li>
            @foreach ($menucategories->take(7) as $cat)
                <li>
                    <a href="{{ url('category/'.$cat->slug) }}">
                        {{ Str::limit($cat->name, 16) }}
                        @if($cat->menusubcategories->count() > 0)
                            <i class="fa-solid fa-angle-down"></i>
                        @endif
                    </a>
                    @if($cat->menusubcategories->count() > 0)
                        <ul class="dz-nav-dropdown">
                            @foreach($cat->menusubcategories as $sub)
                                <li>
                                    <a href="{{ url('subcategory/'.$sub->slug) }}">
                                        {{ Str::limit($sub->subcategoryName, 22) }}
                                        @if($sub->menuchildcategories->count() > 0)
                                            <i class="fa-solid fa-chevron-right"></i>
                                        @endif
                                    </a>
                                    @if($sub->menuchildcategories->count() > 0)
                                        <ul class="dz-nav-child">
                                            @foreach($sub->menuchildcategories as $child)
                                                <li>
                                                    <a href="{{ url('products/'.$child->slug) }}">{{ $child->childcategoryName }}</a>
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

        {{-- Track Order --}}
        <div class="dz-track-link">
            <a href="{{ route('customer.order_track') }}">
                <i class="fa-solid fa-truck-fast"></i> Track Order
            </a>
        </div>

    </div>
</nav>
