@extends('resellerPanel.layouts.master')
@section('title', 'My Orders')

@section('css')
<style>
    .rp-order-img { position: relative; width: 52px; height: 52px; cursor: pointer; }
    .rp-order-img img { width: 52px; height: 52px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb; background:#f7f7f7; }
    .rp-order-img:hover img { box-shadow: 0 0 0 2px #0b5345; }
    .rp-img-badge {
        position: absolute; top: -6px; right: -6px; background: #ef4444; color: #fff;
        border-radius: 50%; min-width: 18px; height: 18px; font-size: 10px; font-weight: 800;
        display: flex; align-items: center; justify-content: center; border: 2px solid #fff; padding: 0 4px;
    }
    .rp-modal-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 16px; padding: 16px; }
    .rp-modal-item { border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; background:#fff; position: relative; transition: transform .2s; }
    .rp-modal-item:hover { transform: translateY(-4px); box-shadow: 0 8px 18px rgba(0,0,0,.1); }
    .rp-modal-item img { width: 100%; height: 170px; object-fit: cover; cursor: zoom-in; display: block; }
    .rp-modal-item .rp-qty { position: absolute; top: 10px; right: 10px; background: rgba(15,23,42,.9); color:#fff; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 14px; }
    .rp-modal-item .rp-name { padding: 10px; font-size: 13px; font-weight: 600; text-align: center; border-top: 1px solid #f1f5f9; }
    .rp-modal-item .rp-meta { font-size: 11px; color: #64748b; text-align:center; padding-bottom: 8px; }
    .rp-modal-item .rp-price { font-size: 12px; color:#0b5345; font-weight:700; text-align:center; padding-bottom: 10px; }
    .rp-filter-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; margin-bottom: 16px; }
    .rp-filter-card label { font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 4px; }
    .rp-date-presets { display: flex; gap: 8px; flex-wrap: wrap; }
    .rp-filter-badge { display: inline-flex; align-items: center; gap: 6px; background: #ecfdf5; border: 1px solid #bbf7d0; color: #166534; border-radius: 999px; padding: 6px 12px; font-size: 12px; font-weight: 700; margin-bottom: 12px; }
</style>
@endsection

@section('content')
    <div class="row align-items-center mb-2">
        <div class="col"><div class="page-title-box"><h4 class="page-title">My Orders</h4></div></div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="rp-filter-card">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <div class="rp-date-presets">
                                @foreach($datePresets as $presetValue => $presetLabel)
                                    <a href="{{ route('reseller.orders.index') }}?{{ http_build_query(array_filter([
                                            'status' => $activeFilters['status'] ?? null,
                                            'date' => $presetValue,
                                        ], fn ($value) => filled($value))) }}"
                                        class="btn btn-sm rounded-pill {{ ($activeFilters['date'] ?? '') === $presetValue ? 'btn-success' : 'btn-outline-success' }}">
                                        {{ $presetLabel }}
                                    </a>
                                @endforeach
                            </div>
                            <a href="{{ route('reseller.orders.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">Reset</a>
                        </div>

                        <form method="GET" action="{{ route('reseller.orders.index') }}" class="row g-2 align-items-end">
                            <input type="hidden" name="date" id="rp-order-date-preset" value="{{ $activeFilters['date'] ?? '' }}">
                            <div class="col-md-3">
                                <label for="rp-order-status">Status</label>
                                <select id="rp-order-status" name="status" class="form-control">
                                    <option value="">All Status</option>
                                    @foreach($orderStatuses as $status)
                                        <option value="{{ $status->id }}" @selected((string) ($activeFilters['status'] ?? '') === (string) $status->id)>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="rp-order-start-date">Start Date</label>
                                <input id="rp-order-start-date" type="date" name="start_date" class="form-control rp-custom-date" value="{{ $activeFilters['start_date'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label for="rp-order-end-date">End Date</label>
                                <input id="rp-order-end-date" type="date" name="end_date" class="form-control rp-custom-date" value="{{ $activeFilters['end_date'] ?? '' }}">
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button class="btn btn-success flex-fill">Filter</button>
                                <a href="{{ route('reseller.orders.index') }}" class="btn btn-secondary flex-fill">Clear</a>
                            </div>
                        </form>
                    </div>

                    @if(($activeFilters['status'] ?? null) || ($activeFilters['start_date'] ?? null) || ($activeFilters['end_date'] ?? null) || ($activeFilters['date'] ?? null))
                        <div class="rp-filter-badge">
                            <i class="mdi mdi-filter"></i>
                            <span>
                                Filter:
                                @if($activeFilters['status'] ?? null)
                                    {{ optional($orderStatuses->firstWhere('id', $activeFilters['status']))->name ?? 'Selected Status' }}
                                @else
                                    All Status
                                @endif
                                @if(($activeFilters['date_label'] ?? null) || ($activeFilters['start_date'] ?? null) || ($activeFilters['end_date'] ?? null))
                                    · {{ ($activeFilters['date_label'] ?? null) ?: (($activeFilters['start_date'] ?? '...') . ' to ' . ($activeFilters['end_date'] ?? '...')) }}
                                @endif
                            </span>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice</th><th>Customer</th><th>Products</th><th>Customer Pays</th>
                                    <th>My Margin</th><th>Customer Report</th><th>Status</th><th>Date</th><th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    @php
                                        $details = $order->orderdetails->map(function ($d) {
                                            $img = $d->productVariable?->primary_media_image
                                                ?? $d->product?->primary_media_image
                                                ?? $d->image?->image
                                                ?? 'public/uploads/default/user.png';
                                            $imgSrc = \Illuminate\Support\Str::startsWith($img, ['http://','https://']) ? $img : asset($img);
                                            return [
                                                'image'   => $imgSrc,
                                                'name'    => $d->product_name,
                                                'qty'     => $d->qty,
                                                'variant' => $d->variant_label,
                                                'price'   => (float) $d->sale_price,
                                            ];
                                        });
                                        $firstImg = $details->first()['image'] ?? asset('public/uploads/default/user.png');
                                        $totalQty = $order->orderdetails->sum('qty');
                                        $sh = $order->shipping;
                                        $summary = [
                                            'invoice'  => $order->invoice_id,
                                            'customer' => $sh ? ($sh->name . ' | ' . $sh->phone) : 'N/A',
                                            'address'  => $sh ? trim($sh->address . ' — ' . ($sh->area ?? '')) : 'N/A',
                                            'subtotal' => $order->amount - $order->shipping_charge,
                                            'shipping' => $order->shipping_charge,
                                            'total'    => $order->amount,
                                        ];
                                    @endphp
                                    <tr>
                                        <td><strong>#{{ $order->invoice_id }}</strong></td>
                                        <td style="min-width:170px;">
                                            <div class="fw-bold" style="font-size:13px;">{{ $sh->name ?? '—' }}</div>
                                            <div class="small text-muted">{{ $sh->phone ?? '' }}</div>
                                            <div class="small text-muted" style="max-width:220px;">
                                                {{ \Illuminate\Support\Str::limit($sh->address ?? '', 50) }}
                                                @if($sh && $sh->area), <span class="text-dark">{{ $sh->area }}</span>@endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="rp-order-img" role="button"
                                                data-details='@json($details)' data-summary='@json($summary)'>
                                                <img src="{{ $firstImg }}" alt="">
                                                @if($totalQty > 1)<span class="rp-img-badge">{{ $totalQty }}</span>@endif
                                            </div>
                                        </td>
                                        <td>৳ {{ number_format($order->amount, 0) }}</td>
                                        <td class="text-success fw-bold">৳ {{ number_format($order->reseller_margin, 0) }}</td>
                                        @php $rep = $reports[$order->shipping->phone ?? ''] ?? null; @endphp
                                        <td class="rp-cust-report" data-phone="{{ $order->shipping->phone ?? '' }}"
                                            data-ready="{{ $rep ? '1' : '0' }}">
                                            @if($rep)
                                                <span class="badge bg-{{ $rep['color'] }}">{{ $rep['label'] }}</span>
                                                <div class="small text-muted mt-1">✓ {{ $rep['success'] }} / ✗ {{ $rep['cancelled'] }} &nbsp; ({{ $rep['ratio'] }}%)</div>
                                            @else
                                                <span class="text-muted small">Checking…</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $sm = [
                                                    1 => ['Pending','warning'], 2 => ['Processing','info'], 3 => ['On Hold','secondary'],
                                                    4 => ['Confirmed','primary'], 5 => ['Shipped','info'], 6 => ['Out for Delivery','info'],
                                                    7 => ['Delivered','success'], 8 => ['Stockout','dark'], 9 => ['Cancelled','danger'],
                                                    10 => ['Refunded','dark'], 11 => ['Returned','dark'], 12 => ['Failed','danger'],
                                                ];
                                                [$n,$c] = $sm[$order->order_status] ?? ['Unknown','secondary'];
                                                $n = $order->status?->name ?? $n;
                                            @endphp
                                            <span class="badge bg-{{ $c }}">{{ $n }}</span>
                                        </td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('reseller.orders.show', $order->id) }}" class="btn btn-sm btn-outline-success">
                                                <i class="mdi mdi-eye"></i> Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9" class="text-center text-muted py-4">Kono order nei.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $orders->links('pagination::bootstrap-4') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Order items modal --}}
    <div class="modal fade" id="rpOrderItemsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="mdi mdi-shopping me-1"></i> Order Items — #<span id="rpModalInvoice"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="px-3 pt-2">
                    <div class="alert alert-light border mb-0 py-2">
                        <div><i class="mdi mdi-account"></i> <span id="rpModalCustomer"></span></div>
                        <div class="text-muted small"><i class="mdi mdi-map-marker"></i> <span id="rpModalAddress"></span></div>
                    </div>
                </div>
                <div class="modal-body p-0">
                    <div id="rpModalGrid" class="rp-modal-grid"></div>
                </div>
                <div class="modal-footer justify-content-between">
                    <span class="small text-muted">Subtotal: ৳<span id="rpModalSubtotal">0</span> &nbsp;|&nbsp; Shipping: ৳<span id="rpModalShipping">0</span></span>
                    <span class="fw-bold">Customer Pays: ৳<span id="rpModalTotal">0</span></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Image zoom modal --}}
    <div class="modal fade" id="rpZoomModal" tabindex="-1" style="z-index:1065;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body p-0 text-center">
                    <img id="rpZoomImg" src="" alt="" style="max-width:100%; max-height:88vh; border-radius:8px;">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
// Order items modal (image click → admin-er moto grid + zoom)
(function () {
    const itemsModalEl = document.getElementById('rpOrderItemsModal');
    const zoomModalEl  = document.getElementById('rpZoomModal');
    if (!itemsModalEl) return;
    const itemsModal = new bootstrap.Modal(itemsModalEl);
    const zoomModal  = new bootstrap.Modal(zoomModalEl);
    const grid = document.getElementById('rpModalGrid');

    function money(n) { return Number(n || 0).toLocaleString('en-US'); }

    document.querySelectorAll('.rp-order-img').forEach(function (el) {
        el.addEventListener('click', function () {
            let details = [], summary = {};
            try { details = JSON.parse(this.dataset.details || '[]'); } catch (e) {}
            try { summary = JSON.parse(this.dataset.summary || '{}'); } catch (e) {}

            document.getElementById('rpModalInvoice').textContent  = summary.invoice || '';
            document.getElementById('rpModalCustomer').textContent = summary.customer || '';
            document.getElementById('rpModalAddress').textContent  = summary.address || '';
            document.getElementById('rpModalSubtotal').textContent = money(summary.subtotal);
            document.getElementById('rpModalShipping').textContent = money(summary.shipping);
            document.getElementById('rpModalTotal').textContent    = money(summary.total);

            grid.innerHTML = '';
            details.forEach(function (it) {
                const meta = it.variant ? '<div class="rp-meta">' + it.variant + '</div>' : '';
                grid.insertAdjacentHTML('beforeend',
                    '<div class="rp-modal-item">' +
                        '<img src="' + it.image + '" alt="" class="rp-zoom" title="' + (it.name || '') + '">' +
                        '<span class="rp-qty">Qty: ' + it.qty + '</span>' +
                        '<div class="rp-name">' + (it.name || '') + '</div>' +
                        meta +
                        '<div class="rp-price">৳ ' + money(it.price) + '</div>' +
                    '</div>'
                );
            });

            itemsModal.show();
        });
    });

    // image zoom
    grid.addEventListener('click', function (e) {
        const img = e.target.closest('.rp-zoom');
        if (!img) return;
        document.getElementById('rpZoomImg').src = img.src;
        zoomModal.show();
    });
})();

(function () {
    const endpoint = "{{ route('reseller.orders.fraud_check') }}";
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const cells = Array.from(document.querySelectorAll('.rp-cust-report'));
    const TTL = 15 * 60 * 1000; // 15 min — server cache-er sathe match

    // localStorage cache helpers
    function lsGet(phone) {
        try {
            const raw = localStorage.getItem('rp_cust:' + phone);
            if (!raw) return null;
            const obj = JSON.parse(raw);
            if (!obj.ts || (Date.now() - obj.ts) > TTL) { localStorage.removeItem('rp_cust:' + phone); return null; }
            return obj.rep;
        } catch (e) { return null; }
    }
    function lsSet(phone, rep) {
        try { localStorage.setItem('rp_cust:' + phone, JSON.stringify({ ts: Date.now(), rep })); } catch (e) {}
    }

    function statusBadge(ratio, total) {
        if (total <= 0) return ['New Customer', 'secondary'];
        if (ratio >= 80) return ['Good Customer', 'success'];
        if (ratio >= 50) return ['Average', 'warning'];
        return ['Risky Customer', 'danger'];
    }
    function repHtml(rep) {
        return '<span class="badge bg-' + rep.color + '">' + rep.label + '</span>' +
            '<div class="small text-muted mt-1">✓ ' + rep.success + ' / ✗ ' + rep.cancelled +
            ' &nbsp; (' + rep.ratio + '%)</div>';
    }
    function render(cellsArr, html) { cellsArr.forEach(c => c.innerHTML = html); }

    // phone onujayi cell group kori; ja already server-render (data-ready=1) ba localStorage-e ache, AJAX baad
    const groups = {};
    const toFetch = [];
    cells.forEach(cell => {
        const phone = (cell.dataset.phone || '').trim();
        if (!phone) { cell.innerHTML = '<span class="text-muted small">No phone</span>'; return; }
        (groups[phone] = groups[phone] || []).push(cell);
    });

    Object.keys(groups).forEach(phone => {
        const cellsArr = groups[phone];
        if (cellsArr[0].dataset.ready === '1') return;          // server warm-cache theke already render
        const cached = lsGet(phone);
        if (cached) { render(cellsArr, repHtml(cached)); return; } // client cache hit — instant
        toFetch.push(phone);
    });

    function fetchPhone(phone) {
        return fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ phone: String(phone) }),
        })
        .then(res => res.json().then(data => ({ ok: res.ok, data })))
        .then(({ ok, data }) => {
            const cellsArr = groups[phone];
            if (!ok) { render(cellsArr, '<span class="text-muted small">' + (data.message || 'N/A') + '</span>'); return; }

            const courierData = data?.courierData || data?.data?.courierData || data?.data || data?.result || null;
            const summary = (courierData && (courierData.summary)) || data?.summary || data?.data?.summary || {};

            const total = parseInt(summary.total_parcel || 0);
            const success = parseInt(summary.success_parcel || 0);
            const cancelled = parseInt(summary.cancelled_parcel || 0);
            let ratio = parseFloat(summary.success_ratio);
            if (isNaN(ratio)) ratio = total > 0 ? Math.round((success / total) * 100) : 0;

            const [label, color] = statusBadge(ratio, total);
            const rep = { label, color, success, cancelled, ratio };
            lsSet(phone, rep);
            render(cellsArr, repHtml(rep));
        })
        .catch(() => render(groups[phone], '<span class="text-muted small">N/A</span>'));
    }

    // sequential — external API rate-limit safe
    (function next(i) {
        if (i >= toFetch.length) return;
        fetchPhone(toFetch[i]).finally(() => next(i + 1));
    })(0);
})();

(function () {
    const datePreset = document.getElementById('rp-order-date-preset');
    if (!datePreset) return;
    document.querySelectorAll('.rp-custom-date').forEach(function (input) {
        input.addEventListener('change', function () {
            datePreset.value = '';
        });
    });
})();
</script>
@endsection
