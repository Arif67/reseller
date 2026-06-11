@extends('vendorPanel.layouts.master')
@section('title', 'Collection')

@section('css')
<style>
    .vendor-item-card {
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        margin-bottom: 12px;
        background: #fff;
    }
    .vendor-item-header {
        display: flex;
        justify-content: space-between;
        padding: 5px 8px;
        border-bottom: 1px solid #f1f1f1;
        font-size: 10px;
        color: #777;
    }
    .vendor-item-body {
        padding: 8px;
        text-align: center;
    }
    .vendor-item-stats {
        display: flex;
        justify-content: center;
        gap: 14px;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: bold;
    }
    .vendor-item-stats .pink-text {
        color: #e91e63;
    }
    .vendor-item-title {
        font-size: 12px;
        color: #333;
        margin-bottom: 8px;
        line-height: 1.3;
        height: 31px;
        overflow: hidden;
    }
    .vendor-item-image img {
        width: 100%;
        height: 110px;
        object-fit: cover;
        border-radius: 6px;
    }
    .vendor-item-footer {
        padding: 0 8px 8px;
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
                Total: <span id="total-pics">{{ $orderDetails->sum('qty') }}</span> Pics
            </div>
        </div>
    </div>

    <div class="row" id="collection-list">
        @foreach($orderDetails as $detail)
        <div class="col-6 col-md-4 col-xl-2 collection-item" data-qty="{{ $detail->qty }}">
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
                    <form action="{{ route('vendor.collection.mark') }}" method="POST" class="collect-form">
                        @csrf
                        <input type="hidden" name="id" value="{{ $detail->id }}">
                        <button type="submit" class="btn btn-sm btn-success w-100">
                            <i class="fe-check"></i> Collected
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="text-center text-muted py-5" id="collection-empty" @if($orderDetails->isNotEmpty()) style="display:none;" @endif>
        No items to collect.
    </div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = '{{ csrf_token() }}';

    function updateMenuCounters(counts) {
        if (!counts) return;
        Object.keys(counts).forEach(function (key) {
            document.querySelectorAll('[data-counter="' + key + '"]').forEach(function (el) {
                el.textContent = counts[key];
            });
        });
    }

    document.querySelectorAll('.collect-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const card  = form.closest('.collection-item');
            const qty   = parseInt(card.getAttribute('data-qty') || '0', 10);
            const btn   = form.querySelector('button');
            const id    = form.querySelector('input[name="id"]').value;

            btn.disabled = true;
            btn.innerHTML = '<i class="fe-loader"></i> Saving...';

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + encodeURIComponent(id),
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.status !== 'success') throw new Error('failed');

                // Drop the card and adjust the pics total.
                const totalEl = document.getElementById('total-pics');
                totalEl.textContent = Math.max(0, (parseInt(totalEl.textContent, 10) || 0) - qty);

                card.style.transition = 'opacity .25s';
                card.style.opacity = '0';
                setTimeout(function () {
                    card.remove();
                    if (!document.querySelector('#collection-list .collection-item')) {
                        document.getElementById('collection-empty').style.display = '';
                    }
                }, 250);

                updateMenuCounters(data.counts);

                if (window.toastr) toastr.success(data.message || 'Collected');
            })
            .catch(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="fe-check"></i> Collected';
                if (window.toastr) toastr.error('Could not save. Try again.');
            });
        });
    });
});
</script>
@endsection
