@if(($exactProduct ?? null) || ($exactVariant ?? null) || (($products ?? collect())->count() > 0))
<div class="search_product">
    <ul>
        @if($exactVariant ?? false)
            @php
                $product = $exactVariant->product;
                $rowOldPrice = $product && $product->usesSharedVariationPricing() ? $product->old_price : $exactVariant->old_price;
                $rowNewPrice = $product && $product->usesSharedVariationPricing() ? $product->new_price : $exactVariant->new_price;
            @endphp
            @if($product)
            <li>
                <a href="javascript:void(0)"
                   class="cart_add"
                   data-id="{{$product->id}}"
                   data-size="{{$exactVariant->size}}"
                   data-color="{{$exactVariant->color}}"
                   data-weight="{{$exactVariant->weight}}"
                   data-model="{{$exactVariant->model}}"
                   data-variant-barcode="{{$exactVariant->barcode}}"
                   data-auto-add="1"
                   data-auto-key="variant-{{$exactVariant->id}}">
                    <p class="name">
                        {{$product->name}}
                        @if($exactVariant->size) - Size: {{$exactVariant->size}} @endif
                        @if($exactVariant->color) - Color: {{$exactVariant->color}} @endif
                        @if($exactVariant->weight) - Weight: {{$exactVariant->weight}} @endif
                        @if($exactVariant->model) - Model: {{$exactVariant->model}} @endif
                        @if($exactVariant->barcode) - Barcode: {{$exactVariant->barcode}} @endif
                        (Stock: {{$exactVariant->stock}})
                    </p>
                    <p class="price">
                        ৳{{$rowNewPrice}}
                        @if($rowOldPrice)
                            <del>৳{{$rowOldPrice}}</del>
                        @endif
                    </p>
                </a>
            </li>
            @endif
        @elseif($exactProduct ?? false)
            <li>
                <a href="javascript:void(0)"
                   class="cart_add"
                   data-id="{{$exactProduct->id}}"
                   data-auto-add="1"
                   data-auto-key="product-{{$exactProduct->id}}">
                    <p class="name">{{$exactProduct->name}} @if($exactProduct->pro_barcode) - Barcode: {{$exactProduct->pro_barcode}} @endif</p>
                    <p class="price">
                        ৳{{$exactProduct->new_price}}
                        @if($exactProduct->old_price)
                            <del>৳{{$exactProduct->old_price}}</del>
                        @endif
                    </p>
                </a>
            </li>
        @else
            @foreach($products as $value)
                @if($value->type == 1)
                <li>
                    <a href="javascript:void(0)" class="cart_add" data-id="{{$value->id}}">
                        <p class="name">{{$value->name}} @if($value->pro_barcode) - Barcode: {{$value->pro_barcode}} @endif</p>
                        <p class="price">
                            ৳{{$value->new_price}}
                            @if($value->old_price)
                                <del>৳{{$value->old_price}}</del>
                            @endif
                        </p>
                    </a>
                </li>
                @else
                    @foreach($value->variables as $variable)
                    @php
                        $rowOldPrice = $value->usesSharedVariationPricing() ? $value->old_price : $variable->old_price;
                        $rowNewPrice = $value->usesSharedVariationPricing() ? $value->new_price : $variable->new_price;
                    @endphp
                    <li>
                        <a href="javascript:void(0)"
                           class="cart_add"
                           data-id="{{$value->id}}"
                           data-size="{{$variable->size}}"
                           data-color="{{$variable->color}}"
                           data-weight="{{$variable->weight}}"
                           data-model="{{$variable->model}}"
                           @if($variable->barcode) data-variant-barcode="{{$variable->barcode}}" @endif>
                            <p class="name">
                                {{$value->name}}
                                @if($variable->size) - Size: {{$variable->size}} @endif
                                @if($variable->color) - Color: {{$variable->color}} @endif
                                @if($variable->weight) - Weight: {{$variable->weight}} @endif
                                @if($variable->model) - Model: {{$variable->model}} @endif
                                @if($variable->barcode) - Barcode: {{$variable->barcode}} @endif
                                (Stock: {{$variable->stock}})
                            </p>
                            <p class="price">
                                ৳{{$rowNewPrice}}
                                @if($rowOldPrice)
                                    <del>৳{{$rowOldPrice}}</del>
                                @endif
                            </p>
                        </a>
                    </li>
                    @endforeach
                @endif
            @endforeach
        @endif
    </ul>
</div>
@endif

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function cart_content() {
    const context = "{{ $context }}";
    fetch("{{route('admin.order.cart_content')}}?context=" + context)
        .then(res => res.text())
        .then(html => {
            document.getElementById("cartTable").innerHTML = html;
        });
}

function cart_details() {
    const context = "{{ $context }}";
    fetch("{{route('admin.order.cart_details')}}?context=" + context)
        .then(res => res.text())
        .then(html => {
            document.getElementById("cart_details").innerHTML = html;
        });
}

function search_clear() {
    const context = "{{ $context }}";
    fetch("{{route('admin.livesearch')}}?keyword=&context=" + context)
        .then(res => res.text())
        .then(html => {
            document.querySelector(".search_result").innerHTML = html;
            document.querySelector(".search_click").value = "";
        });
}

document.removeEventListener("click", window.__cartAddHandler);
window.__cartAddHandler = function (e) {
    const target = e.target.closest(".cart_add");
    if (!target) return;

    e.preventDefault();

    const id = target.dataset.id;
    const color = target.dataset.color || '';
    const size = target.dataset.size || '';
    const weight = target.dataset.weight || '';
    const model = target.dataset.model || '';
    const variantBarcode = target.dataset.variantBarcode || '';
    const context = "{{ $context }}";

    fetch(`{{route('admin.order.cart_add')}}?id=${id}&context=${context}&color=${encodeURIComponent(color)}&size=${encodeURIComponent(size)}&weight=${encodeURIComponent(weight)}&model=${encodeURIComponent(model)}&variant_barcode=${encodeURIComponent(variantBarcode)}`)
        .then(res => res.json())
        .then(() => {
            cart_content();
            cart_details();
            search_clear();
        });
};
document.addEventListener("click", window.__cartAddHandler);

const autoTarget = document.querySelector('.cart_add[data-auto-add="1"]');
if (autoTarget) {
    const autoKey = autoTarget.dataset.autoKey || 'default';
    if (window.__adminPosAutoAddKey !== autoKey) {
        window.__adminPosAutoAddKey = autoKey;
        setTimeout(() => autoTarget.click(), 60);
    }
}
</script>
