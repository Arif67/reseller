@extends('resellerPanel.layouts.master')
@section('title', 'Checkout')

@section('content')
    <div class="row align-items-center mb-2">
        <div class="col"><div class="page-title-box"><h4 class="page-title">Checkout — Customer-er Order</h4></div></div>
        <div class="col-auto">
            <a href="{{ route('reseller.cart.index') }}" class="btn btn-sm btn-outline-secondary">← Back to Cart</a>
        </div>
    </div>

    <form action="{{ route('reseller.orders.place') }}" method="POST">
        @csrf
        <div class="row">
            {{-- Customer info --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Customer Information</h4>
                        <div class="mb-3">
                            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" required>
                            @error('phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Address <span class="text-danger">*</span></label>
                            <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror" required>{{ old('address') }}</textarea>
                            @error('address')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Shipping Area</label>
                            <select name="shipping_id" class="form-control" id="shipping-select">
                                <option value="" data-amount="0">No shipping / Self delivery</option>
                                @foreach($shippingCharges as $sc)
                                    <option value="{{ $sc->id }}" data-amount="{{ $sc->amount }}">
                                        {{ $sc->name }} (৳{{ number_format($sc->amount, 0) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Note (optional)</label>
                            <textarea name="note" rows="2" class="form-control">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order summary --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Order Summary</h4>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Product</th><th>Qty</th><th class="text-end">Sell</th></tr></thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td style="font-size:13px;">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ \Illuminate\Support\Str::startsWith($item->image, ['http://','https://']) ? $item->image : asset($item->image ?? 'public/uploads/default/user.png') }}"
                                                        height="42" class="rounded me-2">
                                                    <span>
                                                        {{ $item->product_name }}
                                                        @if($item->variant_label)<br><small class="text-muted">{{ $item->variant_label }}</small>@endif
                                                    </span>
                                                </div>
                                            </td>
                                            <td>{{ $item->qty }}</td>
                                            <td class="text-end">৳ {{ number_format($item->subtotal, 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between"><span>Items Total</span><span>৳ {{ number_format($totalSell, 0) }}</span></div>
                        <div class="d-flex justify-content-between"><span>Shipping</span><span id="sum-shipping">৳ 0</span></div>
                        <div class="d-flex justify-content-between fw-bold fs-5 mt-1"><span>Customer Pays</span><span id="sum-total">৳ {{ number_format($totalSell, 0) }}</span></div>
                        <div class="d-flex justify-content-between text-success mt-2"><span>Apnar Margin (profit)</span><span>৳ {{ number_format($totalMargin, 0) }}</span></div>

                        <button type="submit" class="btn btn-success btn-lg w-100 mt-3">
                            <i class="mdi mdi-check-circle"></i> Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
<script>
(function(){
    const itemsTotal = {{ $totalSell }};
    const sel = document.getElementById('shipping-select');
    function fmt(n){ return '৳ ' + Number(n).toLocaleString('en-US'); }
    function recalc(){
        const amt = parseFloat(sel.options[sel.selectedIndex].dataset.amount) || 0;
        document.getElementById('sum-shipping').textContent = fmt(amt);
        document.getElementById('sum-total').textContent = fmt(itemsTotal + amt);
    }
    sel.addEventListener('change', recalc);
})();
</script>
@endsection
