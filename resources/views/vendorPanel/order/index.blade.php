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
                    <form class="row g-2 align-items-center" method="GET">
                        <div class="col-sm-4">
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Search by Invoice ID, Phone, or Name">
                        </div>
                        <div class="col-sm-4">
                            <button class="btn btn-info">Search</button>
                            <a href="{{ route('vendor.orders.index', 'all') }}" class="btn btn-light">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice ID</th>
                                    <th>Date</th>
                                    <th>Customer Info</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>{{ $loop->iteration + $orders->firstItem() - 1 }}</td>
                                        <td><strong>{{ $order->invoice_id }}</strong></td>
                                        <td>{{ $order->created_at->format('d M, Y h:i A') }}</td>
                                        <td>
                                            @if($order->shipping)
                                                <strong>{{ $order->shipping->name }}</strong><br>
                                                {{ $order->shipping->phone }}<br>
                                                <small class="text-muted">{{ Str::limit($order->shipping->address, 30) }}</small>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary order-status-badge">
                                                {{ $order->status ? $order->status->name : 'Pending' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('vendor.orders.show', $order->id) }}" class="btn btn-xs btn-info" title="View Details">
                                                <i class="fe-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No orders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $orders->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
