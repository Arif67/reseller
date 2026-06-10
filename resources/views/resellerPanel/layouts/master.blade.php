<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Reseller Panel') | {{ $generalsetting?->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="{{ asset($generalsetting?->favicon) }}" />

    <link href="{{ asset('backEnd/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backEnd/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backEnd/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/toastr.min.css') }}" />
    <script src="{{ asset('backEnd/assets/js/head.js') }}"></script>
    <style>
        /* Favourite (heart) button on product cards */
        .rp-fav-btn {
            position: absolute;
            top: 8px;
            left: 8px;
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
            color: #e23744;
            font-size: 18px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 2;
            transition: transform .15s ease, background .15s ease;
        }
        .rp-fav-btn:hover { transform: scale(1.1); }
        .rp-fav-btn.is-fav { background: #e23744; color: #fff; }
        .rp-fav-btn:disabled { opacity: .6; cursor: default; }

        /* Topbar logo — natural size, always visible */
        .navbar-custom .rp-logo-li { margin-left: 6px; }
        .navbar-custom .rp-logo { display: inline-flex; align-items: center; }
        .navbar-custom .rp-logo img {
            height: 32px;
            width: auto;
            max-width: 170px;
            object-fit: contain;
        }

        /* Topbar balance pill */
        .navbar-custom .rp-balance {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #0b5345;
            color: #fff;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            transition: background .15s ease;
        }
        .navbar-custom .rp-balance:hover { background: #0a4439; color: #fff; }
        .navbar-custom .topnav-menu.float-end > li { margin-left: 6px; }

        /* Topbar layout — sob screen-e ek row: left (toggle+logo) | right (balance+cart) */
        .navbar-custom .container-fluid {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 64px;
        }
        .navbar-custom .topnav-menu-left { margin: 0; }
        .navbar-custom .topnav-menu.float-end {
            float: none !important;
            margin-left: auto;
            margin-bottom: 0;
        }

        /* white topbar — toggle icon dark */
        .navbar-custom .button-menu-mobile i,
        .navbar-custom .button-menu-mobile { color: #15384a !important; }

        /* Mobile responsive sidebar (slide-in) */
        @media (max-width: 991.98px) {
            body {
                overflow-x: hidden;
            }

            body::before {
                content: "";
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.44);
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                transition: opacity .24s ease, visibility .24s ease;
                z-index: 1001;
            }

            body.sidebar-enable::before {
                opacity: 1;
                visibility: visible;
                pointer-events: auto;
            }

            .navbar-custom {
                z-index: 1003;
            }

            /* mobile-e jaiga bachate logo ektu choto, username text hide */
            .navbar-custom .rp-logo img { height: 26px; max-width: 120px; }
            .navbar-custom .rp-balance { padding: 5px 9px; font-size: 12px; }
            .navbar-custom .pro-user-name { display: none !important; }

            .left-side-menu {
                width: min(86vw, 300px);
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                transform: translateX(-108%);
                transition: transform .28s ease;
                box-shadow: 0 18px 48px rgba(15, 23, 42, 0.24);
                background: #ffffff !important;
                z-index: 1004 !important;
            }

            body.sidebar-enable .left-side-menu {
                transform: translateX(0);
            }

            /* Menu button JS sidebar ke "condensed" kore dey — mobile e
               oitake full-width e force kori, jate text/icon visible thake */
            body[data-leftbar-size="condensed"] .left-side-menu {
                width: min(86vw, 300px) !important;
            }

            body[data-leftbar-size="condensed"] .left-side-menu #sidebar-menu>ul>li>a {
                text-align: left !important;
                padding: 12px 14px !important;
            }

            body[data-leftbar-size="condensed"] .left-side-menu #sidebar-menu>ul>li>a i,
            body[data-leftbar-size="condensed"] .left-side-menu #sidebar-menu>ul>li>a span,
            body[data-leftbar-size="condensed"] .left-side-menu #sidebar-menu>ul>li>a .badge,
            body[data-leftbar-size="condensed"] .left-side-menu .menu-title {
                display: inline-flex !important;
                visibility: visible !important;
                opacity: 1 !important;
            }

            body[data-leftbar-size="condensed"] .left-side-menu .menu-title {
                display: block !important;
            }

            body[data-leftbar-size="condensed"] .left-side-menu #sidebar-menu>ul>li>a span {
                margin-left: 10px;
            }

            body[data-leftbar-size="condensed"] .content-page {
                margin-left: 0 !important;
            }

            .left-side-menu .h-100[data-simplebar],
            .left-side-menu .simplebar-wrapper,
            .left-side-menu .simplebar-mask,
            .left-side-menu .simplebar-offset,
            .left-side-menu .simplebar-content-wrapper,
            .left-side-menu #sidebar-menu {
                height: 100%;
                background: #ffffff !important;
            }

            .left-side-menu .simplebar-content-wrapper {
                overflow-y: auto !important;
            }

            .content-page {
                margin-left: 0 !important;
            }

            .footer {
                left: 0 !important;
            }
        }
    </style>
    @yield('css')
</head>

<body data-layout-mode="default" data-theme="light" data-layout-width="fluid" data-topbar-color="dark"
    data-menu-position="fixed" data-leftbar-color="light" data-leftbar-size="default" data-sidebar-user="false">

    <div id="wrapper">
        <!-- Topbar Start -->
        <div class="navbar-custom" style="background: #ffffff !important; box-shadow: 0 2px 8px rgba(0,0,0,.06); border-bottom: 1px solid #eee;">
            @php
                $resellerUser  = Auth::guard('reseller')->user();
                $topCartCount  = \App\Models\ResellerCart::where('reseller_id', $resellerUser->id)->count();
                $topBalance    = $resellerUser->balance ?? 0;
            @endphp
            <div class="container-fluid">
                {{-- LEFT: toggle + logo --}}
                <ul class="list-unstyled topnav-menu topnav-menu-left m-0 d-flex align-items-center">
                    <li>
                        <button class="button-menu-mobile waves-effect waves-light">
                            <i class="fe-menu"></i>
                        </button>
                    </li>
                    <li class="rp-logo-li">
                        <a href="{{ route('reseller.dashboard') }}" class="rp-logo">
                            <img src="{{ asset($generalsetting?->white_logo) }}" alt="Logo">
                        </a>
                    </li>
                </ul>

                {{-- RIGHT: balance + cart + user --}}
                <ul class="list-unstyled topnav-menu float-end mb-0 d-flex align-items-center">
                    <li class="topbar-balance align-self-center">
                        <a href="{{ route('reseller.withdraw.index') }}" class="rp-balance" title="Balance">
                            <i class="fe-credit-card"></i>
                            <span class="rp-balance-amt">৳ {{ number_format($topBalance, 0) }}</span>
                        </a>
                    </li>
                    <li class="topbar-cart align-self-center">
                        <a href="{{ route('reseller.cart.index') }}" class="nav-link position-relative">
                            <i class="fe-shopping-cart noti-icon" style="font-size:22px; color:#15384a;"></i>
                            @if($topCartCount)
                                <span class="badge bg-danger rounded-circle position-absolute"
                                    style="top:14px; right:4px; font-size:10px;">{{ $topCartCount }}</span>
                            @endif
                        </a>
                    </li>
                </ul>

                {{-- logout form — sidebar-er Logout menu eta submit kore --}}
                <form id="reseller-logout-form" action="{{ route('reseller.logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
        <!-- Topbar End -->

        <!-- Left Sidebar Start -->
        <div class="left-side-menu">
            <div class="h-100" data-simplebar>
                <div id="sidebar-menu">
                    <ul id="side-menu">
                        <li class="menu-title">Navigation</li>
                        <li>
                            <a href="{{ route('reseller.dashboard') }}">
                                <i class="fe-home"></i><span> Home </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reseller.products.index') }}">
                                <i class="fe-grid"></i><span> All Products </span>
                            </a>
                        </li>
                        @php
                            $rcCount = \App\Models\ResellerCart::where('reseller_id', Auth::guard('reseller')->id())->count();
                            $favCount = \App\Models\ResellerFavourite::where('reseller_id', Auth::guard('reseller')->id())->count();
                        @endphp
                        <li>
                            <a href="{{ route('reseller.favourites.index') }}">
                                <i class="fe-heart"></i><span> Favourites </span>
                                <span class="badge bg-danger float-end" id="rp-fav-count" {{ $favCount ? '' : 'style=display:none' }}>{{ $favCount }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reseller.cart.index') }}">
                                <i class="fe-shopping-cart"></i><span> Cart List </span>
                                @if($rcCount)<span class="badge bg-success float-end">{{ $rcCount }}</span>@endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reseller.orders.index') }}">
                                <i class="fe-shopping-bag"></i><span> My Orders </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reseller.withdraw.index') }}">
                                <i class="fe-dollar-sign"></i><span> Withdraw </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reseller.payment_methods.index') }}">
                                <i class="fe-credit-card"></i><span> Payment Methods </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reseller.tickets.index') }}">
                                <i class="fe-life-buoy"></i><span> Support Tickets </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reseller.profile') }}">
                                <i class="fe-user"></i><span> My Profile </span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);"
                                onclick="document.getElementById('reseller-logout-form').submit();">
                                <i class="fe-log-out"></i><span> Logout </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <!-- Left Sidebar End -->

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12 text-end">
                            © {{ request()->getHost() }} — Reseller Panel
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('backEnd/assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('backEnd/assets/js/app.min.js') }}"></script>
    <script src="{{ asset('backEnd/assets/js/toastr.min.js') }}"></script>
    <script>
        // Mobile: sidebar khola obosthay overlay/baire click korle close
        document.addEventListener('click', function (e) {
            if (!document.body.classList.contains('sidebar-enable')) return;
            var inSidebar = e.target.closest('.left-side-menu');
            var onToggle  = e.target.closest('.button-menu-mobile');
            if (!inSidebar && !onToggle) {
                document.body.classList.remove('sidebar-enable');
            }
        });

        // Favourite (heart) toggle — event delegation, infinite-scroll card-eo kaj kore
        (function () {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const toggleUrl = "{{ route('reseller.favourites.toggle') }}";

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('[data-fav-toggle]');
                if (!btn) return;
                e.preventDefault();
                if (btn.disabled) return;
                btn.disabled = true;

                const body = new URLSearchParams({ product_id: btn.dataset.productId });

                fetch(toggleUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: body.toString(),
                })
                .then(r => r.json())
                .then(data => {
                    const fav = data.favourited;
                    btn.dataset.favourited = fav ? '1' : '0';
                    btn.classList.toggle('is-fav', fav);
                    const icon = btn.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('mdi-heart', fav);
                        icon.classList.toggle('mdi-heart-outline', !fav);
                    }

                    // details page-er bootstrap button color toggle
                    if (btn.classList.contains('btn')) {
                        btn.classList.toggle('btn-danger', fav);
                        btn.classList.toggle('btn-outline-danger', !fav);
                    }

                    // sidebar favourite count update
                    const badge = document.getElementById('rp-fav-count');
                    if (badge) {
                        badge.textContent = data.count;
                        badge.style.display = data.count > 0 ? '' : 'none';
                    }

                    // favourite page-e thakle, remove hole card-ta soriye dei
                    if (!fav && btn.dataset.removeOnUnfav === '1') {
                        const col = btn.closest('.col-lg-3, .col-md-4, .col-sm-6, [data-fav-col]');
                        if (col) col.remove();
                    }
                })
                .catch(() => {})
                .finally(() => { btn.disabled = false; });
            });
        })();
    </script>
    {!! Toastr::message() !!}
    @yield('script')
</body>

</html>
