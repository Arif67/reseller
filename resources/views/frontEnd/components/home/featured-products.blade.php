<section class="homeproduct showcase-product-section">
    <div class="custom-container">
        <div class="showcase-product-shell featured-shell">
            <div class="showcase-product-head">
                <div>
                    <span class="showcase-product-kicker">
                        <i class="fa-solid fa-star"></i>
                        Curated Products
                    </span>
                    <h5 class="showcase-product-title">Featured Products</h5>
                </div>
                <a href="{{ route('hotdeals') }}" class="showcase-product-link">
                    Browse All
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="featured_products_slider showcase-product-slider owl-carousel">
                @foreach ($featuredProducts as $value)
                @php
                    $oldPrice = $value->display_old_price;
                    $newPrice = $value->display_new_price;
                    $discount = ($oldPrice && $newPrice && $oldPrice > $newPrice)
                        ? number_format((($oldPrice - $newPrice) * 100) / $oldPrice, 0)
                        : null;
                @endphp
                @php
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
    </div>
</section>
