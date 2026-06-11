@extends('vendorPanel.layouts.master')
@section('title', 'Returns')

@section('css')
<style>
    .vendor-item-card {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        background: #fff;
    }
    .vendor-item-header {
        display: flex; justify-content: space-between;
        padding: 10px 15px; border-bottom: 1px solid #f1f1f1;
        font-size: 13px; color: #555;
    }
    .vendor-item-body { padding: 15px; text-align: center; }
    .vendor-item-stats {
        display: flex; justify-content: center; gap: 40px;
        margin-bottom: 10px; font-size: 16px; font-weight: bold;
    }
    .vendor-item-stats .pink-text { color: #e91e63; }
    .vendor-item-title { font-size: 16px; color: #333; margin-bottom: 15px; }
    .vendor-item-image img { max-width: 100%; height: auto; border-radius: 8px; }
    .vendor-item-footer { padding: 0 15px 15px; }
    .section-head {
        font-weight: 700; font-size: 16px; margin: 8px 0 14px;
        padding-bottom: 6px; border-bottom: 2px solid #eee;
    }
    .return-received-tag {
        text-align: center; background: #e8f5e9; color: #1b5e20;
        border-radius: 6px; padding: 8px; font-weight: 600; font-size: 13px;
    }
</style>
@endsection

@section('content')
    @include('vendorPanel.layouts.mobile_menu')

    {{-- ============ TO RECEIVE ============ --}}
    <div class="section-head text-danger">
        <i class="fe-corner-down-left"></i> Returns to Receive ({{ $toReceive->count() }})
    </div>
    <div class="row">
        @forelse($toReceive as $detail)
        <div class="col-md-6 col-xl-4">
            <div class="vendor-item-card">
                <div class="vendor-item-header">
                    <span>ID: {{ $detail->order ? $detail->order->invoice_id : $detail->id }}</span>
                    <span>{{ optional($detail->created_at)->format('M-d h:i A') }}</span>
                </div>
                <div class="vendor-item-body">
                    <div class="vendor-item-stats">
                        <span>SIZE : <span class="pink-text">{{ $detail->product_size ?: 'N/A' }}</span></span>
                        <span>QTY : <span class="pink-text">{{ $detail->qty }}</span></span>
                    </div>
                    <div class="vendor-item-title">{{ $detail->product_name }}</div>
                    <div class="vendor-item-image">
                        @if($detail->product && $detail->product->image)
                            <img src="{{ asset($detail->product->image->image) }}" alt="{{ $detail->product_name }}">
                        @else
                            <img src="{{ asset('public/uploads/default/product.png') }}" alt="{{ $detail->product_name }}">
                        @endif
                    </div>
                </div>
                <div class="vendor-item-footer">
                    <form action="{{ route('vendor.returns.receive') }}" method="POST" onsubmit="return confirm('Confirm you received this returned product?')">
                        @csrf
                        <input type="hidden" name="id" value="{{ $detail->id }}">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fe-check"></i> Mark Return Received
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12"><div class="text-center text-muted py-4">No returns waiting.</div></div>
        @endforelse
    </div>

    {{-- ============ RECEIVED LIST ============ --}}
    <div class="section-head text-success mt-3">
        <i class="fe-check-circle"></i> Received Returns ({{ $received->count() }})
    </div>
    <div class="row">
        @forelse($received as $detail)
        <div class="col-md-6 col-xl-4">
            <div class="vendor-item-card">
                <div class="vendor-item-header">
                    <span>ID: {{ $detail->order ? $detail->order->invoice_id : $detail->id }}</span>
                    <span>QTY: {{ $detail->qty }}</span>
                </div>
                <div class="vendor-item-body">
                    <div class="vendor-item-title">{{ $detail->product_name }}</div>
                    <div class="vendor-item-image">
                        @if($detail->product && $detail->product->image)
                            <img src="{{ asset($detail->product->image->image) }}" alt="{{ $detail->product_name }}">
                        @else
                            <img src="{{ asset('public/uploads/default/product.png') }}" alt="{{ $detail->product_name }}">
                        @endif
                    </div>
                </div>
                <div class="vendor-item-footer">
                    <div class="return-received-tag">
                        <i class="fe-home"></i> Received back
                        @if($detail->vendor_return_received_at)
                            <small>({{ \Illuminate\Support\Carbon::parse($detail->vendor_return_received_at)->format('M-d h:i A') }})</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12"><div class="text-center text-muted py-4">No received returns yet.</div></div>
        @endforelse
    </div>
@endsection
