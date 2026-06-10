@extends('resellerPanel.layouts.master')
@section('title', 'Home')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box"><h4 class="page-title">Home</h4></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end"><i class="mdi mdi-cart-outline widget-icon"></i></div>
                    <h5 class="text-muted fw-normal mt-0">Total Orders</h5>
                    <h3 class="mt-3 mb-1">{{ $total_orders }}</h3>
                    <p class="mb-0 text-muted"><span class="text-success">{{ $delivered_orders }}</span> delivered</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end"><i class="mdi mdi-clock-outline widget-icon"></i></div>
                    <h5 class="text-muted fw-normal mt-0">Pending Margin</h5>
                    <h3 class="mt-3 mb-1 text-warning">৳ {{ number_format($pending_margin, 0) }}</h3>
                    <p class="mb-0 text-muted">Delivery hole pabe</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end"><i class="mdi mdi-trending-up widget-icon"></i></div>
                    <h5 class="text-muted fw-normal mt-0">Earned Margin</h5>
                    <h3 class="mt-3 mb-1 text-success">৳ {{ number_format($delivered_margin, 0) }}</h3>
                    <p class="mb-0 text-muted">Delivered order theke</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card widget-flat bg-success text-white">
                <div class="card-body">
                    <div class="float-end"><i class="mdi mdi-wallet-outline widget-icon text-white"></i></div>
                    <h5 class="text-white-50 fw-normal mt-0">Available Balance</h5>
                    <h3 class="mt-3 mb-1 text-white">৳ {{ number_format($balance, 0) }}</h3>
                    <p class="mb-0 text-white-50">Withdrawn: ৳ {{ number_format($withdrawn, 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="header-title mb-1">Welcome, {{ Auth::guard('reseller')->user()->name }} 👋</h4>
                        <p class="text-muted mb-0">Delivered order-er margin withdraw korte paren.</p>
                    </div>
                    <a href="{{ route('reseller.withdraw.index') }}" class="btn btn-success">
                        <i class="mdi mdi-cash-multiple"></i> Withdraw Request
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
