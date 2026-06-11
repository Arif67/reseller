@extends('vendorPanel.layouts.master')
@section('title', 'Dashboard')

@section('css')
<style>
    .stat-link .card { transition: transform .15s ease, box-shadow .15s ease; }
    .stat-link:hover .card {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0,0,0,.12);
    }
</style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Dashboard</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('vendor.products.index') }}" class="text-reset text-decoration-none d-block stat-link">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end"><i class="mdi mdi-package-variant widget-icon"></i></div>
                        <h5 class="text-muted fw-normal mt-0">Total Products</h5>
                        <h3 class="mt-3 mb-3">{{ $total_products }}</h3>
                        <p class="mb-0 text-muted"><span class="text-nowrap">Your shop products</span></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('vendor.orders.index', 'all') }}" class="text-reset text-decoration-none d-block stat-link">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end"><i class="mdi mdi-cart-outline widget-icon"></i></div>
                        <h5 class="text-muted fw-normal mt-0">Total Orders</h5>
                        <h3 class="mt-3 mb-3">{{ $total_orders }}</h3>
                        <p class="mb-0 text-muted"><span class="text-nowrap">View all orders</span></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('vendor.orders.index', 'delivered') }}" class="text-reset text-decoration-none d-block stat-link">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end"><i class="mdi mdi-check-circle-outline widget-icon text-success"></i></div>
                        <h5 class="text-muted fw-normal mt-0">Delivered Orders</h5>
                        <h3 class="mt-3 mb-3">{{ $delivered_orders }}</h3>
                        <p class="mb-0 text-muted"><span class="text-nowrap">Completed sales</span></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('vendor.orders.index', 'returned') }}" class="text-reset text-decoration-none d-block stat-link">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end"><i class="mdi mdi-keyboard-return widget-icon text-danger"></i></div>
                        <h5 class="text-muted fw-normal mt-0">Returned Orders</h5>
                        <h3 class="mt-3 mb-3">{{ $returned_orders }}</h3>
                        <p class="mb-0 text-muted"><span class="text-nowrap">Returned / refunded</span></p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Earnings Summary <small class="text-muted">(your rate)</small></h4>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted"><i class="mdi mdi-clock-outline text-warning"></i> Pending <small>(not delivered yet)</small></span>
                        <span class="fw-bold text-warning">৳ {{ number_format($summary['pending'], 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted"><i class="mdi mdi-check-circle text-success"></i> Earned <small>(delivered)</small></span>
                        <span class="fw-bold text-success">৳ {{ number_format($summary['earned'], 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted"><i class="mdi mdi-close-circle text-secondary"></i> Cancelled</span>
                        <span class="fw-bold text-secondary">৳ {{ number_format($summary['cancelled'], 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted"><i class="mdi mdi-keyboard-return text-danger"></i> Returned</span>
                        <span class="fw-bold text-danger">৳ {{ number_format($summary['returned'], 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted"><i class="mdi mdi-wallet-outline"></i> Available to withdraw</span>
                        <span class="fw-bold {{ $summary['available'] < 0 ? 'text-danger' : 'text-success' }}">৳ {{ number_format($summary['available'], 2) }}</span>
                    </div>
                    <a href="{{ route('vendor.payment') }}" class="btn btn-sm btn-outline-success w-100 mt-2">Go to Payment →</a>
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Recent Orders</h4>

                    @if ($recent_orders->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="mdi mdi-cart-off h2 d-block mb-2"></i>
                            No orders yet.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recent_orders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('vendor.orders.show', $order->id) }}">
                                                    {{ $order->invoice_id }}
                                                </a>
                                            </td>
                                            <td>{{ $order->created_at?->format('d M Y') }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ optional($order->status)->name ?? '—' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Welcome, {{ Auth::guard('vendor')->user()->shop_name }} 👋</h4>
                    <p class="text-muted mt-2 mb-0">
                        You can manage your products and track your orders from the vendor panel.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
