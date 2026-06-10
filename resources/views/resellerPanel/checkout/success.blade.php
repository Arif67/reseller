@extends('resellerPanel.layouts.master')
@section('title', 'Order Placed')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body text-center py-4">
                    <i class="mdi mdi-check-circle text-success" style="font-size:54px;"></i>
                    <h3 class="mt-2">Order Place Hoyeche!</h3>
                    <p class="text-muted">Invoice <strong>#{{ $order->invoice_id }}</strong></p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <h5 class="header-title">Customer / Delivery</h5>
                            <p class="mb-0"><strong>{{ $order->shipping->name ?? '' }}</strong></p>
                            <p class="mb-0">{{ $order->shipping->phone ?? '' }}</p>
                            <p class="mb-0">{{ $order->shipping->address ?? '' }}</p>
                            <p class="mb-0 text-muted">Area: {{ $order->shipping->area ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-2 text-md-end">
                            <h5 class="header-title">Summary</h5>
                            <p class="mb-0">Customer Pays: <strong>৳ {{ number_format($order->amount, 0) }}</strong></p>
                            <p class="mb-0">Shipping: ৳ {{ number_format($order->shipping_charge, 0) }}</p>
                            <p class="mb-0 text-success">Apnar Margin: <strong>৳ {{ number_format($order->reseller_margin, 0) }}</strong></p>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm">
                            <thead><tr><th>Product</th><th>Variant</th><th>Qty</th><th class="text-end">Sell Price</th></tr></thead>
                            <tbody>
                                @foreach($order->orderdetails as $d)
                                    <tr>
                                        <td>{{ $d->product_name }}</td>
                                        <td>{{ $d->variant_label ?: '—' }}</td>
                                        <td>{{ $d->qty }}</td>
                                        <td class="text-end">৳ {{ number_format($d->sale_price, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('reseller.products.index') }}" class="btn btn-outline-secondary">Continue</a>
                        <a href="{{ route('reseller.orders.index') }}" class="btn btn-success">My Orders</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
