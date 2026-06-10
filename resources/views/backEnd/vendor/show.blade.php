@extends('backEnd.layouts.master')
@section('title','Vendor Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="page-title-box"><h4 class="page-title">{{ $vendor->shop_name }}</h4></div>
        </div>
        <div class="col-auto pt-2">
            <a href="{{ route('admin.vendors.index') }}" class="btn btn-light btn-sm">← Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Vendor Info</h4>
                    <table class="table table-borderless mb-0">
                        <tr><th>Owner</th><td>{{ $vendor->name }}</td></tr>
                        <tr><th>Shop</th><td>{{ $vendor->shop_name }}</td></tr>
                        <tr><th>Phone</th><td>{{ $vendor->phone }}</td></tr>
                        <tr><th>Email</th><td>{{ $vendor->email ?? '—' }}</td></tr>
                        <tr><th>Address</th><td>{{ $vendor->address ?? '—' }}</td></tr>
                        <tr><th>Commission</th><td>{{ $vendor->commission_rate }}%</td></tr>
                        <tr><th>Balance</th><td>৳ {{ number_format($vendor->balance,2) }}</td></tr>
                        <tr><th>Products</th><td>{{ $vendor->products_count }}</td></tr>
                        <tr><th>Status</th><td>
                            @if($vendor->status=='active')<span class="badge bg-success">Active</span>
                            @elseif($vendor->status=='pending')<span class="badge bg-warning">Pending</span>
                            @else<span class="badge bg-danger">Suspended</span>@endif
                        </td></tr>
                    </table>
                    <div class="mt-2">
                        @if($vendor->status !== 'active')
                            <form action="{{ route('admin.vendors.approve') }}" method="POST" class="d-inline">
                                @csrf<input type="hidden" name="hidden_id" value="{{ $vendor->id }}">
                                <button class="btn btn-success btn-sm">Approve</button>
                            </form>
                        @else
                            <form action="{{ route('admin.vendors.suspend') }}" method="POST" class="d-inline">
                                @csrf<input type="hidden" name="hidden_id" value="{{ $vendor->id }}">
                                <button class="btn btn-warning btn-sm">Suspend</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Products</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead><tr><th>Image</th><th>Name</th><th>Price</th><th>Stock</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($products as $p)
                                    <tr>
                                        <td><img src="{{ asset($p->image->image ?? 'public/uploads/default/user.png') }}" height="36" class="rounded"></td>
                                        <td>{{ $p->name }}</td>
                                        <td>৳ {{ number_format($p->new_price,2) }}</td>
                                        <td>{{ $p->stock }}</td>
                                        <td>@if($p->status)<span class="badge bg-success">Active</span>@else<span class="badge bg-danger">Inactive</span>@endif</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-3">Kono product nei.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2">{{ $products->links('pagination::bootstrap-4') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
