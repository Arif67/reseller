@extends('vendorPanel.layouts.master')
@section('title', 'Order Details')

@section('content')
    <div class="row align-items-center mb-3">
        <div class="col">
            <div class="page-title-box">
                <h4 class="page-title">Order Details: {{ $order->invoice_id }}</h4>
            </div>
        </div>
        <div class="col-auto">
            <a href="{{ route('vendor.orders.index', 'all') }}" class="btn btn-secondary btn-sm">
                <i class="fe-arrow-left"></i> Back to Orders
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Invoice ID:</strong> {{ $order->invoice_id }}</p>
                    <p class="mb-1"><strong>Date:</strong> {{ $order->created_at->format('d M, Y h:i A') }}</p>
                    <p class="mb-1">
                        <strong>Status:</strong>
                        <span class="badge bg-primary">{{ $order->status ? $order->status->name : 'Pending' }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Products from Your Shop</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>Variant</th>
                                    <th>Qty</th>
                                    <th class="text-end">Your Rate</th>
                                    <th class="text-end">Your Earning</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalEarning = 0; @endphp
                                @foreach($order->orderdetails as $detail)
                                    @php
                                        $lineEarning = $detail->qty * $detail->purchase_price;
                                        $totalEarning += $lineEarning;
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            @if($detail->product && $detail->product->image)
                                                <img src="{{ asset($detail->product->image->image) }}" alt="product" height="40" class="rounded">
                                            @else
                                                <img src="{{ asset('public/uploads/default/product.png') }}" alt="default" height="40" class="rounded">
                                            @endif
                                        </td>
                                        <td>{{ $detail->product_name }}</td>
                                        <td>
                                            @if($detail->variant_label)
                                                <small class="text-muted">{{ $detail->variant_label }}</small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $detail->qty }}</td>
                                        <td class="text-end">৳ {{ number_format($detail->purchase_price, 2) }}</td>
                                        <td class="text-end">৳ {{ number_format($lineEarning, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Your Total Earning from this Order:</strong></td>
                                    <td class="text-end"><strong class="text-success">৳ {{ number_format($totalEarning, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fe-info"></i> Note: "Your Rate" is the wholesale price you charge the hub/admin. The retail price customers pay is higher; the difference goes to the platform and resellers.
            </div>
        </div>
    </div>
@endsection
