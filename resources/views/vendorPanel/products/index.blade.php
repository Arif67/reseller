@extends('vendorPanel.layouts.master')
@section('title', 'My Products')

@section('css')
<style>
    .vp-card { transition: box-shadow .2s; height: 100%; }
    .vp-card:hover { box-shadow: 0 0 18px rgba(0,0,0,.12); }
    .vp-thumb { width: 100%; height: 220px; object-fit: contain; object-position: center; background:#f7f7f7; padding:8px; }
    .vp-name { font-size: 14px; font-weight: 600; min-height: 38px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .vp-variant-list { border-top: 1px solid #eef2f7; margin-top: 10px; padding-top: 8px; }
    .vp-variant-row { border: 1px solid #eef2f7; border-radius: 8px; padding: 6px; margin-bottom: 6px; }
    .vp-variant-name { font-size: 12px; line-height: 1.25; font-weight: 600; }
    .vp-variant-meta { font-size: 11px; color: #64748b; }
    .vp-variant-switch { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 6px; font-size: 12px; }
    .vp-saving { opacity: .55; pointer-events: none; }
</style>
@endsection

@section('content')
    @include('vendorPanel.layouts.mobile_menu')

    <div class="row align-items-center">
        <div class="col">
            <div class="page-title-box"><h4 class="page-title">My Products</h4></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form class="row g-2 mb-2" method="GET">
                <div class="col-sm-4">
                    <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Search product">
                </div>
                <div class="col-sm-4">
                    <button class="btn btn-info">Search</button>
                    <a href="{{ route('vendor.products.index') }}" class="btn btn-light">Reset</a>
                </div>
            </form>
            <div class="alert alert-info py-2">
                <i class="mdi mdi-information"></i> Product admin add kore. Apni stock update, product status, ar variation stop/enable korte parben.
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5">
        @forelse($products as $product)
            <div class="col mb-3">
                <div class="card vp-card mb-0">
                    <div class="position-relative">
                        <img src="{{ asset($product->image->image ?? 'public/uploads/default/user.png') }}"
                            alt="{{ $product->name }}" class="vp-thumb card-img-top">
                        <span class="badge {{ $product->status ? 'bg-success' : 'bg-danger' }} position-absolute"
                            style="top:8px; right:8px;">
                            {{ $product->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="card-body p-2">
                        <p class="vp-name mb-1" title="{{ $product->name }}">{{ $product->name }}</p>
                        <div class="mb-2">
                            <span class="d-block" style="font-size:11px; color:#888;">Your Rate</span>
                            <h5 class="mb-0 text-primary">৳ {{ number_format($product->purchase_price, 0) }}</h5>
                            <small class="text-muted">Retail: ৳{{ number_format($product->new_price, 0) }}</small>
                        </div>

                        <form action="{{ route('vendor.products.stock') }}" method="POST" class="mb-2">
                            @csrf
                            <input type="hidden" name="id" value="{{ $product->id }}">
                            <label class="form-label mb-1" style="font-size:12px;">Stock</label>
                            <div class="input-group input-group-sm">
                                <input type="number" min="0" name="stock" value="{{ $product->stock }}" class="form-control">
                                <button class="btn btn-secondary">Save</button>
                            </div>
                        </form>

                        <form action="{{ route('vendor.products.toggle') }}" method="POST" id="toggle-form-{{ $product->id }}">
                            @csrf
                            <input type="hidden" name="id" value="{{ $product->id }}">
                            <div class="form-check form-switch d-flex align-items-center justify-content-between border rounded px-2 py-1">
                                <label class="form-check-label" for="status-{{ $product->id }}" style="font-size:13px;">
                                    {{ $product->status ? 'On' : 'Off' }}
                                </label>
                                <input class="form-check-input ms-0" type="checkbox" role="switch"
                                    id="status-{{ $product->id }}" {{ $product->status ? 'checked' : '' }}
                                    onchange="document.getElementById('toggle-form-{{ $product->id }}').submit();">
                            </div>
                        </form>

                        @if($product->allVariables->count())
                            <div class="vp-variant-list">
                                <div class="vp-variant-meta mb-1">Variations</div>
                                @foreach($product->allVariables as $variable)
                                    <div class="vp-variant-row" data-variant-id="{{ $variable->id }}">
                                        @csrf
                                        <div>
                                            <div class="vp-variant-name">{{ $variable->variant_label ?: 'Variation #' . $variable->id }}</div>
                                            <div class="vp-variant-meta" data-variant-meta>
                                                Stock: {{ $variable->stock }}
                                                @if($variable->is_available_for_reseller)
                                                    · Reseller e show
                                                @else
                                                    · Reseller e hidden
                                                @endif
                                            </div>
                                        </div>
                                        <div class="vp-variant-switch">
                                            <span data-variant-label class="{{ $variable->vendor_status ? 'text-success' : 'text-danger' }}">
                                                {{ $variable->vendor_status ? 'On' : 'Off' }}
                                            </span>
                                            <div class="form-check form-switch m-0">
                                                <input class="form-check-input vp-variation-toggle" type="checkbox" role="switch"
                                                    data-stock="{{ (int) $variable->stock }}"
                                                    @checked($variable->vendor_status)>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-muted py-4">
                    Apnar kono product nei. Admin product add korle ekhane dekhabe.
                </div></div>
            </div>
        @endforelse
    </div>

    <div class="row">
        <div class="col-12">{{ $products->links('pagination::bootstrap-4') }}</div>
    </div>
@endsection

@section('script')
<script>
(function () {
    const url = "{{ route('vendor.products.variation_toggle') }}";
    const csrf = "{{ csrf_token() }}";

    document.querySelectorAll('.vp-variation-toggle').forEach(function (input) {
        input.addEventListener('change', function () {
            const row = input.closest('[data-variant-id]');
            const label = row.querySelector('[data-variant-label]');
            const meta = row.querySelector('[data-variant-meta]');
            const previous = !input.checked;
            const stock = parseInt(input.dataset.stock || '0');

            row.classList.add('vp-saving');

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    id: row.dataset.variantId,
                    vendor_status: input.checked ? 1 : 0,
                }),
            })
            .then(response => response.json().then(data => {
                if (!response.ok) throw data;
                return data;
            }))
            .then(data => {
                input.checked = !!data.vendor_status;
                label.textContent = data.vendor_status ? 'On' : 'Off';
                label.classList.toggle('text-success', !!data.vendor_status);
                label.classList.toggle('text-danger', !data.vendor_status);
                meta.textContent = 'Stock: ' + stock + (data.available ? ' · Reseller e show' : ' · Reseller e hidden');
                row.classList.remove('vp-saving');
                if (window.toastr) {
                    toastr.success(data.message || 'Variation status updated');
                }
            })
            .catch((data) => {
                input.checked = previous;
                row.classList.remove('vp-saving');
                if (window.toastr) {
                    toastr.error(data?.message || 'Variation status update failed');
                } else {
                    alert(data?.message || 'Variation status update failed');
                }
            });
        });
    });
})();
</script>
@endsection
