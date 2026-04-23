<section class="homeproduct showcase-product-section best-selling-simple">
    <div class="custom-container">
            <div class="showcase-product-head">
                <div>
                    <h5 class="showcase-product-title">Best Selling Product</h5>
                </div>
                <a href="{{ route('hotdeals') }}" class="showcase-product-link">
                    Browse All
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="best_seller_slider showcase-product-slider owl-carousel">
                @foreach ($bestSellingProducts as $value)
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
                            <span class="showcase-best-badge">Best</span>
                        </a>
                        <div class="showcase-product-body">
                            <a href="{{ route('product', $value->slug) }}" class="showcase-product-name">
                                {{ Str::limit($value->name, 45) }}
                            </a>
                            <div class="showcase-product-meta">
                                <span class="showcase-product-rating">
                                    <i class="fa-solid fa-star"></i>
                                    {{ $value->sold_quantity ? number_format(min(4.9, 3.8 + ($value->sold_quantity / 1000)), 1) : '4.6' }}
                                </span>
                                <span class="showcase-product-stock">
                                    @php
                                        $stockTotal = ((int) $value->type === 0)
                                            ? (int) ($value->total_variable_stock ?? 0)
                                            : (int) ($value->stock ?? 0);
                                    @endphp
                                    @if($stockTotal > 0)
                                        Stock {{ number_format($stockTotal) }}
                                    @else
                                        Out of stock
                                    @endif
                                </span>
                            </div>
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
