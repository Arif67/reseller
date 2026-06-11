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
    .vendor-item-footer {
        padding: 0 15px 15px;
    }
    .collected-tick {
        text-align: center;
        color: #28a745;
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 8px;
    }
    .hub-received-tag {
        text-align: center;
        background: #e8f5e9;
        color: #1b5e20;
        border-radius: 6px;
        padding: 8px;
        font-weight: 600;
        font-size: 13px;
    }
    .hub-waiting-tag {
        text-align: center;
        color: #b8860b;
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 8px;
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
        @forelse($orderDetails as $detail)
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
                <div class="vendor-item-footer">
                    @if($detail->vendor_collected_at)
                        <div class="collected-tick">
                            <i class="fe-check-circle"></i> Collected on {{ \Illuminate\Support\Carbon::parse($detail->vendor_collected_at)->format('M-d h:i A') }}
                        </div>
                    @endif

                    @if($detail->admin_received)
                        <div class="hub-received-tag">
                            <i class="fe-home"></i> Received at Hub
                            @if($detail->admin_received_at)
                                <small>({{ \Illuminate\Support\Carbon::parse($detail->admin_received_at)->format('M-d h:i A') }})</small>
                            @endif
                        </div>
                    @else
                        <div class="hub-waiting-tag">
                            <i class="fe-clock"></i> Waiting for hub pickup
                        </div>
                        <form action="{{ route('vendor.collection.uncollect') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $detail->id }}">
                            <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="fe-corner-up-left"></i> Move back to Collection
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center text-muted py-5">No collected items yet.</div>
        </div>
        @endforelse
    </div>
@endsection
