@php
    $product = $product ?? $value;
    $titleLimit = $titleLimit ?? 56;
    $displayOldPrice = $product->variable_count > 0 && $product->type == 0 ? $product->display_old_price : $product->old_price;
    $displayNewPrice = $product->variable_count > 0 && $product->type == 0 ? $product->display_new_price : $product->new_price;
    $oldPriceNumeric = (float) preg_replace('/[^0-9.]/', '', (string) $displayOldPrice);
    $newPriceNumeric = (float) preg_replace('/[^0-9.]/', '', (string) $displayNewPrice);
    $discountPercent = $oldPriceNumeric > 0 && $newPriceNumeric > 0 && $oldPriceNumeric > $newPriceNumeric
        ? (int) round((($oldPriceNumeric - $newPriceNumeric) / $oldPriceNumeric) * 100)
        : null;
    $primaryImage = $product->primary_media_image ?? optional($product->image)->image ?? 'uploads/logo.png';
    $hoverImage = $product->hover_media_image;
@endphp
<div class="np-item {{ $hoverImage ? 'has-hover-image' : '' }}">
    <article class="np-product-card">
        <a href="{{ route('product', $product->slug) }}" class="np-media">
            <img class="catalog-product-image catalog-product-image--primary" src="{{ asset($primaryImage) }}" alt="{{ $product->name }}" />
            @if ($hoverImage)
            <img class="catalog-product-image catalog-product-image--secondary" src="{{ asset($hoverImage) }}" alt="{{ $product->name }} alternate view" />
            @endif
            @if ($discountPercent)
            <span class="np-discount">-{{ $discountPercent }}%</span>
            @endif
        </a>

        <div class="np-body">
            <a href="{{ route('product', $product->slug) }}" class="np-name">
                {{ \Illuminate\Support\Str::limit($product->name, $titleLimit) }}
            </a>

            <p class="np-price">
                @if ($displayOldPrice)
                <del>৳ {{ $displayOldPrice }}</del>
                @endif
                <span>৳ {{ $displayNewPrice }}</span>
            </p>

            @if($product->variable_count > 0 && $product->type == 0)
            <a href="{{ route('product', $product->slug) }}" class="np-action-btn">Order Now</a>
            @else
            <form action="{{ route('cart.store') }}" method="POST" class="np-action-form">
                @csrf
                <input type="hidden" name="id" value="{{ $product->id }}">
                <input type="hidden" name="qty" value="1">
                <input type="hidden" name="order_now" value="Order Now">
                <button class="np-action-btn" type="submit">Order Now</button>
            </form>
            @endif
        </div>
    </article>
</div>
