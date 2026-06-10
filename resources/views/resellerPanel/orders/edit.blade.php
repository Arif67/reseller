@extends('resellerPanel.layouts.master')
@section('title', 'Edit Order #' . $order->invoice_id)

@section('content')
    <div class="row align-items-center mb-2">
        <div class="col"><div class="page-title-box"><h4 class="page-title">Edit Order #{{ $order->invoice_id }}</h4></div></div>
        <div class="col-auto">
            <a href="{{ route('reseller.orders.show', $order->id) }}" class="btn btn-sm btn-outline-secondary">
                <i class="mdi mdi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <form action="{{ route('reseller.orders.update', $order->id) }}" method="POST">
        @csrf
        <div class="row">
            {{-- Customer info --}}
            <div class="col-lg-5 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="header-title mb-3">Customer / Delivery</h5>
                        <div class="mb-2">
                            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $order->shipping->name ?? '') }}"
                                class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $order->shipping->phone ?? '') }}"
                                class="form-control @error('phone') is-invalid @enderror" required>
                            @error('phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Full Address <span class="text-danger">*</span></label>
                            <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror" required>{{ old('address', $order->shipping->address ?? '') }}</textarea>
                            @error('address')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Shipping Area</label>
                            <select name="shipping_id" class="form-control">
                                <option value="">No shipping / Self delivery</option>
                                @foreach($shippingCharges as $sc)
                                    <option value="{{ $sc->id }}"
                                        @selected(($order->shipping->area ?? '') === $sc->name)>
                                        {{ $sc->name }} (৳{{ number_format($sc->amount, 0) }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Age chilo: {{ $order->shipping->area ?? 'N/A' }}</small>
                        </div>
                        <div class="mb-1">
                            <label class="form-label">Note</label>
                            <textarea name="note" rows="2" class="form-control">{{ old('note', $order->note) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items --}}
            <div class="col-lg-7 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="header-title mb-3">Products (qty / sell price edit korun)</h5>
                        <div class="table-responsive">
                            <table class="table table-centered mb-0">
                                <thead class="table-light">
                                    <tr><th>Product</th><th>Variant</th><th style="width:100px">Qty</th><th style="width:140px">Sell Price</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderdetails as $d)
                                        <tr>
                                            <td style="font-size:13px;">{{ $d->product_name }}</td>
                                            <td style="font-size:12px;">
                                                @if($d->variant_label)<span class="badge bg-light text-dark">{{ $d->variant_label }}</span>@endif
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $d->id }}][qty]" min="1"
                                                    value="{{ old('items.'.$d->id.'.qty', $d->qty) }}"
                                                    class="form-control form-control-sm" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $d->id }}][sale_price]" min="0" step="any"
                                                    value="{{ old('items.'.$d->id.'.sale_price', $d->sale_price) }}"
                                                    class="form-control form-control-sm" required>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted small mt-2 mb-0">
                            <i class="mdi mdi-information-outline"></i>
                            Sell price + shipping onujayi customer-er total ar apnar margin auto recalculate hobe.
                        </p>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-content-save"></i> Update Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
