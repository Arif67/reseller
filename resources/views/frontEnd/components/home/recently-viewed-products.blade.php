@if ($recentlyViewedProducts->isNotEmpty())
<section class="homeproduct showcase-product-section recently-viewed-simple">
    <div class="custom-container">
        <div class="showcase-product-head">
            <div>
                <span class="showcase-product-kicker">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    Pick Up Where You Left Off
                </span>
                <h5 class="showcase-product-title">Recently Viewed</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <form action="{{ route('recently_viewed.clear') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="showcase-product-link border-0 bg-transparent p-0">
                        Clear History
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </form>
                <a href="{{ route('hotdeals') }}" class="showcase-product-link">
                    Browse All
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="recently_viewed_slider showcase-product-slider owl-carousel">
            @foreach ($recentlyViewedProducts as $value)
            @php
                $oldPrice = $value->display_old_price;
                $newPrice = $value->display_new_price;
                $discount = ($oldPrice && $newPrice && $oldPrice > $newPrice)
                    ? number_format((($oldPrice - $newPrice) * 100) / $oldPrice, 0)
                    : null;
                $primaryImage = $value->primary_media_image ?? optional($value->image)->image ?? 'uploads/logo.png';
            @endphp
            <div class="item">
                <article class="showcase-product-card">
                    <a href="{{ route('product', $value->slug) }}" class="showcase-product-media">
                        <img src="{{ asset($primaryImage) }}" alt="{{ $value->name }}">
                        @if ($discount)
                        <span class="showcase-discount-badge">-{{ $discount }}%</span>
                        @endif
                    </a>
                    <div class="showcase-product-body">
                        <a href="{{ route('product', $value->slug) }}" class="showcase-product-name">
                            {{ Str::limit($value->name, 45) }}
                        </a>
                        <p class="showcase-product-price">
                            @if ($oldPrice)
                            <del>৳ {{ $oldPrice }}</del>
                            @endif
                            <span>৳ {{ $newPrice }}</span>
                        </p>
                        <a href="{{ route('product', $value->slug) }}" class="showcase-product-cta">
                            View Details
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </article>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
