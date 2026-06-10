@extends('vendorPanel.layouts.master')
@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Dashboard</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end"><i class="mdi mdi-package-variant widget-icon"></i></div>
                    <h5 class="text-muted fw-normal mt-0">Total Products</h5>
                    <h3 class="mt-3 mb-3">{{ $total_products }}</h3>
                    <p class="mb-0 text-muted"><span class="text-nowrap">Your shop products</span></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end"><i class="mdi mdi-cart-outline widget-icon"></i></div>
                    <h5 class="text-muted fw-normal mt-0">Total Orders</h5>
                    <h3 class="mt-3 mb-3">{{ $total_orders }}</h3>
                    <p class="mb-0 text-muted"><span class="text-nowrap">Orders with your products</span></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end"><i class="mdi mdi-wallet-outline widget-icon"></i></div>
                    <h5 class="text-muted fw-normal mt-0">Balance</h5>
                    <h3 class="mt-3 mb-3">৳ {{ number_format($balance, 2) }}</h3>
                    <p class="mb-0 text-muted"><span class="text-nowrap">Withdrawable</span></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Welcome, {{ Auth::guard('vendor')->user()->shop_name }} 👋</h4>
                    <p class="text-muted mt-2">
                        You can now manage your products and track your orders from the vendor panel.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
