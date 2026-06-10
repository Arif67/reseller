@extends('vendorPanel.layouts.master')
@section('title', 'Collected')

@section('css')
<style>
    .vendor-item-card {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        background: #fff;
    }
    .vendor-item-header {
        display: flex;
        justify-content: space-between;
        padding: 10px 15px;
        border-bottom: 1px solid #f1f1f1;
        font-size: 13px;
        color: #555;
    }
    .vendor-item-body {
        padding: 15px;
        text-align: center;
    }
    .vendor-item-stats {
        display: flex;
        justify-content: center;
        gap: 40px;
        margin-bottom: 10px;
        font-size: 16px;
        font-weight: bold;
    }
    .vendor-item-stats .pink-text {
        color: #e91e63;
    }
    .vendor-item-title {
        font-size: 16px;
        color: #333;
        margin-bottom: 15px;
    }
    .vendor-item-image img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }
    .total-pics-card {
        text-align: center;
        padding: 15px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        font-weight: bold;
        font-size: 16px;
    }
</style>
@endsection

@section('content')
    @include('vendorPanel.layouts.mobile_menu')

    <div class="row">
        <div class="col-12">
            <div class="total-pics-card">
                Total: {{ $orderDetails->sum('qty') }} Pics
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($orderDetails as $detail)
        <div class="col-md-6 col-xl-4">
            <div class="vendor-item-card">
                <div class="vendor-item-header">
                    <span>ID: {{ $detail->order ? $detail->order->invoice_id : $detail->id }}</span>
                    <span>Time: {{ $detail->created_at->format('M-d h:i A') }}</span>
                </div>
                <div class="vendor-item-body">
                    <div class="vendor-item-stats">
                        <span>SIZE : <span class="pink-text">{{ $detail->product_size ?: 'N/A' }}</span></span>
                        <span>QTY : <span class="pink-text">{{ $detail->qty }}</span></span>
                    </div>
                    <div class="vendor-item-title">
                        {{ $detail->product_name }}
                    </div>
                    <div class="vendor-item-image">
                        @if($detail->product && $detail->product->image)
                            <img src="{{ asset($detail->product->image->image) }}" alt="{{ $detail->product_name }}">
                        @else
                            <img src="{{ asset('public/uploads/default/product.png') }}" alt="{{ $detail->product_name }}">
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endsection
