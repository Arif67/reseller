@extends('backEnd.layouts.master')
@section('title','Hub Receiving')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-title-box"><h4 class="page-title">Hub Receiving — Vendor Collections</h4></div>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.hub.stock') }}" class="btn btn-outline-primary btn-sm">
                <i class="fe-box"></i> Hub Stock
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row g-2 mb-3" method="GET">
                        <div class="col-sm-4">
                            <select name="vendor_id" class="form-control" onchange="this.form.submit()">
                                <option value="">All Vendors</option>
                                @foreach($vendors as $v)
                                    <option value="{{ $v->id }}" @selected(request('vendor_id')==$v->id)>{{ $v->shop_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <a href="{{ route('admin.hub.receiving') }}" class="btn btn-light">Reset</a>
                        </div>
                    </form>

                    <p class="text-muted">Items vendors have marked <b>Collected</b>, waiting to be received at the hub. Receiving adds the qty to hub stock.</p>

                    <div class="table-responsive">
                        <table class="table table-striped table-centered nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Image</th><th>Vendor / Shop</th><th>Product</th>
                                    <th>Size</th><th>Qty</th><th>Invoice</th><th>Collected</th>
                                    <th style="width:160px">Action</th>
                                </tr>
                            </thead>
                            <tbody id="receiving-list">
                                @forelse($items as $detail)
                                    @php
                                        $img = $detail->image->image
                                            ?? optional(optional($detail->product)->image)->image
                                            ?? 'public/uploads/default/product.png';
                                    @endphp
                                    <tr class="receiving-row">
                                        <td><img src="{{ asset($img) }}" height="40" class="rounded" alt=""></td>
                                        <td>{{ optional(optional($detail->product)->vendor)->shop_name ?? '—' }}</td>
                                        <td>{{ $detail->product_name }}</td>
                                        <td>{{ $detail->product_size ?: 'N/A' }}</td>
                                        <td><span class="badge bg-info">{{ $detail->qty }}</span></td>
                                        <td>{{ optional($detail->order)->invoice_id ?? '—' }}</td>
                                        <td><small class="text-muted">{{ optional($detail->vendor_collected_at)?->format('M-d h:i A') }}</small></td>
                                        <td>
                                            <form action="{{ route('admin.hub.receive') }}" method="POST" class="receive-form">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $detail->id }}">
                                                <button class="btn btn-sm btn-success">
                                                    <i class="fe-check"></i> Received
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="receiving-empty"><td colspan="8" class="text-center text-muted py-4">No items waiting to be received.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $items->links('pagination::bootstrap-4') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = '{{ csrf_token() }}';
    document.querySelectorAll('.receive-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const row = form.closest('.receiving-row');
            const btn = form.querySelector('button');
            const id  = form.querySelector('input[name="id"]').value;
            btn.disabled = true;
            btn.innerHTML = '<i class="fe-loader"></i>';

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
            .then(r => r.json())
            .then(function (data) {
                if (data.status !== 'success') throw new Error();
                row.style.transition = 'opacity .25s';
                row.style.opacity = '0';
                setTimeout(function () {
                    row.remove();
                    if (!document.querySelector('#receiving-list .receiving-row')) {
                        document.querySelector('#receiving-list').innerHTML =
                            '<tr><td colspan="8" class="text-center text-muted py-4">No items waiting to be received.</td></tr>';
                    }
                }, 250);
                if (window.toastr) toastr.success(data.message || 'Received');
            })
            .catch(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="fe-check"></i> Received';
                if (window.toastr) toastr.error('Could not receive. Try again.');
            });
        });
    });
});
</script>
@endsection
