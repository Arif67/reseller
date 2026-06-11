<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Vendor Panel') | {{ $generalsetting?->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="{{ asset($generalsetting?->favicon) }}" />

    <link href="{{ asset('backEnd/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backEnd/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backEnd/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/toastr.min.css') }}" />
    <script src="{{ asset('backEnd/assets/js/head.js') }}"></script>
    @yield('css')
</head>

<body data-layout-mode="default" data-theme="light" data-layout-width="fluid" data-topbar-color="dark"
    data-menu-position="fixed" data-leftbar-color="light" data-leftbar-size="default" data-sidebar-user="false">

    <div id="wrapper">
        <!-- Topbar Start -->
        <div class="navbar-custom" style="background: black !important;">
            <div class="container-fluid">
                <ul class="list-unstyled topnav-menu float-end mb-0">
                    <li class="dropdown notification-list topbar-dropdown">
                        <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown"
                            href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <img src="{{ asset(Auth::guard('vendor')->user()->image) }}" alt="vendor"
                                class="rounded-circle" />
                            <span class="pro-user-name ms-1 text-white">
                                {{ Auth::guard('vendor')->user()->shop_name }} <i class="mdi mdi-chevron-down"></i>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                            <a href="{{ route('vendor.profile') }}" class="dropdown-item notify-item">
                                <i class="fe-user"></i><span>My Profile</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item notify-item"
                                onclick="document.getElementById('vendor-logout-form').submit();">
                                <i class="fe-log-out"></i><span>Logout</span>
                            </a>
                            <form id="vendor-logout-form" action="{{ route('vendor.logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>

                <!-- LOGO -->
                <div class="logo-box">
                    <a href="{{ route('vendor.dashboard') }}" class="logo logo-light text-center">
                        <span class="logo-lg">
                            <img src="{{ asset($generalsetting?->white_logo) }}" alt="" height="22" />
                        </span>
                    </a>
                </div>

                <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
                    <li>
                        <button class="button-menu-mobile waves-effect waves-light">
                            <i class="fe-menu"></i>
                        </button>
                    </li>
                    <li class="d-none d-md-inline-block">
                        <h4 class="page-title-main text-white mt-3">Vendor Panel</h4>
                    </li>
                </ul>
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
                            <a href="{{ route('vendor.dashboard') }}">
                                <i class="fe-home"></i><span> Dashboard </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('vendor.products.index') }}">
                                <i class="fe-package"></i><span> My Products </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('vendor.orders.index', 'all') }}">
                                <i class="fe-shopping-cart"></i><span> Orders </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('vendor.collection') }}">
                                <i class="fe-shopping-bag"></i><span> Collection </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('vendor.pending_summary') }}">
                                <i class="fe-refresh-cw"></i><span> Pending Summary </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('vendor.collected') }}">
                                <i class="fe-check-square"></i><span> Collected </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('vendor.returns') }}">
                                <i class="fe-corner-down-left"></i><span> Returns </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('vendor.payment') }}">
                                <i class="fe-credit-card"></i><span> Payment </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('vendor.profile') }}">
                                <i class="fe-user"></i><span> My Shop </span>
                            </a>
                        </li>
                        {{-- Phase 3 e Earnings menu add hobe --}}
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
                            © {{ request()->getHost() }} — Vendor Panel
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('backEnd/assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('backEnd/assets/js/app.min.js') }}"></script>
    <script src="{{ asset('backEnd/assets/js/toastr.min.js') }}"></script>
    {!! Toastr::message() !!}
    @yield('script')
</body>

</html>
