@extends('resellerPanel.layouts.master')
@section('title', 'Cart List')

@section('css')
<style>
    .qty-box { display:inline-flex; align-items:center; border:1px solid #dde; border-radius:6px; overflow:hidden; }
    .qty-box button { border:0; background:#f3f4f6; width:30px; height:32px; font-size:16px; line-height:1; cursor:pointer; }
    .qty-box button:hover { background:#e5e7eb; }
    .qty-box input { border:0; width:46px; height:32px; text-align:center; }
    .qty-box input:focus { outline:none; }
    .rc-sell { width:120px; }
    .rc-saving { opacity:.5; }
</style>
@endsection

@section('content')
    <div class="row align-items-center mb-2">
        <div class="col"><div class="page-title-box"><h4 class="page-title">Cart List</h4></div></div>
        <div class="col-auto">
            <a href="{{ route('reseller.products.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="mdi mdi-plus"></i> Add More
            </a>
            @if($items->count())
                <form action="{{ route('reseller.cart.clear') }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Pura cart clear korben?')">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger">Clear Cart</button>
                </form>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Variant</th>
                                    <th>Wholesale</th>
                                    <th style="width:130px">Qty</th>
                                    <th style="width:140px">Sell Price</th>
                                    <th class="text-end">Margin</th>
                                    <th class="text-end">Subtotal</th>
                                    <th style="width:60px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr id="row-{{ $item->id }}" data-id="{{ $item->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ \Illuminate\Support\Str::startsWith($item->image, ['http://','https://']) ? $item->image : asset($item->image ?? 'public/uploads/default/user.png') }}"
                                                    height="42" class="rounded me-2">
                                                <span style="font-size:13px;">{{ $item->product_name }}</span>
                                            </div>
                                        </td>
                                        <td style="min-width:160px;">
                                            @php
                                                $groups = $variantOptions[$item->product_id] ?? collect();
                                                $selectedIds = (array) $item->selected_value_ids;
                                            @endphp
                                            @forelse($groups as $group)
                                                <select class="form-control form-control-sm rc-attr mb-1"
                                                    data-attribute-id="{{ $group['attribute_id'] }}" title="{{ $group['title'] }}">
                                                    @foreach($group['options'] as $option)
                                                        <option value="{{ $option['id'] }}"
                                                            @selected(in_array($option['id'], $selectedIds))>
                                                            {{ $group['title'] }}: {{ $option['title'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @empty
                                                @if($item->variant_label)
                                                    <span class="badge bg-light text-dark">{{ $item->variant_label }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            @endforelse
                                        </td>
                                        <td>৳ {{ number_format($item->wholesale_price, 0) }}</td>
                                        <td>
                                            <div class="qty-box">
                                                <button type="button" class="rc-minus">−</button>
                                                <input type="number" class="rc-qty" value="{{ $item->qty }}" min="1">
                                                <button type="button" class="rc-plus">+</button>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm rc-sell" value="{{ $item->sell_price }}" min="0">
                                        </td>
                                        <td class="text-end text-success fw-bold rc-margin">৳ {{ number_format($item->margin, 0) }}</td>
                                        <td class="text-end rc-subtotal">৳ {{ number_format($item->subtotal, 0) }}</td>
                                        <td class="text-end">
                                            <button type="submit" form="rm-{{ $item->id }}" class="btn btn-sm btn-danger" title="Remove"
                                                onclick="return confirm('Remove this item?')">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-4">
                                        Cart khali. <a href="{{ route('reseller.products.index') }}">Product browse korun</a>।
                                    </td></tr>
                                @endforelse
                            </tbody>
                            @if($items->count())
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="5" class="text-end">Total</td>
                                    <td class="text-end text-success" id="rc-total-margin">৳ {{ number_format($totalMargin, 0) }}</td>
                                    <td class="text-end" id="rc-total-sell">৳ {{ number_format($totalSell, 0) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    @if($items->count())
                        <div class="text-end mt-3">
                            <a href="{{ route('reseller.checkout') }}" class="btn btn-success btn-lg">
                                <i class="mdi mdi-truck-check"></i> Checkout / Place Order
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- remove forms (table-er baire) --}}
    @foreach($items as $item)
        <form id="rm-{{ $item->id }}" action="{{ route('reseller.cart.remove') }}" method="POST" class="d-none">
            @csrf<input type="hidden" name="id" value="{{ $item->id }}">
        </form>
    @endforeach
@endsection

@section('script')
<script>
(function () {
    const updateUrl = "{{ route('reseller.cart.update') }}";
    const csrf = "{{ csrf_token() }}";

    function fmt(n) { return '৳ ' + Number(n).toLocaleString('en-US'); }

    let timers = {};

    function saveRow(row) {
        const id   = row.dataset.id;
        const qty  = parseInt(row.querySelector('.rc-qty').value) || 1;
        const sell = parseFloat(row.querySelector('.rc-sell').value) || 0;

        const payload = { id: id, qty: qty, sell_price: sell };

        // dynamic attribute selects → attribute_values[attribute_id] = value_id
        const attrSelects = row.querySelectorAll('.rc-attr');
        if (attrSelects.length) {
            payload.attribute_values = {};
            attrSelects.forEach(sel => { payload.attribute_values[sel.dataset.attributeId] = sel.value; });
        }

        row.classList.add('rc-saving');

        fetch(updateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
        .then(r => r.json().then(data => {
            if (!r.ok) {
                throw data;
            }
            return data;
        }))
        .then(data => {
            row.querySelector('.rc-qty').value = data.qty;
            row.querySelector('.rc-margin').textContent = fmt(data.margin);
            row.querySelector('.rc-subtotal').textContent = fmt(data.subtotal);
            document.getElementById('rc-total-margin').textContent = fmt(data.total_margin);
            document.getElementById('rc-total-sell').textContent = fmt(data.total_sell);
            row.classList.remove('rc-saving');
        })
        .catch((data) => {
            row.classList.remove('rc-saving');
            if (data && data.message) {
                alert(data.message);
            }
        });
    }

    // debounce — type korar somoy bar bar request na pathai
    function scheduleSave(row) {
        const id = row.dataset.id;
        clearTimeout(timers[id]);
        timers[id] = setTimeout(() => saveRow(row), 350);
    }

    document.querySelectorAll('tr[data-id]').forEach(function (row) {
        const qtyInput = row.querySelector('.rc-qty');
        const sellInput = row.querySelector('.rc-sell');

        row.querySelector('.rc-plus').addEventListener('click', function () {
            qtyInput.value = (parseInt(qtyInput.value) || 1) + 1;
            saveRow(row);
        });
        row.querySelector('.rc-minus').addEventListener('click', function () {
            const v = (parseInt(qtyInput.value) || 1) - 1;
            qtyInput.value = v < 1 ? 1 : v;
            saveRow(row);
        });

        qtyInput.addEventListener('input', () => scheduleSave(row));
        sellInput.addEventListener('input', () => scheduleSave(row));

        // dynamic attribute change hole sathe sathe save
        row.querySelectorAll('.rc-attr').forEach(sel => sel.addEventListener('change', () => saveRow(row)));
    });
})();
</script>
@endsection
