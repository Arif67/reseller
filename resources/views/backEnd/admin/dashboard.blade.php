@extends('backEnd.layouts.master')
@section('title','Dashboard')

@section('css')
<style>
    .dashboard-shell {
        padding-bottom: 24px;
    }

    .dashboard-hero {
        position: relative;
        overflow: hidden;
        border: 0;
        border-radius: 24px;
        background:
            radial-gradient(circle at top right, rgba(255, 255, 255, 0.28), transparent 28%),
            linear-gradient(135deg, #0f766e 0%, #0f3d91 58%, #1d4ed8 100%);
        box-shadow: 0 18px 45px rgba(15, 61, 145, 0.18);
    }

    .dashboard-hero::before,
    .dashboard-hero::after {
        content: "";
        position: absolute;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
    }

    .dashboard-hero::before {
        width: 210px;
        height: 210px;
        top: -70px;
        right: -40px;
    }

    .dashboard-hero::after {
        width: 150px;
        height: 150px;
        left: -50px;
        bottom: -70px;
    }

    .dashboard-hero .card-body {
        position: relative;
        z-index: 1;
        padding: 28px;
    }

    .dashboard-hero h2,
    .dashboard-hero p,
    .dashboard-hero h3,
    .dashboard-hero .hero-meta-label,
    .dashboard-hero .hero-meta-note {
        color: #fff;
    }

    .hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .hero-total {
        font-size: clamp(2rem, 3vw, 3rem);
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.03em;
        margin: 10px 0 12px;
    }

    .hero-subtext {
        max-width: 560px;
        color: rgba(255, 255, 255, 0.82);
        margin-bottom: 0;
    }

    .hero-meta-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .hero-meta-card {
        min-height: 100%;
        padding: 16px 18px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(8px);
    }

    .hero-meta-label {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 700;
        opacity: 0.8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .hero-meta-value {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 800;
        color: #fff;
    }

    .hero-meta-note {
        display: block;
        margin-top: 4px;
        font-size: 12px;
        opacity: 0.74;
    }

    .mini-stat-card,
    .status-card,
    .insight-card {
        border: 0;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    }

    .mini-stat-card .card-body,
    .status-card .card-body,
    .insight-card .card-body {
        padding: 22px;
    }

    .mini-stat-card {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    .mini-stat-card .stat-label,
    .status-card .stat-label {
        display: block;
        margin-bottom: 8px;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .mini-stat-card .stat-number,
    .status-card .stat-number {
        margin: 0;
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        color: #0f172a;
    }

    .mini-stat-card .stat-icon,
    .status-card .stat-icon {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.32);
    }

    .mini-stat-card .stat-foot,
    .status-card .stat-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 16px;
        color: #64748b;
        font-size: 13px;
    }

    .mini-stat-card .amount-pill,
    .status-card .amount-pill {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 800;
        line-height: 1;
        white-space: nowrap;
    }

    .stat-trend {
        font-weight: 700;
    }

    .status-card {
        position: relative;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }

    .status-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.1);
    }

    .status-card::after {
        content: "";
        position: absolute;
        inset: auto 0 0 0;
        height: 4px;
        background: var(--accent);
    }

    .status-card .status-description {
        color: #64748b;
        font-size: 13px;
    }

    .status-card .amount-pill {
        color: var(--accent-deep);
        background: var(--amount-bg);
        box-shadow: inset 0 0 0 1px var(--amount-border);
    }

    .dashboard-link {
        color: inherit;
        text-decoration: none;
    }

    .dashboard-link:hover {
        color: inherit;
        text-decoration: none;
    }

    .insight-card {
        background: #fff;
    }

    .insight-card .section-title {
        margin-bottom: 4px;
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
    }

    .insight-card .section-subtitle {
        margin-bottom: 0;
        color: #64748b;
        font-size: 13px;
    }

    .best-product-image {
        width: 100%;
        max-height: 120px;
        object-fit: cover;
        border-radius: 18px;
        background: #f8fafc;
    }

    .metric-badge {
        display: inline-flex;
        align-items: center;
        padding: 9px 13px;
        border-radius: 999px;
        background: #eef4ff;
        color: #1d4ed8;
        font-size: 13px;
        font-weight: 700;
    }

    .price-box {
        padding: 16px;
        border-radius: 18px;
        text-align: center;
    }

    .price-box small {
        display: block;
        margin-bottom: 4px;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .price-box strong {
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
    }

    .price-box.current {
        background: linear-gradient(180deg, #ecfdf5 0%, #dcfce7 100%);
    }

    .price-box.old {
        background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%);
    }

    .dashboard-table thead th {
        border-bottom: 0;
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding-top: 14px;
        padding-bottom: 14px;
    }

    .dashboard-table tbody td {
        padding-top: 14px;
        padding-bottom: 14px;
        vertical-align: middle;
        border-color: #edf2f7;
    }

    .table-avatar {
        width: 42px;
        height: 42px;
        object-fit: cover;
        border-radius: 14px;
        background: #f8fafc;
    }

    .table-amount {
        color: #0f766e;
        font-weight: 800;
    }

    .status-badge,
    .customer-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-badge {
        background: #eef2ff;
        color: #4338ca;
    }

    .customer-badge.active {
        background: #ecfdf5;
        color: #047857;
    }

    .customer-badge.inactive {
        background: #fef2f2;
        color: #b91c1c;
    }

    @media (max-width: 991.98px) {
        .hero-meta-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .dashboard-hero .card-body,
        .mini-stat-card .card-body,
        .status-card .card-body,
        .insight-card .card-body {
            padding: 18px;
        }

        .hero-total {
            font-size: 2rem;
        }

        .mini-stat-card .stat-number,
        .status-card .stat-number {
            font-size: 1.7rem;
        }
    }
</style>
@endsection

@section('content')
@php
    $quickStats = [
        [
            'label' => "Today's Orders",
            'value' => $today_order_count,
            'icon' => 'fe-shopping-bag',
            'amount' => $today_order_amount,
            'note' => 'Today amount',
            'trend' => 'Live activity',
            'link' => route('admin.orders', ['slug' => 'all', 'date' => 'today']),
            'icon_bg' => 'linear-gradient(135deg, #dcfce7 0%, #86efac 100%)',
            'icon_color' => '#15803d',
            'amount_bg' => 'linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)',
            'amount_color' => '#166534',
        ],
        [
            'label' => 'Products',
            'value' => $total_product,
            'icon' => 'fe-package',
            'amount' => null,
            'note' => 'Live catalog',
            'trend' => 'Manage catalog',
            'link' => route('products.index'),
            'icon_bg' => 'linear-gradient(135deg, #e0f2fe 0%, #7dd3fc 100%)',
            'icon_color' => '#0369a1',
            'amount_bg' => 'linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)',
            'amount_color' => '#1d4ed8',
        ],
        [
            'label' => 'Customers',
            'value' => $total_customer,
            'icon' => 'fe-users',
            'amount' => null,
            'note' => 'Registered base',
            'trend' => 'Growing audience',
            'link' => null,
            'icon_bg' => 'linear-gradient(135deg, #fef3c7 0%, #fcd34d 100%)',
            'icon_color' => '#b45309',
            'amount_bg' => 'linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%)',
            'amount_color' => '#c2410c',
        ],
        [
            'label' => 'In Courier',
            'value' => $in_courier_count,
            'icon' => 'fe-truck',
            'amount' => $in_courier_amount,
            'note' => 'Running delivery load',
            'trend' => $today_delivery . ' dispatched today',
            'link' => route('admin.orders', ['slug' => 'in-courier']),
            'icon_bg' => 'linear-gradient(135deg, #ede9fe 0%, #c4b5fd 100%)',
            'icon_color' => '#6d28d9',
            'amount_bg' => 'linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%)',
            'amount_color' => '#5b21b6',
        ],
    ];

    $statusCards = [
        [
            'label' => 'Total Orders',
            'count' => $total_order_count,
            'amount' => $total_order_amount,
            'icon' => 'fe-shopping-cart',
            'description' => 'All orders across every status',
            'link' => route('admin.orders', ['slug' => 'all']),
            'accent' => '#2563eb',
            'accent_deep' => '#1d4ed8',
            'accent_soft' => 'linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%)',
            'amount_bg' => '#eff6ff',
            'amount_border' => '#bfdbfe',
        ],
        [
            'label' => 'Pending Orders',
            'count' => $pending_count,
            'amount' => $pending_amount,
            'icon' => 'fe-clock',
            'description' => 'Needs quick follow-up',
            'link' => route('admin.orders', ['slug' => 'pending']),
            'accent' => '#d97706',
            'accent_deep' => '#b45309',
            'accent_soft' => 'linear-gradient(135deg, #fef3c7 0%, #fde68a 100%)',
            'amount_bg' => '#fffbeb',
            'amount_border' => '#fde68a',
        ],
        [
            'label' => 'Approved Orders',
            'count' => $processing_count,
            'amount' => $processing_amount,
            'icon' => 'fe-check-circle',
            'description' => 'Approved and in processing',
            'link' => route('admin.orders', ['slug' => 'processing']),
            'accent' => '#16a34a',
            'accent_deep' => '#15803d',
            'accent_soft' => 'linear-gradient(135deg, #dcfce7 0%, #86efac 100%)',
            'amount_bg' => '#f0fdf4',
            'amount_border' => '#bbf7d0',
        ],
        [
            'label' => 'Packed Orders',
            'count' => $on_the_way_count,
            'amount' => $on_the_way_amount,
            'icon' => 'fe-package',
            'description' => 'Packed and ready to move',
            'link' => route('admin.orders', ['slug' => 'on-the-way']),
            'accent' => '#0891b2',
            'accent_deep' => '#0e7490',
            'accent_soft' => 'linear-gradient(135deg, #cffafe 0%, #67e8f9 100%)',
            'amount_bg' => '#ecfeff',
            'amount_border' => '#a5f3fc',
        ],
        [
            'label' => 'On Hold',
            'count' => $on_hold_count,
            'amount' => $on_hold_amount,
            'icon' => 'fe-pause',
            'description' => 'Temporarily paused orders',
            'link' => route('admin.orders', ['slug' => 'on-hold']),
            'accent' => '#64748b',
            'accent_deep' => '#475569',
            'accent_soft' => 'linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%)',
            'amount_bg' => '#f8fafc',
            'amount_border' => '#cbd5e1',
        ],
        [
            'label' => 'Returned Orders',
            'count' => $cancelled_count,
            'amount' => $cancelled_amount,
            'icon' => 'fe-rotate-ccw',
            'description' => 'Returned or cancelled orders',
            'link' => route('admin.orders', ['slug' => 'cancelled']),
            'accent' => '#dc2626',
            'accent_deep' => '#b91c1c',
            'accent_soft' => 'linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)',
            'amount_bg' => '#fef2f2',
            'amount_border' => '#fecaca',
        ],
        [
            'label' => 'Delivered Orders',
            'count' => $completed_count,
            'amount' => $completed_amount,
            'icon' => 'fe-check-circle',
            'description' => 'Completed successfully',
            'link' => route('admin.orders', ['slug' => 'completed']),
            'accent' => '#059669',
            'accent_deep' => '#047857',
            'accent_soft' => 'linear-gradient(135deg, #d1fae5 0%, #6ee7b7 100%)',
            'amount_bg' => '#ecfdf5',
            'amount_border' => '#a7f3d0',
        ],
        [
            'label' => 'In Courier',
            'count' => $in_courier_count,
            'amount' => $in_courier_amount,
            'icon' => 'fe-map-pin',
            'description' => 'Currently with courier',
            'link' => route('admin.orders', ['slug' => 'in-courier']),
            'accent' => '#7c3aed',
            'accent_deep' => '#6d28d9',
            'accent_soft' => 'linear-gradient(135deg, #ede9fe 0%, #c4b5fd 100%)',
            'amount_bg' => '#f5f3ff',
            'amount_border' => '#ddd6fe',
        ],
        [
            'label' => "Didn't Receive Call",
            'count' => $didnt_receive_count,
            'amount' => $didnt_receive_amount,
            'icon' => 'fe-phone-missed',
            'description' => 'Requires calling again',
            'link' => route('admin.orders', ['slug' => 'didnt-receive-call']),
            'accent' => '#db2777',
            'accent_deep' => '#be185d',
            'accent_soft' => 'linear-gradient(135deg, #fce7f3 0%, #f9a8d4 100%)',
            'amount_bg' => '#fdf2f8',
            'amount_border' => '#fbcfe8',
        ],
    ];
@endphp

<div class="container-fluid dashboard-shell">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Dashboard</h4>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card dashboard-hero">
                <div class="card-body">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-7">
                            <span class="hero-chip">
                                <i class="fe-activity"></i>
                                Admin overview
                            </span>
                            <h2 class="mt-3 mb-1">Order performance at a glance</h2>
                            <div class="hero-total">৳ {{ number_format($total_order_amount, 2) }}</div>
                            <p class="hero-subtext">
                                Total revenue snapshot from {{ number_format($total_order_count) }} orders. Monitor fulfilment flow, delivery pressure, and sales movement from one place.
                            </p>
                        </div>
                        <div class="col-lg-5">
                            <div class="hero-meta-grid">
                                <div class="hero-meta-card">
                                    <span class="hero-meta-label">Today Orders</span>
                                    <h3 class="hero-meta-value">{{ number_format($today_order_count) }}</h3>
                                    <span class="hero-meta-note">৳ {{ number_format($today_order_amount, 2) }}</span>
                                </div>
                                <div class="hero-meta-card">
                                    <span class="hero-meta-label">In Courier</span>
                                    <h3 class="hero-meta-value">{{ number_format($total_delivery) }}</h3>
                                    <span class="hero-meta-note">{{ $today_delivery }} dispatched today</span>
                                </div>
                                <div class="hero-meta-card">
                                    <span class="hero-meta-label">Last Periods</span>
                                    <h3 class="hero-meta-value">{{ number_format($last_week) }}</h3>
                                    <span class="hero-meta-note">Last week, {{ number_format($last_month) }} last month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach($quickStats as $stat)
            <div class="col-md-6 col-xl-3">
                @if($stat['link'])
                    <a href="{{ $stat['link'] }}" class="dashboard-link">
                @endif
                <div class="card mini-stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <span class="stat-label">{{ $stat['label'] }}</span>
                                <h3 class="stat-number">
                                    @if(is_numeric($stat['value']))
                                        <span data-plugin="counterup">{{ $stat['value'] }}</span>
                                    @else
                                        {{ $stat['value'] }}
                                    @endif
                                </h3>
                            </div>
                            <span class="stat-icon" style="background: {{ $stat['icon_bg'] }}; color: {{ $stat['icon_color'] }};">
                                <i class="{{ $stat['icon'] }}"></i>
                            </span>
                        </div>
                        <div class="stat-foot">
                            <span class="stat-trend">{{ $stat['trend'] }}</span>
                            @if(!is_null($stat['amount']))
                                <span class="amount-pill" style="background: {{ $stat['amount_bg'] }}; color: {{ $stat['amount_color'] }};">
                                    ৳ {{ number_format($stat['amount'], 2) }}
                                </span>
                            @else
                                <span class="text-muted">{{ $stat['note'] }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @if($stat['link'])
                    </a>
                @endif
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        @foreach($statusCards as $card)
            <div class="col-md-6 col-xl-4">
                <a href="{{ $card['link'] }}" class="dashboard-link">
                    <div class="card status-card h-100" style="--accent: {{ $card['accent'] }}; --accent-deep: {{ $card['accent_deep'] }}; --amount-bg: {{ $card['amount_bg'] }}; --amount-border: {{ $card['amount_border'] }};">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <span class="stat-label">{{ $card['label'] }}</span>
                                    <h3 class="stat-number"><span data-plugin="counterup">{{ $card['count'] }}</span></h3>
                                    <p class="status-description mt-2 mb-0">{{ $card['description'] }}</p>
                                </div>
                                <span class="stat-icon" style="background: {{ $card['accent_soft'] }}; color: {{ $card['accent_deep'] }};">
                                    <i class="{{ $card['icon'] }}"></i>
                                </span>
                            </div>
                            <div class="stat-foot">
                                <span class="text-muted">Amount</span>
                                <span class="amount-pill">৳ {{ number_format($card['amount'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card insight-card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h4 class="section-title">Top Best Selling Product</h4>
                            <p class="section-subtitle">Calculated from completed orders and actual sold quantity.</p>
                        </div>
                        @if($topBestSellingProduct)
                            <a href="{{ route('products.edit', $topBestSellingProduct->id) }}" class="btn btn-outline-primary btn-sm">Manage Product</a>
                        @endif
                    </div>

                    @if($topBestSellingProduct)
                        <div class="row align-items-center g-3">
                            <div class="col-md-2">
                                <img src="{{ asset($topBestSellingProduct->image ? $topBestSellingProduct->image->image : '') }}" alt="{{ $topBestSellingProduct->name }}" class="best-product-image">
                            </div>
                            <div class="col-md-6">
                                <h4 class="mb-2">{{ $topBestSellingProduct->name }}</h4>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="metric-badge">Sold Qty: {{ $topBestSellingProduct->sold_quantity }}</span>
                                    <span class="metric-badge">Orders: {{ $topBestSellingProduct->total_orders }}</span>
                                    <span class="metric-badge">Revenue: ৳ {{ number_format($topBestSellingProduct->sold_amount, 2) }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="price-box current">
                                            <small>Current Price</small>
                                            <strong>৳ {{ number_format($topBestSellingProduct->new_price, 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="price-box old">
                                            <small>Old Price</small>
                                            <strong>{{ $topBestSellingProduct->old_price ? '৳ ' . number_format($topBestSellingProduct->old_price, 2) : 'N/A' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-light border mb-0">
                            No completed order data was found yet, so the top best selling product is not available.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-6">
            <div class="card insight-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="section-title">Latest 5 Orders</h4>
                            <p class="section-subtitle">Newest order activity from the store.</p>
                        </div>
                        <a href="{{ route('admin.orders', ['slug' => 'all']) }}" class="btn btn-light btn-sm">View All</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table dashboard-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Invoice</th>
                                    <th>Amount</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latest_order as $order)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if($order->product)
                                                <img src="{{ asset($order->product->image ? $order->product->image->image : '') }}" alt="product" class="table-avatar">
                                            @else
                                                <div class="table-avatar d-inline-flex align-items-center justify-content-center">
                                                    <i class="fe-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $order->invoice_id }}</td>
                                        <td class="table-amount">৳ {{ number_format($order->amount, 2) }}</td>
                                        <td>{{ $order->customer ? $order->customer->name : '' }}</td>
                                        <td><span class="status-badge">{{ $order->status?->name ?? 'Unknown' }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No orders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card insight-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="section-title">Latest Customers</h4>
                            <p class="section-subtitle">Most recent customer registrations.</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table dashboard-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latest_customer as $customer)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $customer->name }}</td>
                                        <td>{{ $customer->phone }}</td>
                                        <td>{{ $customer->created_at->format('d-m-Y') }}</td>
                                        <td>
                                            <span class="customer-badge {{ (string) $customer->status === '1' ? 'active' : 'inactive' }}">
                                                {{ (string) $customer->status === '1' ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No customers found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
