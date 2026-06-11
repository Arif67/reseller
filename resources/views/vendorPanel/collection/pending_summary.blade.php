@extends('vendorPanel.layouts.master')
@section('title', 'Pending Summary')

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
    .size-chip {
        display: inline-block;
        background: #f1f1f4;
        color: #444;
        border-radius: 4px;
        padding: 2px 8px;
        margin: 2px;
        font-size: 12px;
        font-weight: 600;
    }
    .size-chip b { color: #e91e63; }
    .vendor-item-qty { font-size: 28px; font-weight: 700; color: #e91e63; line-height: 1; }
</style>
@endsection

@section('content')
    @include('vendorPanel.layouts.mobile_menu')

    <div class="row">
        <div class="col-12">
            <div class="total-pics-card">
                Total: {{ $summary->sum('total_qty') }} Pics &nbsp;•&nbsp; {{ $summary->count() }} Product(s)
            </div>
        </div>
    </div>

    <div class="row">
        @forelse($summary as $item)
        <div class="col-md-6 col-xl-4">
            <div class="vendor-item-card">
                <div class="vendor-item-header">
                    <span>Pending in <b>{{ $item->order_count }}</b> order(s)</span>
                    <span>Total QTY: <b class="pink-text">{{ $item->total_qty }}</b></span>
                </div>
                <div class="vendor-item-body">
                    <div class="vendor-item-image mb-2">
                        <img src="{{ asset($item->image) }}" alt="{{ $item->product_name }}">
                    </div>
                    <div class="vendor-item-title">
                        {{ $item->product_name }}
                    </div>
                    <div class="vendor-item-qty mb-2">{{ $item->total_qty }} pcs</div>
                    <div>
                        @foreach($item->sizes as $size => $qty)
                            <span class="size-chip">{{ $size }}: <b>{{ $qty }}</b></span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center text-muted py-5">No pending items.</div>
        </div>
        @endforelse
    </div>
@endsection
