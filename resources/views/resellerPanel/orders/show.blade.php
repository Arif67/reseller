@extends('resellerPanel.layouts.master')
@section('title', 'Order #' . $order->invoice_id)

@section('css')
<style>
    .ord-thumb { width:48px; height:48px; object-fit:contain; background:#f7f7f7; border-radius:6px; padding:3px; }
    #fraudModal table { width:100%; border-collapse:collapse; }
    #fraudModal th, #fraudModal td { border:1px solid #ddd; padding:8px 12px; text-align:left; font-size:13px; }
    #fraudModal th { background:#0b5345; color:#fff; }
</style>
@endsection

@section('content')
    @php
        $statusMap = [
            1 => ['Pending', 'warning'], 2 => ['Processing', 'info'], 3 => ['On Hold', 'secondary'],
            4 => ['Confirmed', 'primary'], 5 => ['Shipped', 'info'], 6 => ['Out for Delivery', 'info'],
            7 => ['Delivered', 'success'], 8 => ['Stockout', 'dark'], 9 => ['Cancelled', 'danger'],
            10 => ['Refunded', 'dark'], 11 => ['Returned', 'dark'], 12 => ['Failed', 'danger'],
        ];
        [$stName, $stColor] = $statusMap[$order->order_status] ?? ['Unknown', 'secondary'];
        $isPending = $order->order_status == 1;
    @endphp

    <div class="row align-items-center mb-2">
        <div class="col">
            <div class="page-title-box">
                <h4 class="page-title">Order #{{ $order->invoice_id }}
                    <span class="badge bg-{{ $stColor }} align-middle ms-1">{{ $stName }}</span>
                </h4>
            </div>
        </div>
        <div class="col-auto">
            <a href="{{ route('reseller.orders.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="mdi mdi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- Action bar --}}
    <div class="card">
        <div class="card-body py-2 d-flex flex-wrap gap-2 align-items-center">
            <button type="button" class="btn btn-sm btn-info fraud-checker"
                data-phone="{{ $order->shipping->phone ?? '' }}">
                <i class="mdi mdi-shield-search"></i> Fraud / Courier Report
            </button>

            <a href="{{ route('reseller.tickets.create', ['order_id' => $order->id]) }}" class="btn btn-sm btn-warning">
                <i class="mdi mdi-ticket-confirmation"></i> Report Issue
            </a>

            @if($isPending)
                <a href="{{ route('reseller.orders.edit', $order->id) }}" class="btn btn-sm btn-primary">
                    <i class="mdi mdi-pencil"></i> Edit Order
                </a>
                <form action="{{ route('reseller.orders.cancel', $order->id) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Order cancel korben? Eta ar undo hobe na.')">
                    @csrf
                    <button class="btn btn-sm btn-danger"><i class="mdi mdi-close-circle"></i> Cancel Order</button>
                </form>
            @else
                <span class="text-muted small ms-1">
                    <i class="mdi mdi-information-outline"></i>
                    Order ekhon "{{ $stName }}" — edit/cancel sudhu Pending obosthay kora jay.
                </span>
            @endif
        </div>
    </div>

    <div class="row">
        {{-- Customer / Shipping --}}
        <div class="col-lg-5 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="header-title mb-3">Customer / Delivery</h5>
                    <table class="table table-sm mb-0">
                        <tr><th style="width:120px;">Name</th><td>{{ $order->shipping->name ?? '—' }}</td></tr>
                        <tr><th>Phone</th><td>{{ $order->shipping->phone ?? '—' }}</td></tr>
                        <tr><th>Address</th><td>{{ $order->shipping->address ?? '—' }}</td></tr>
                        <tr><th>Area</th><td>{{ $order->shipping->area ?? 'N/A' }}</td></tr>
                        <tr><th>Order Date</th><td>{{ $order->created_at->format('d M Y, h:i A') }}</td></tr>
                        @if($order->note)<tr><th>Note</th><td>{{ $order->note }}</td></tr>@endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Summary --}}
        <div class="col-lg-7 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="header-title mb-3">Order Summary</h5>
                    <div class="table-responsive">
                        <table class="table table-centered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th><th>Variant</th><th>Qty</th>
                                    <th class="text-end">Sell Price</th><th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderdetails as $d)
                                    @php
                                        $img = $d->product->primary_media_image ?? 'public/uploads/default/user.png';
                                        $imgSrc = \Illuminate\Support\Str::startsWith($img, ['http://','https://']) ? $img : asset($img);
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $imgSrc }}" class="ord-thumb me-2" alt="">
                                                <span style="font-size:13px;">{{ $d->product_name }}</span>
                                            </div>
                                        </td>
                                        <td style="font-size:12px;">
                                            @if($d->variant_label)
                                                <span class="badge bg-light text-dark">{{ $d->variant_label }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $d->qty }}</td>
                                        <td class="text-end">৳ {{ number_format($d->sale_price, 0) }}</td>
                                        <td class="text-end">৳ {{ number_format($d->sale_price * $d->qty, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between"><span>Items Total</span>
                        <span>৳ {{ number_format($order->amount - $order->shipping_charge, 0) }}</span></div>
                    <div class="d-flex justify-content-between"><span>Shipping</span>
                        <span>৳ {{ number_format($order->shipping_charge, 0) }}</span></div>
                    <div class="d-flex justify-content-between fw-bold fs-5 mt-1"><span>Customer Pays</span>
                        <span>৳ {{ number_format($order->amount, 0) }}</span></div>
                    <div class="d-flex justify-content-between text-success mt-2"><span>Apnar Margin (profit)</span>
                        <span class="fw-bold">৳ {{ number_format($order->reseller_margin, 0) }}</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fraud / Courier Report Modal --}}
    <div id="fraudModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:9999; justify-content:center; align-items:center;">
        <div style="background:#fff; border-radius:8px; max-width:760px; width:92%; padding:20px; position:relative;">
            <button id="fraudModalClose" style="position:absolute; top:10px; right:14px; background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
            <div class="d-flex align-items-center gap-2 mb-3">
                <h4 class="mb-0">Fraud / Courier Report</h4>
                <span id="fraudCacheBadge" class="badge bg-success" style="display:none;">Cached</span>
            </div>
            <div id="fraudModalContent" style="max-height:420px; overflow-y:auto;"></div>
        </div>
    </div>
@endsection

@section('script')
<script>
(function () {
    const endpoint = "{{ route('reseller.orders.fraud_check') }}";
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const modal = document.getElementById('fraudModal');
    const content = document.getElementById('fraudModalContent');
    const badge = document.getElementById('fraudCacheBadge');

    document.getElementById('fraudModalClose').addEventListener('click', closeModal);
    window.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    function closeModal() { modal.style.display = 'none'; content.innerHTML = ''; badge.style.display = 'none'; }

    document.querySelector('.fraud-checker').addEventListener('click', function () {
        const phone = this.dataset.phone;
        if (!phone) { alert('Phone number nei'); return; }

        content.innerHTML = '<p class="p-3">Loading fraud / courier data...</p>';
        modal.style.display = 'flex';

        fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ phone: String(phone) }),
        })
        .then(res => res.json().then(data => ({ ok: res.ok, data })))
        .then(({ ok, data }) => {
            if (!ok) { badge.style.display = 'none'; content.innerHTML = '<p class="p-3">' + (data.message || 'Failed') + '</p>'; return; }
            badge.style.display = data.cached ? 'inline-block' : 'none';

            const courierData = data?.courierData || data?.data?.courierData || data?.data || data?.result || null;
            if (!courierData || typeof courierData !== 'object') {
                content.innerHTML = '<p class="p-3">' + (data?.message || 'No data found.') + '</p>';
                return;
            }

            const summary = courierData.summary || data?.summary || data?.data?.summary || {};
            const table = document.createElement('table');
            table.innerHTML = '<tr><th>Logo</th><th>Courier</th><th>Total</th><th>Success</th><th>Cancelled</th><th>Success Rate</th></tr>';

            for (const key in courierData) {
                if (key === 'summary') continue;
                const c = courierData[key];
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${c.logo ? '<img src="'+c.logo+'" width="36">' : ''}</td>
                    <td>${c.name || key}</td><td>${c.total_parcel ?? 0}</td>
                    <td>${c.success_parcel ?? 0}</td><td>${c.cancelled_parcel ?? 0}</td>
                    <td><b>${c.success_ratio ?? 0}%</b></td>`;
                table.appendChild(tr);
            }
            const sr = document.createElement('tr');
            sr.style.background = '#f3f3f3';
            sr.innerHTML = `<td colspan="2"><b>Total Summary</b></td><td><b>${summary.total_parcel || 0}</b></td>
                <td><b>${summary.success_parcel || 0}</b></td><td><b>${summary.cancelled_parcel || 0}</b></td>
                <td><b>${summary.success_ratio || 0}%</b></td>`;
            table.appendChild(sr);

            content.innerHTML = '';
            content.appendChild(table);
        })
        .catch(() => { content.innerHTML = '<p class="p-3">Failed to fetch data.</p>'; });
    });
})();
</script>
@endsection
