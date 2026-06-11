@extends('backEnd.layouts.master')
@section('title','Hub Stock')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-title-box"><h4 class="page-title">Hub Stock — Total {{ $totalPics }} pcs</h4></div>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.hub.receiving') }}" class="btn btn-outline-success btn-sm">
                <i class="fe-download"></i> Receiving Queue
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row g-2 mb-3" method="GET">
                        <div class="col-sm-4">
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Product name">
                        </div>
                        <div class="col-sm-2">
                            <button class="btn btn-info">Search</button>
                            <a href="{{ route('admin.hub.stock') }}" class="btn btn-light">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-centered nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Image</th><th>Product</th><th>Shop</th><th>Variant / Size</th><th>Hub Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stocks as $stock)
                                    @php
                                        $img = optional(optional($stock->product)->image)->image ?? 'public/uploads/default/product.png';
                                        $variant = optional($stock->productVariable)->size
                                            ?? optional($stock->productVariable)->color
                                            ?? '—';
                                    @endphp
                                    <tr>
                                        <td><img src="{{ asset($img) }}" height="40" class="rounded" alt=""></td>
                                        <td>{{ optional($stock->product)->name ?? 'Product #'.$stock->product_id }}</td>
                                        <td>{{ optional(optional($stock->product)->vendor)->shop_name ?? '—' }}</td>
                                        <td>{{ $variant }}</td>
                                        <td><span class="badge bg-success">{{ $stock->qty }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">Hub stock empty.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $stocks->links('pagination::bootstrap-4') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
