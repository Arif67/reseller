@extends('vendorPanel.layouts.master')
@section('title', 'My Orders')

@section('css')
<style>
    .order-status-badge {
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: 500;
    }
    /* Order cards */
    .order-card {
        border: 1px solid #ececf1; border-radius: 10px;
        margin-bottom: 16px; background: #fff; overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        height: calc(100% - 16px);
        display: flex; flex-direction: column;
    }
    .order-card .oc-image { position: relative; }
    .order-card .oc-image img {
        width: 100%; height: 180px; object-fit: cover; display: block;
        background: #f7f7f9;
    }
    .order-card .oc-status {
        position: absolute; top: 8px; right: 8px;
    }
    .order-card .oc-img-count {
        position: absolute; bottom: 8px; right: 8px;
        background: rgba(0,0,0,.6); color: #fff;
        border-radius: 20px; padding: 2px 9px; font-size: 12px; font-weight: 600;
    }
    .order-card .oc-body { padding: 10px 12px; display: flex; flex-direction: column; flex: 1; }
    .order-card .oc-invoice { font-weight: 700; margin-bottom: 4px; }
    .order-card .oc-meta { font-size: 13px; color: #555; line-height: 1.5; }
    .order-card .btn { margin-top: auto; }
</style>
@endsection

@section('content')
    @include('vendorPanel.layouts.mobile_menu')
    <div class="row align-items-center">
        <div class="col">
            <div class="page-title-box">
                <h4 class="page-title">
                    Orders
                    @if($slug !== 'all')
                        <span class="text-muted">({{ ucfirst(str_replace('-', ' ', $slug)) }})</span>
                    @endif
                </h4>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card mb-0">
                <div class="card-body">
                    <form class="row g-2 align-items-center mb-2" method="GET">
                        <div class="col-sm-4">
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Search by Invoice ID or Product">
                        </div>
                        <div class="col-sm-4">
                            <button class="btn btn-info">Search</button>
                            <a href="{{ route('vendor.orders.index', 'all') }}" class="btn btn-light">Reset</a>
                        </div>
                    </form>

                    @php
                        $tabs = [
                            'all'       => 'All',
                            'pending'   => 'Pending',
                            'processing'=> 'Processing',
                            'shipped'   => 'Shipped',
                            'delivered' => 'Delivered',
                            'returned'  => 'Returned',
                            'cancelled' => 'Cancelled',
                        ];
                    @endphp
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($tabs as $key => $label)
                            <a href="{{ route('vendor.orders.index', $key) }}"
                               class="btn btn-sm {{ $slug === $key ? 'btn-primary' : 'btn-outline-secondary' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== ORDER CARDS (all screens) ===================== --}}
    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5">
        @forelse($orders as $order)
            @php
                $thumbs = $order->orderdetails->map(function ($d) {
                    return $d->image->image ?? optional($d->product->image)->image ?? 'uploads/logo.png';
                });
                $mainImage = $thumbs->first() ?? 'uploads/logo.png';
            @endphp
            <div class="col">
                <div class="order-card">
                    <div class="oc-image">
                        <img src="{{ asset($mainImage) }}" alt="order item">
                        <span class="oc-status badge bg-primary order-status-badge">
                            {{ $order->status ? $order->status->name : 'Pending' }}
                        </span>
                        @if($thumbs->count() > 1)
                            <span class="oc-img-count">+{{ $thumbs->count() - 1 }} more</span>
                        @endif
                    </div>

                    <div class="oc-body">
                        <div class="oc-invoice">#{{ $order->invoice_id }}</div>
                        <div class="oc-meta">
                            @foreach($order->orderdetails as $d)
                                <div>{{ $d->product_name }} <span class="text-muted">×{{ $d->qty }}@if($d->product_size) · {{ $d->product_size }}@endif</span></div>
                            @endforeach
                            <div class="text-muted">{{ $order->created_at->format('d M, Y h:i A') }}</div>
                        </div>

                        <a href="{{ route('vendor.orders.show', $order->id) }}" class="btn btn-info btn-sm w-100 mt-2">
                            <i class="fe-eye"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="text-center text-muted py-4">No orders found.</div></div>
        @endforelse
    </div>

    <div class="row">
        <div class="col-12">
            <div class="mt-3">
                {{ $orders->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection
