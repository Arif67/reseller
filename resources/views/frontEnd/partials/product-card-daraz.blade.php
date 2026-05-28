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
    $hoverImage   = $product->hover_media_image;
    // Use pre-loaded aggregate if available, otherwise query DB once
    if (isset($product->active_reviews_avg_ratting)) {
        $avgRating   = round((float) $product->active_reviews_avg_ratting, 1);
        $reviewCount = (int) ($product->active_reviews_count ?? 0);
    } else {
        $avgRating   = round((float) \App\Models\Review::where('product_id', $product->id)->where('status','active')->avg('ratting'), 1);
        $reviewCount = (int) \App\Models\Review::where('product_id', $product->id)->where('status','active')->count();
    }
@endphp

<div class="dz-card-item {{ $hoverImage ? 'has-hover-image' : '' }}">
    <a href="{{ route('product', $product->slug) }}" class="dz-card">

        {{-- Image --}}
        <div class="dz-card-img">
            <img class="dz-card-img-primary" src="{{ asset($primaryImage) }}" alt="{{ $product->name }}">
            @if($hoverImage)
                <img class="dz-card-img-secondary" src="{{ asset($hoverImage) }}" alt="{{ $product->name }}">
            @endif
        </div>

        {{-- Body --}}
        <div class="dz-card-body">

            {{-- Title --}}
            <p class="dz-card-title">{{ \Illuminate\Support\Str::limit($product->name, $titleLimit) }}</p>

            {{-- Price row — all on one line --}}
            <div class="dz-card-price-row">
                <span class="dz-card-price">৳{{ $displayNewPrice }}</span>
                @if($displayOldPrice && $oldPriceNumeric > $newPriceNumeric)
                    <span class="dz-card-old-price">৳{{ $displayOldPrice }}</span>
                @endif
                @if($discountPercent)
                    <span class="dz-card-discount">-{{ $discountPercent }}%</span>
                @endif
            </div>

            {{-- Rating --}}
            @if($avgRating > 0)
            <div class="dz-card-rating">
                <div class="dz-card-stars">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($avgRating))
                            <i class="fa-solid fa-star"></i>
                        @elseif($i - $avgRating < 1)
                            <i class="fa-solid fa-star-half-stroke"></i>
                        @else
                            <i class="fa-regular fa-star"></i>
                        @endif
                    @endfor
                </div>
                @if($reviewCount > 0)
                    <span class="dz-card-review-count">({{ $reviewCount }})</span>
                @endif
            </div>
            @endif

        </div>
    </a>
</div>
