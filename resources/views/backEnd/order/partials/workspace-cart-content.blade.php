@php $product_discount = 0; @endphp
@foreach ($cartinfo as $value)
    <tr data-row-id="{{ $value->rowId }}">
        <td>
            <img class="workspace-product-thumb" src="{{ asset($value->options->image) }}">
        </td>
        <td>
            <div class="fw-semibold">{{ $value->name }}</div>
            @php $selectedAttributes = $value->options->selected_attributes ?? []; @endphp
            @if (!empty($selectedAttributes))
                <div class="workspace-variant-list">
                    @foreach ($selectedAttributes as $selectedAttribute)
                        <span class="workspace-variant-chip">{{ $selectedAttribute['attribute'] ?? '' }}: {{ $selectedAttribute['value'] ?? '' }}</span>
                    @endforeach
                </div>
            @else
                <div class="workspace-variant-list">
                    @if ($value->options->product_size)
                        <span class="workspace-variant-chip">Size: {{ $value->options->product_size }}</span>
                    @endif
                    @if ($value->options->product_color)
                        <span class="workspace-variant-chip">Color: {{ $value->options->product_color }}</span>
                    @endif
                </div>
            @endif
        </td>
        <td>
            <div class="qty-cart vcart-qty">
                <div class="quantity">
                    <button type="button" class="minus cart_decrement" value="{{ $value->qty }}" data-id="{{ $value->rowId }}">-</button>
                    <input type="text" value="{{ $value->qty }}" readonly />
                    <button type="button" class="plus cart_increment" value="{{ $value->qty }}" data-id="{{ $value->rowId }}">+</button>
                </div>
            </div>
        </td>
        <td>{{ $value->price }}</td>
        <td class="discount">
            <input type="text" inputmode="decimal" class="product_discount" value="{{ $value->options->product_discount ?? 0 }}"
                placeholder="0.00" data-id="{{ $value->rowId }}" />
        </td>
        <td>{{ ($value->price - ($value->options->product_discount ?? 0)) * $value->qty }}</td>
        <td>
            <button type="button" class="btn btn-danger btn-xs cart_remove" data-id="{{ $value->rowId }}"><i class="fa fa-times"></i></button>
        </td>
    </tr>
    @php
        $product_discount += ($value->options->product_discount ?? 0) * $value->qty;
    @endphp
@endforeach
