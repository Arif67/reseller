<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />

    <title>{{ $generalsetting?->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset($generalsetting?->favicon) }}" />

    <!-- Bootstrap css -->
    <link href="{{ asset('backEnd/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App css -->
    <link href="{{ asset('backEnd/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- icons -->
    <link href="{{ asset('backEnd/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- toastr css -->
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/toastr.min.css') }}" />
    <!-- custom css -->
    <link href="{{ asset('backEnd/assets/css/custom.css') }}" rel="stylesheet" type="text/css" />
    @php
        $isModalView = request()->boolean('modal');
    @endphp
    <style>
        body.modal-layout .navbar-custom,
        body.modal-layout .left-side-menu,
        body.modal-layout .footer,
        body.modal-layout .right-bar {
            display: none !important;
        }

        body.modal-layout .content-page {
            margin-left: 0 !important;
            padding: 16px 0 24px;
        }

        body.modal-layout .content-page .content>.container-fluid {
            padding-left: 16px;
            padding-right: 16px;
        }

        body.modal-layout .page-title-box {
            margin-bottom: 12px;
        }

        body.modal-layout .card {
            box-shadow: none;
        }

        @media (max-width: 991.98px) {
            body[data-leftbar-size="condensed"] .logo-box {
                width: auto !important;
            }

            body[data-leftbar-size="condensed"] .left-side-menu {
                width: 272px !important;
            }

            body[data-leftbar-size="condensed"] .left-side-menu #sidebar-menu>ul>li>a {
                text-align: left !important;
                padding: 12px 14px !important;
            }

            body[data-leftbar-size="condensed"] .left-side-menu #sidebar-menu>ul>li>a i,
            body[data-leftbar-size="condensed"] .left-side-menu #sidebar-menu>ul>li>a svg,
            body[data-leftbar-size="condensed"] .left-side-menu #sidebar-menu>ul>li>a span,
            body[data-leftbar-size="condensed"] .left-side-menu #sidebar-menu .menu-arrow,
            body[data-leftbar-size="condensed"] .left-side-menu .nav-second-level,
            body[data-leftbar-size="condensed"] .left-side-menu .nav-second-level li a,
            body[data-leftbar-size="condensed"] .left-side-menu .nav-second-level li a span,
            body[data-leftbar-size="condensed"] .left-side-menu .nav-second-level li a i,
            body[data-leftbar-size="condensed"] .left-side-menu .nav-second-level li a svg {
                display: inline-flex !important;
                visibility: visible !important;
                opacity: 1 !important;
            }

            body[data-leftbar-size="condensed"] .left-side-menu #sidebar-menu>ul>li>a span {
                margin-left: 10px;
            }

            body[data-leftbar-size="condensed"] .left-side-menu #sidebar-menu>ul>li>a .menu-arrow {
                margin-left: auto;
            }

            body[data-leftbar-size="condensed"] .content-page {
                margin-left: 0 !important;
            }

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
            }

            .navbar-custom {
                min-height: 64px;
                background: linear-gradient(90deg, #07111f 0%, #0f172a 100%) !important;
                box-shadow: 0 10px 28px rgba(15, 23, 42, 0.18);
                z-index: 1003;
            }

            .navbar-custom .container-fluid,
            .content-page .content>.container-fluid,
            .footer .container-fluid {
                padding-left: 14px;
                padding-right: 14px;
            }

            .logo-box {
                width: auto !important;
                min-width: 0;
            }

            .logo-box .logo-lg img,
            .logo-box .logo-sm img,
            .navbar-custom .logo img {
                max-height: 38px;
                width: auto;
            }

            .topnav-menu .nav-link {
                padding-left: 10px;
                padding-right: 10px;
                color: #e2e8f0 !important;
            }

            .topnav-menu .noti-icon {
                font-size: 20px;
            }

            .topnav-menu .nav-user img {
                width: 34px;
                height: 34px;
            }

            .left-side-menu {
                width: 272px;
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                transform: translateX(-108%);
                transition: transform .28s ease;
                box-shadow: 0 18px 48px rgba(15, 23, 42, 0.24);
                background: linear-gradient(180deg, #081120 0%, #111c30 100%) !important;
                z-index: 1004;
            }

            body.sidebar-enable .left-side-menu {
                transform: translateX(0);
            }

            .left-side-menu .h-100,
            #sidebar-menu {
                background: transparent !important;
            }

            .user-box {
                padding: 18px 14px;
                background: linear-gradient(180deg, rgba(37, 99, 235, 0.22) 0%, rgba(15, 23, 42, 0) 100%) !important;
                border-bottom: 1px solid rgba(148, 163, 184, 0.18);
            }

            .user-box a,
            .user-box .text-dark,
            .user-box .text-muted,
            .user-box p {
                color: #e2e8f0 !important;
            }

            .user-box .dropdown-menu a,
            .user-box .dropdown-menu span,
            .user-box .dropdown-menu i {
                color: inherit !important;
            }

            .user-box img.avatar-md {
                width: 60px;
                height: 60px;
            }

            #sidebar-menu>ul>li>a {
                padding-top: 12px;
                padding-bottom: 12px;
                color: #e2e8f0 !important;
            }

            #sidebar-menu a,
            #sidebar-menu a span,
            #sidebar-menu a i,
            #sidebar-menu a svg,
            #sidebar-menu li span,
            #sidebar-menu li i {
                color: #e2e8f0 !important;
                stroke: #e2e8f0 !important;
                opacity: 1 !important;
            }

            #sidebar-menu a svg {
                width: 18px;
                height: 18px;
            }

            .content-page {
                margin-left: 0 !important;
                padding: 76px 0 72px;
            }

            .page-title-box {
                margin-bottom: 16px;
            }

            .page-title-box .page-title {
                font-size: 20px;
                line-height: 1.25;
            }

            .footer {
                left: 0 !important;
                padding: 14px 0;
            }

            .footer .text-end {
                text-align: center !important;
                font-size: 13px;
            }

            .right-bar {
                width: 100%;
                right: -100%;
            }
        }

        @media (max-width: 767.98px) {
            body[data-leftbar-size="condensed"] .left-side-menu {
                width: min(86vw, 300px) !important;
            }

            .navbar-custom {
                padding: 0;
            }

            .navbar-custom .container-fluid {
                min-height: 64px;
            }

            .topnav-menu.topnav-menu-left .dropdown.d-none.d-xl-block {
                display: none !important;
            }

            .topnav-menu .pro-user-name {
                display: none !important;
            }

            .topnav-menu.float-end {
                gap: 2px;
            }

            .topnav-menu.float-end>li {
                display: inline-flex;
                align-items: center;
            }

            .button-menu-mobile {
                padding: 0 10px;
            }

            .left-side-menu {
                width: min(86vw, 300px);
            }

            .left-side-menu .h-100[data-simplebar],
            .left-side-menu .simplebar-wrapper,
            .left-side-menu .simplebar-mask,
            .left-side-menu .simplebar-offset,
            .left-side-menu .simplebar-content-wrapper {
                height: 100%;
            }

            .left-side-menu .simplebar-content-wrapper {
                overflow-y: auto !important;
                overflow-x: hidden !important;
            }

            #sidebar-menu {
                padding: 8px 0 18px;
            }

            #sidebar-menu>ul>li {
                margin-bottom: 4px;
            }

            #sidebar-menu>ul>li>a {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 13px 14px;
                border-radius: 14px;
                margin: 0 10px;
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid rgba(148, 163, 184, 0.08);
            }

            #sidebar-menu>ul>li>a .menu-arrow {
                margin-left: auto;
            }

            #sidebar-menu>ul>li>a:hover,
            #sidebar-menu>ul>li.menuitem-active>a,
            #sidebar-menu>ul>li>a[aria-expanded="true"] {
                background: linear-gradient(90deg, rgba(37, 99, 235, 0.24) 0%, rgba(59, 130, 246, 0.12) 100%);
                border-color: rgba(96, 165, 250, 0.28);
                color: #ffffff !important;
            }

            #sidebar-menu>ul>li>a:hover span,
            #sidebar-menu>ul>li>a:hover i,
            #sidebar-menu>ul>li>a:hover svg,
            #sidebar-menu>ul>li.menuitem-active>a span,
            #sidebar-menu>ul>li.menuitem-active>a i,
            #sidebar-menu>ul>li.menuitem-active>a svg,
            #sidebar-menu>ul>li>a[aria-expanded="true"] span,
            #sidebar-menu>ul>li>a[aria-expanded="true"] i,
            #sidebar-menu>ul>li>a[aria-expanded="true"] svg {
                color: #ffffff !important;
                stroke: #ffffff !important;
            }

            #sidebar-menu .collapse,
            #sidebar-menu .collapsing {
                margin: 6px 10px 10px;
                background: rgba(255, 255, 255, 0.04);
                border: 1px solid rgba(148, 163, 184, 0.10);
                border-radius: 14px;
            }

            #sidebar-menu .nav-second-level {
                padding: 8px;
            }

            #sidebar-menu .nav-second-level li+li {
                margin-top: 4px;
            }

            #sidebar-menu .nav-second-level li a {
                display: flex;
                align-items: center;
                gap: 8px;
                white-space: normal;
                word-break: break-word;
                line-height: 1.45;
                padding: 10px 12px;
                border-radius: 12px;
                font-size: 13px;
                color: #cbd5e1 !important;
            }

            #sidebar-menu .nav-second-level li a:hover,
            #sidebar-menu .nav-second-level li a.active {
                background: rgba(37, 99, 235, 0.18);
                color: #ffffff !important;
            }

            #sidebar-menu .nav-second-level li a:hover i,
            #sidebar-menu .nav-second-level li a:hover svg,
            #sidebar-menu .nav-second-level li a.active i,
            #sidebar-menu .nav-second-level li a.active svg {
                color: #ffffff !important;
                stroke: #ffffff !important;
            }

            #sidebar-menu .nav-second-level li a i {
                flex: 0 0 auto;
            }

            .content-page {
                padding-top: 72px;
            }

            .content-page .content>.container-fluid {
                padding-left: 12px;
                padding-right: 12px;
            }

            .page-title-box {
                display: flex;
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            .page-title-box .page-title-right {
                width: 100%;
                margin: 0;
            }

            .page-title-box .page-title-right .btn,
            .page-title-box .page-title-right .btn-group {
                width: 100%;
            }

            .card,
            .modal-content {
                border-radius: 16px;
            }

            .table-responsive-sm {
                border: 0;
            }
        }
    </style>
    <!-- Head js -->
    @yield('css')
    <script src="{{ asset('backEnd/assets/js/head.js') }}"></script>
</head>

<!-- body start -->

<body data-layout-mode="default" data-theme="light" data-layout-width="fluid" data-topbar-color="dark"
    data-menu-position="fixed" data-leftbar-color="light" data-leftbar-size="default" data-sidebar-user="false"
    class="{{ $isModalView ? 'modal-layout' : '' }}">
    <!-- Begin page -->
    <div id="wrapper">
        <!-- Topbar Start -->
        @if (!$isModalView)
            <div class="navbar-custom" style="background: black !important;">

                <div class="container-fluid">
                    <ul class="list-unstyled topnav-menu float-end mb-0">
                        <li class="dropdown d-inline-block d-lg-none">
                            <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light"
                                data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                aria-expanded="false">
                                <i class="fe-search noti-icon"></i>
                            </a>
                            <div class="dropdown-menu dropdown-lg dropdown-menu-end p-0">
                                <form class="p-3">
                                    <input type="text" class="form-control" placeholder="Search ..."
                                        aria-label="Recipient's username" />
                                </form>
                            </div>
                        </li>

                        <li class="dropdown d-none d-lg-inline-block">
                            <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light"
                                data-toggle="fullscreen" href="#">
                                <i class="fe-maximize noti-icon"></i>
                            </a>
                        </li>

                        <li class="dropdown notification-list topbar-dropdown">
                            <a class="nav-link dropdown-toggle waves-effect waves-light" data-bs-toggle="dropdown"
                                href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <i class="fe-bell noti-icon"></i>
                                <span class="badge bg-danger rounded-circle noti-icon-badge">{{ $neworder }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-lg">
                                <!-- item-->
                                <div class="dropdown-item noti-title">
                                    <h5 class="m-0">
                                        <span class="float-end">
                                            <a href="{{ route('admin.orders', ['slug' => 'pending']) }}"
                                                class="text-dark">
                                                <small>View All</small>
                                            </a>
                                        </span>
                                        Orders
                                    </h5>
                                </div>

                                <div class="noti-scroll" data-simplebar>
                                    @foreach ($pendingorder as $porder)
                                        <!-- item-->
                                        <a href="{{ route('admin.orders', ['slug' => 'pending']) }}"
                                            class="dropdown-item notify-item active">
                                            <div class="notify-icon">
                                                <img src="{{ asset($porder->customer ? $porder->customer->image : '') }}"
                                                    class="img-fluid rounded-circle" alt="" />
                                            </div>
                                            <p class="notify-details">
                                                {{ $porder->customer ? $porder->customer->name : '' }}</p>
                                            <p class="text-muted mb-0 user-msg">
                                                <small>Invoice : {{ $porder->invoice_id }}</small>
                                            </p>
                                        </a>
                                    @endforeach

                                    <!-- item-->
                                </div>

                                <!-- All-->
                                <a href="{{ route('admin.orders', ['slug' => 'pending']) }}"
                                    class="dropdown-item text-center text-primary notify-item notify-all">
                                    View all
                                    <i class="fe-arrow-right"></i>
                                </a>
                            </div>
                        </li>

                        <li class="dropdown notification-list topbar-dropdown">
                            <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light"
                                data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                aria-expanded="false">
                                <img src="{{ asset(Auth::user()->image) }}" alt="user-image"
                                    class="rounded-circle" />
                                <span class="pro-user-name ms-1"> {{ Auth::user()->name }} <i
                                        class="mdi mdi-chevron-down"></i> </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                                <!-- item-->
                                <div class="dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Welcome !</h6>
                                </div>

                                <!-- item-->
                                <a href="{{ route('dashboard') }}" class="dropdown-item notify-item">
                                    <i class="fe-user"></i>
                                    <span>Dashboard</span>
                                </a>

                                <!-- item-->
                                <a href="{{ route('change_password') }}" class="dropdown-item notify-item">
                                    <i class="fe-settings"></i>
                                    <span>Change Password</span>
                                </a>

                                <!-- item-->
                                <a href="{{ route('locked') }}" class="dropdown-item notify-item">
                                    <i class="fe-lock"></i>
                                    <span>Lock Screen</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                <!-- item-->
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                  document.getElementById('logout-form').submit();"
                                    class="dropdown-item notify-item">
                                    <i class="fe-log-out me-1"></i>
                                    <span>Logout</span>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>

                        <!--<li class="dropdown notification-list">-->
                        <!--    <a href="javascript:void(0);" class="nav-link right-bar-toggle waves-effect waves-light">-->
                        <!--        <i class="fe-settings noti-icon"></i>-->
                        <!--    </a>-->
                        <!--</li>-->
                    </ul>

                    <!-- LOGO -->
                    <div class="logo-box">
                        <a href="{{ url('admin/dashboard') }}" class="logo logo-dark text-center">
                            <span class="logo-sm">
                                <img src="{{ asset($generalsetting?->white_logo) }}" alt=""
                                    height="50" />
                                <!-- <span class="logo-lg-text-light">UBold</span> -->
                            </span>
                            <span class="logo-lg">
                                <img src="{{ asset($generalsetting?->dark_logo) }}" alt="" height="50" />
                                <!-- <span class="logo-lg-text-light">U</span> -->
                            </span>
                        </a>

                        <a href="{{ url('admin/dashboard') }}" class="logo logo-light text-center">
                            <span class="logo-sm">
                                <img src="{{ asset($generalsetting?->white_logo) }}" alt=""
                                    height="50" />
                            </span>
                            <span class="logo-lg">
                                <img src="{{ asset($generalsetting?->white_logo) }}" alt=""
                                    height="50" />
                            </span>
                        </a>
                    </div>

                    <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
                        <li>
                            <button class="button-menu-mobile waves-effect waves-light">
                                <i class="fe-menu"></i>
                            </button>
                        </li>

                        <li>
                            <!-- Mobile menu toggle (Horizontal Layout)-->
                            <a class="navbar-toggle nav-link" data-bs-toggle="collapse"
                                data-bs-target="#topnav-menu-content">
                                <div class="lines">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </a>
                            <!-- End mobile menu toggle-->
                        </li>

                        <li class="dropdown d-none d-xl-block">
                            <a class="nav-link dropdown-toggle waves-effect waves-light" href="{{ route('home') }}"
                                target="_blank"> <i data-feather="globe"></i> Visit Site </a>
                        </li>
                        <li class="dropdown d-none d-xl-block">
                            <a class="nav-link dropdown-toggle waves-effect waves-light" href="{{ url('/cc') }}">
                                <i data-feather="refresh-cw"></i> Clear Cache </a>
                        </li>
                        <li class="dropdown d-none d-xl-block">
                            <a class="nav-link dropdown-toggle waves-effect waves-light"
                                href="{{ url('/optimize-clear') }}"> <i data-feather="refresh-ccw"></i> Optimize
                                Clear </a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
            </div>
        @endif
        <!-- end Topbar -->

        <!-- ========== Left Sidebar Start ========== -->
        @if (!$isModalView)
            <div class="left-side-menu" style="background: black;">
                <div class="h-100" data-simplebar>
                    <!-- User box -->
                    <div class="user-box text-center" style="background: green;">
                        <img src="{{ asset('public/backEnd/') }}/assets/images/users/user-1.jpg" alt="user-img"
                            title="Mat Helme" class="rounded-circle avatar-md" />
                        <div class="dropdown">
                            <a href="javascript: void(0);" class="text-dark dropdown-toggle h5 mt-2 mb-1 d-block"
                                data-bs-toggle="dropdown">{{ Auth::user()->name }}</a>
                            <div class="dropdown-menu user-pro-dropdown">
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="fe-user me-1"></i>
                                    <span>My Account</span>
                                </a>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="fe-settings me-1"></i>
                                    <span>Settings</span>
                                </a>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="fe-lock me-1"></i>
                                    <span>Lock Screen</span>
                                </a>

                                <!-- item-->
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"
                                    class="dropdown-item notify-item">
                                    <i class="fe-log-out me-1"></i>
                                    <span>Logout</span>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                        <p class="text-muted">Admin Head</p>
                    </div>

                    <!--- Sidemenu -->
                    <div id="sidebar-menu" style="background: black;">
                        <ul id="side-menu">
                            <li>
                                <a href="{{ route('dashboard') }}">
                                    <i data-feather="airplay"></i>
                                    <span class="text-white"> Dashboard </span>
                                </a>
                            </li>

                            <li>
                                <a class="text-white" href="{{ route('admin.pos') }}">
                                    <i data-feather="shopping-bag"></i>
                                    <span> POS </span>
                                </a>
                            </li>

                            <li>
                                <a class="text-white" href="#sidebar-orders" data-bs-toggle="collapse">
                                    <i data-feather="shopping-cart"></i>
                                    <span> Orders </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebar-orders">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a class="text-white"
                                                href="{{ route('admin.orders', ['slug' => 'all']) }}"><i
                                                    data-feather="file-plus"></i> All Order</a>
                                        </li>
                                        @foreach ($orderstatus as $value)
                                            <li>
                                                <a class="text-white"
                                                    href="{{ route('admin.orders', ['slug' => $value->slug]) }}"><i
                                                        data-feather="file-plus"></i>{{ $value->name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                            <!-- nav items -->
                            <li>
                                <a href="#siebar-product" data-bs-toggle="collapse">
                                    <i data-feather="database"></i>
                                    <span class="text-white"> Products </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="siebar-product">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a class="text-white" href="{{ route('products.index') }}"><i
                                                    data-feather="file-plus"></i> Product Manage</a>
                                        </li>

                                        <li>
                                            <a class="text-white" href="{{ route('attribute.index') }}"><i
                                                    data-feather="sliders"></i> Attributes</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('value.index') }}"><i
                                                    data-feather="list"></i> Attribute Values</a>
                                        </li>

                                        <li>
                                            <a class="text-white" href="{{ route('products.price_edit') }}"><i
                                                    data-feather="file-plus"></i> Price Edit</a>
                                        </li>

                                        <li>
                                            <a class="text-white" href="{{ route('media.index') }}"><i
                                                    data-feather="image"></i> Media Library</a>
                                        </li>

                                        <li>
                                            <a class="text-white" href="{{ route('couponcodes.index') }}"><i
                                                    data-feather="file-plus"></i> Coupon Code</a>
                                        </li>

                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a class="text-white" href="{{ route('categories.index') }}"><i
                                        data-feather="layers"></i> Categories</a>
                            </li>
                            <li>
                                <a class="text-white" href="{{ route('subcategories.index') }}"><i
                                        data-feather="grid"></i> Subcategories</a>
                            </li>
                            <li>
                                <a class="text-white" href="{{ route('childcategories.index') }}"><i
                                        data-feather="list"></i> Childcategories</a>
                            </li>
                            <li>
                                <a class="text-white" href="{{ route('brands.index') }}"><i data-feather="tag"></i>
                                    Brands</a>
                            </li>

                            <!-- nav items end -->
                            @php
                                $pending_reviews = \App\Models\Review::where('status', 'pending')->count();
                            @endphp
                            <li>
                                <a href="#sidebar-product-review" data-bs-toggle="collapse">
                                    <i data-feather="star"></i>
                                    <span class="text-white"> Reviews </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebar-product-review">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a class="text-white" href="{{ route('reviews.pending') }}"><i
                                                    data-feather="file-plus"></i> Pending Reviews
                                                ({{ $pending_reviews }})</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('reviews.pending') }}"><i
                                                    data-feather="file-plus"></i> Create</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('reviews.index') }}"><i
                                                    data-feather="file-plus"></i> All Reviews</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <!-- nav items end -->
                            <li>
                                <a href="#sidebar-landing-page" data-bs-toggle="collapse">
                                    <i data-feather="airplay"></i>
                                    <span class="text-white"> Landing Page </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebar-landing-page">
                                    <ul class="nav-second-level">

                                        <li>
                                            <a class="text-white" href="{{ route('campaign.create') }}"><i
                                                    data-feather="file-plus"></i> Create</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('campaign.index') }}"><i
                                                    data-feather="file-plus"></i> Campaign</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <!-- nav items end -->

                            <li>
                                <a href="#sidebar-users" data-bs-toggle="collapse">
                                    <i data-feather="user"></i>
                                    <span class="text-white"> Users </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebar-users">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a class="text-white" href="{{ route('users.index') }}"><i
                                                    data-feather="file-plus"></i> User</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('roles.index') }}"><i
                                                    data-feather="file-plus"></i> Roles</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('permissions.index') }}"><i
                                                    data-feather="file-plus"></i> Permissions</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('customers.index') }}"><i
                                                    data-feather="file-plus"></i> Customers</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="#sidebar-theme-setting" data-bs-toggle="collapse">
                                    <i data-feather="user"></i>
                                    <span class="text-white"> Theme Customization </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebar-theme-setting">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a class="text-white" href="{{ route('header.index') }}"><i
                                                    data-feather="file-plus"></i> Top Header </a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('theme.customization.index') }}"><i
                                                    data-feather="sliders"></i> Components </a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('banner_category.index') }}"><i
                                                    data-feather="file-plus"></i> Banner Category</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('banners.index') }}"><i
                                                    data-feather="image"></i> Marketing Banner</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('promo_strip.index') }}"><i
                                                    data-feather="zap"></i> Promo Strip</a>
                                        </li>

                                    </ul>
                                </div>
                            </li>
                            <!-- nav items -->
                            <li>
                                <a href="#siebar-sitesetting" data-bs-toggle="collapse">
                                    <i data-feather="settings"></i>
                                    <span class="text-white"> App Setting </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="siebar-sitesetting">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a class="text-white" href="{{ route('settings.index') }}"><i
                                                    data-feather="file-plus"></i> General Setting</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('socialmedias.index') }}"><i
                                                    data-feather="file-plus"></i> Social Media</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('contact.index') }}"><i
                                                    data-feather="file-plus"></i> Contact</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('pages.index') }}"><i
                                                    data-feather="file-plus"></i> Create Page</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('shippingcharges.index') }}"><i
                                                    data-feather="file-plus"></i> Shipping Charge</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('orderstatus.index') }}"><i
                                                    data-feather="file-plus"></i> Order Status</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ url('/cc') }}"><i
                                                    data-feather="refresh-cw"></i> Clear Cache</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ url('/optimize-clear') }}"><i
                                                    data-feather="refresh-ccw"></i> Optimize Clear</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <!-- nav items end -->
                            <li>
                                <a href="#sidebar-api-integration" data-bs-toggle="collapse">
                                    <i data-feather="save"></i>
                                    <span class="text-white"> API Integration </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebar-api-integration">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a class="text-white" href="{{ route('paymentgeteway.manage') }}"><i
                                                    data-feather="file-plus"></i> Payment Gateway</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('smsgeteway.manage') }}"><i
                                                    data-feather="file-plus"></i> SMS Gateway</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('courierapi.manage') }}"><i
                                                    data-feather="file-plus"></i> Courier API</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <!-- nav items end -->
                            <li>
                                <a href="#sidebar-pixel-gtm" data-bs-toggle="collapse">
                                    <i data-feather="save"></i>
                                    <span class="text-white"> Marketing Tools </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebar-pixel-gtm">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a class="text-white" href="{{ route('marketing.tools.index') }}"><i
                                                    data-feather="activity"></i> Marketing Suite</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('tagmanagers.index') }}"><i
                                                    data-feather="file-plus"></i> Google Tag Manager</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('pixels.index') }}"><i
                                                    data-feather="file-plus"></i> Pixel Configuration</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('seo.config.index') }}"><i
                                                    data-feather="search"></i> SEO Configuration</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <!-- nav items end -->
                            <!-- nav items end -->

                            <li>
                                <a href="#sitebar-report" data-bs-toggle="collapse">
                                    <i data-feather="pie-chart"></i>
                                    <span class="text-white"> Reports </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sitebar-report">
                                    <ul class="nav-second-level">
                                        <li>
                                            <a class="text-white"
                                                href="{{ route('reports.conversion_dashboard') }}"><i
                                                    data-feather="activity"></i> Conversion Dashboard</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('reports.visitor_analytics') }}"><i
                                                    data-feather="map"></i> Visitor Analytics</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('reports.incomplete_orders') }}"><i
                                                    data-feather="shopping-cart"></i> Incomplete Orders</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('reports.utm_campaigns') }}"><i
                                                    data-feather="crosshair"></i> UTM Campaign Report</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('admin.stock_report') }}"><i
                                                    data-feather="file-plus"></i> Stock Report</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('customers.ip_block') }}"><i
                                                    data-feather="file-plus"></i> IP Block</a>
                                        </li>
                                        <li>
                                            <a class="text-white" href="{{ route('admin.order_report') }}"><i
                                                    data-feather="file-plus"></i> Order Reports</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <!--
            <li>
              <a href="#sidebar-accounts" data-bs-toggle="collapse">
                <i data-feather="briefcase"></i>
                <span class="text-white"> Accounts </span>
                <span class="menu-arrow"></span>
              </a>
              <div class="collapse" id="sidebar-accounts">
                <ul class="nav-second-level">
                  <li>
                    <a class="text-white" href="{{ route('admin.loss_profit') }}"><i data-feather="dollar-sign"></i> Profit & Loss</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('accounts.heads.index') }}"><i data-feather="book-open"></i> Chart of Accounts</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('accounts.financial_accounts.index') }}"><i data-feather="briefcase"></i> Cash / Bank Accounts</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('accounts.supplier_master.index') }}"><i data-feather="truck"></i> Supplier Master</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('accounts.income_categories.index') }}"><i data-feather="tag"></i> Income Categories</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('accounts.income.index') }}"><i data-feather="trending-up"></i> Income Ledger</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('accounts.journal_entries.index') }}"><i data-feather="edit-3"></i> Journal Entry</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('accounts.ledger') }}"><i data-feather="list"></i> Account Ledger</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('accounts.daily_closing') }}"><i data-feather="calendar"></i> Daily Closing</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('accounts.purchase_report') }}"><i data-feather="bar-chart"></i> Purchase Report</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('accounts.suppliers.index') }}"><i data-feather="truck"></i> Supplier Ledger</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('accounts.customer_dues.index') }}"><i data-feather="users"></i> Customer Due Ledger</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('accounts.fund_transfers.index') }}"><i data-feather="repeat"></i> Fund Transfer</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('accounts.return_refunds.index') }}"><i data-feather="corner-up-left"></i> Return / Refund Ledger</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('accounts.payment_method_report') }}"><i data-feather="pie-chart"></i> Payment Method Report</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('admin.zero_cost_audit') }}"><i data-feather="alert-triangle"></i> Zero Cost Audit</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('expensecategories.index') }}"><i data-feather="folder"></i> Expense Categories</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('expenses.index') }}"><i data-feather="credit-card"></i> Expenses</a>
                  </li>
                </ul>
              </div>
            </li>
            <li>
              <a href="#sidebar-inventory" data-bs-toggle="collapse">
                <i data-feather="cpu"></i>
                <span class="text-white"> Inventory </span>
                <span class="menu-arrow"></span>
              </a>
              <div class="collapse" id="sidebar-inventory">
                <ul class="nav-second-level">
                  <li>
                    <a class="text-white" href="{{ route('inventory.dashboard') }}"><i data-feather="monitor"></i> Dashboard</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('inventory.ledger') }}"><i data-feather="archive"></i> Inventory Ledger</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('inventory.adjustments.index') }}"><i data-feather="sliders"></i> Stock Adjustment</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('inventory.low_stock') }}"><i data-feather="alert-octagon"></i> Low Stock Report</a>
                  </li>
                </ul>
              </div>
            </li>
            <li>
              <a href="#sidebar-hr" data-bs-toggle="collapse">
                <i data-feather="users"></i>
                <span class="text-white"> HR Module </span>
                <span class="menu-arrow"></span>
              </a>
              <div class="collapse" id="sidebar-hr">
                <ul class="nav-second-level">
                  <li>
                    <a class="text-white" href="{{ route('hr.dashboard') }}"><i data-feather="home"></i> HR Dashboard</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.departments.index') }}"><i data-feather="grid"></i> Departments</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.designations.index') }}"><i data-feather="award"></i> Designations</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.employees.index') }}"><i data-feather="user"></i> Employees</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.shifts.index') }}"><i data-feather="clock"></i> Shifts</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.holidays.index') }}"><i data-feather="sun"></i> Holidays</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.leave_types.index') }}"><i data-feather="bookmark"></i> Leave Types</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.attendance.index') }}"><i data-feather="check-square"></i> Attendance</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.attendance.dashboard') }}"><i data-feather="bar-chart-2"></i> Attendance Dashboard</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.leave.index') }}"><i data-feather="calendar"></i> Leave</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.leave.balance') }}"><i data-feather="layers"></i> Leave Balance</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.payroll.index') }}"><i data-feather="credit-card"></i> Payroll</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.salary_structures.index') }}"><i data-feather="file-text"></i> Salary Structure</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.advances.index') }}"><i data-feather="dollar-sign"></i> Advance / Loan</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.bonuses.index') }}"><i data-feather="gift"></i> Bonus / Incentive</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.documents.index') }}"><i data-feather="folder"></i> Documents</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.notices.index') }}"><i data-feather="bell"></i> Notices</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.separations.index') }}"><i data-feather="log-out"></i> Separation</a>
                  </li>
                  <li>
                    <a class="text-white" href="{{ route('hr.performance_notes.index') }}"><i data-feather="activity"></i> Performance Notes</a>
                  </li>
                </ul>
              </div>
            </li>
            nav items end -->
                        </ul>
                    </div>
                    <!-- End Sidebar -->

                    <div class="clearfix"></div>
                </div>
                <!-- Sidebar -left -->
            </div>
        @endif
        <!-- Left Sidebar End -->

        <div class="content-page">
            <div class="content">
                @yield('content')
            </div>
            <!-- content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12 text-end">
                            © {{ request()->getHost() }} / All rights reserved. Developed by CodexLab BD.
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->
        </div>
    </div>
    <!-- END wrapper -->

    <!-- Right Sidebar -->
    <div class="right-bar">
        <div data-simplebar class="h-100">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs nav-bordered nav-justified" role="tablist">
                <li class="nav-item">
                    <a class="nav-link py-2" data-bs-toggle="tab" href="#chat-tab" role="tab">
                        <i class="mdi mdi-message-text d-block font-22 my-1"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2" data-bs-toggle="tab" href="#tasks-tab" role="tab">
                        <i class="mdi mdi-format-list-checkbox d-block font-22 my-1"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 active" data-bs-toggle="tab" href="#settings-tab" role="tab">
                        <i class="mdi mdi-cog-outline d-block font-22 my-1"></i>
                    </a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content pt-0">
                <div class="tab-pane" id="chat-tab" role="tabpanel">
                    <form class="search-bar p-3">
                        <div class="position-relative">
                            <input type="text" class="form-control" placeholder="Search..." />
                            <span class="mdi mdi-magnify"></span>
                        </div>
                    </form>
                </div>

                <div class="tab-pane" id="tasks-tab" role="tabpanel">
                    <h6 class="fw-medium p-3 m-0 text-uppercase">Working Tasks</h6>
                </div>
                <div class="tab-pane active" id="settings-tab" role="tabpanel">
                    <h6 class="fw-medium px-3 m-0 py-2 font-13 text-uppercase bg-light">
                        <span class="d-block py-1">Theme Customization</span>
                    </h6>

                    <div class="p-3">
                        <div class="alert alert-warning" role="alert"><strong>Customize </strong> the overall color
                            scheme, sidebar menu, etc.</div>

                        <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Color Scheme</h6>
                        <div class="form-check form-switch mb-1">
                            <input type="checkbox" class="form-check-input" name="layout-color" value="light"
                                id="light-mode-check" checked />
                            <label class="form-check-label" for="light-mode-check">Light Mode</label>
                        </div>

                        <div class="form-check form-switch mb-1">
                            <input type="checkbox" class="form-check-input" name="layout-color" value="dark"
                                id="dark-mode-check" />
                            <label class="form-check-label" for="dark-mode-check">Dark Mode</label>
                        </div>

                        <!-- Width -->
                        <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Width</h6>
                        <div class="form-check form-switch mb-1">
                            <input type="checkbox" class="form-check-input" name="layout-width" value="fluid"
                                id="fluid-check" checked />
                            <label class="form-check-label" for="fluid-check">Fluid</label>
                        </div>
                        <div class="form-check form-switch mb-1">
                            <input type="checkbox" class="form-check-input" name="layout-width" value="boxed"
                                id="boxed-check" />
                            <label class="form-check-label" for="boxed-check">Boxed</label>
                        </div>

                        <!-- Menu positions -->
                        <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Menus (Leftsidebar and Topbar) Positon</h6>

                        <div class="form-check form-switch mb-1">
                            <input type="checkbox" class="form-check-input" name="menu-position" value="fixed"
                                id="fixed-check" checked />
                            <label class="form-check-label" for="fixed-check">Fixed</label>
                        </div>

                        <div class="form-check form-switch mb-1">
                            <input type="checkbox" class="form-check-input" name="menu-position" value="scrollable"
                                id="scrollable-check" />
                            <label class="form-check-label" for="scrollable-check">Scrollable</label>
                        </div>

                        <!-- Left Sidebar-->
                        <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Left Sidebar Color</h6>

                        <div class="form-check form-switch mb-1">
                            <input type="checkbox" class="form-check-input" name="leftbar-color" value="light"
                                id="light-check" />
                            <label class="form-check-label" for="light-check">Light</label>
                        </div>

                        <div class="form-check form-switch mb-1">
                            <input type="checkbox" class="form-check-input" name="leftbar-color" value="dark"
                                id="dark-check" checked />
                            <label class="form-check-label" for="dark-check">Dark</label>
                        </div>

                        <div class="form-check form-switch mb-1">
                            <input type="checkbox" class="form-check-input" name="leftbar-color" value="brand"
                                id="brand-check" />
                            <label class="form-check-label" for="brand-check">Brand</label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" class="form-check-input" name="leftbar-color" value="gradient"
                                id="gradient-check" />
                            <label class="form-check-label" for="gradient-check">Gradient</label>
                        </div>

                        <!-- size -->
                        <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Left Sidebar Size</h6>

                        <div class="form-check form-switch mb-1">
                            <input type="checkbox" class="form-check-input" name="leftbar-size" value="default"
                                id="default-size-check" checked />
                            <label class="form-check-label" for="default-size-check">Default</label>
                        </div>

                        <div class="form-check form-switch mb-1">
                            <input type="checkbox" class="form-check-input" name="leftbar-size" value="condensed"
                                id="condensed-check" />
                            <label class="form-check-label" for="condensed-check">Condensed <small>(Extra Small
                                    size)</small></label>
                        </div>

                        <div class="form-check form-switch mb-1">
                            <input type="checkbox" class="form-check-input" name="leftbar-size" value="compact"
                                id="compact-check" />
                            <label class="form-check-label" for="compact-check">Compact <small>(Small
                                    size)</small></label>
                        </div>

                        <!-- User info -->
                        <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Sidebar User Info</h6>

                        <div class="form-check form-switch mb-1">
                            <input type="checkbox" class="form-check-input" name="sidebar-user" value="fixed"
                                id="sidebaruser-check" />
                            <label class="form-check-label" for="sidebaruser-check">Enable</label>
                        </div>

                        <!-- Topbar -->
                        <h6 class="fw-medium font-14 mt-4 mb-2 pb-1">Topbar</h6>

                        <div class="form-check form-switch mb-1">
                            <input type="checkbox" class="form-check-input" name="topbar-color" value="dark"
                                id="darktopbar-check" checked />
                            <label class="form-check-label" for="darktopbar-check">Dark</label>
                        </div>

                        <div class="form-check form-switch mb-1">
                            <input type="checkbox" class="form-check-input" name="topbar-color" value="light"
                                id="lighttopbar-check" />
                            <label class="form-check-label" for="lighttopbar-check">Light</label>
                        </div>

                        <div class="d-grid mt-4">
                            <button class="btn btn-primary" id="resetBtn">Reset to Default</button>
                            <a href="https://1.envato.market/uboldadmin" class="btn btn-danger mt-3"
                                target="_blank"><i class="mdi mdi-basket me-1"></i> Purchase Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end slimscroll-menu-->
    </div>
    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- Vendor js -->
    <script src="{{ asset('backEnd/assets/js/vendor.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('backEnd/assets/js/app.min.js') }}"></script>
    <script src="{{ asset('backEnd/assets/js/toastr.min.js') }}"></script>
    {!! Toastr::message() !!}
    <script src="{{ asset('backEnd/assets/js/sweetalert.min.js') }}"></script>
    <script type="text/javascript">
        $(".delete-confirm").click(function(event) {
            var form = $(this).closest("form");
            event.preventDefault();
            swal({
                title: `Are you sure you want to delete this record?`,
                text: "If you delete this, it will be gone forever.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    form.submit();
                }
            });
        });
        $(".change-confirm").click(function(event) {
            var form = $(this).closest("form");
            event.preventDefault();
            swal({
                title: `Are you sure you want to change this record?`,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    form.submit();
                }
            });
        });
    </script>
    <!--patho courier-->
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change', '.pathaocity', function() {
                var id = $(this).val();
                var form = $(this).closest('form');
                var zone_select = form.find('.pathaozone');
                var area_select = form.find('.pathaoarea');

                if (id) {
                    $.ajax({
                        type: "GET",
                        url: "{{ url('admin/pathao-city') }}?city_id=" + id,
                        success: function(res) {
                            if (res && res.data && res.data.data) {
                                zone_select.empty();
                                zone_select.append('<option value="">Select..</option>');
                                $.each(res.data.data, function(index, zone) {
                                    zone_select.append('<option value="' + zone
                                        .zone_id + '">' + zone.zone_name +
                                        '</option>');
                                });
                                zone_select.trigger("chosen:updated");
                            } else {
                                area_select.empty();
                                zone_select.empty();
                            }
                        }
                    });
                } else {
                    area_select.empty();
                    zone_select.empty();
                }
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change', '.pathaozone', function() {
                var id = $(this).val();
                var form = $(this).closest('form');
                var area_select = form.find('.pathaoarea');

                if (id) {
                    $.ajax({
                        type: "GET",
                        url: "{{ url('admin/pathao-zone') }}?zone_id=" + id,
                        success: function(res) {
                            if (res && res.data && res.data.data) {
                                area_select.empty();
                                area_select.append('<option value="">Select..</option>');
                                $.each(res.data.data, function(index, area) {
                                    area_select.append('<option value="' + area
                                        .area_id + '">' + area.area_name +
                                        '</option>');
                                });
                                area_select.trigger("chosen:updated");
                            } else {
                                area_select.empty();
                            }
                        }
                    });
                } else {
                    area_select.empty();
                }
            });
        });
    </script>
    <script>
        $(".search_click").on("keyup change", function() {
            var keyword = $(this).val();
            $.ajax({
                type: "GET",
                data: {
                    keyword: keyword
                },
                url: "{{ route('admin.livesearch') }}",
                success: function(products) {
                    if (products) {
                        $(".search_result").html(products);
                    } else {
                        $(".search_result").empty();
                    }
                },
            });
        });
    </script>
    @yield('script')
</body>

</html>
