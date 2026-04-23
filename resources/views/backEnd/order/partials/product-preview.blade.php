@php
    $thumb = asset($product->primary_media_image ?? optional($product->image)->image ?? 'uploads/logo.png');
    $variants = $product->variables ?? collect();
    $hasVariants = (int) $product->type === 0 && $variants->count() > 0;
    $shortDescription = trim(strip_tags((string) ($product->description ?? '')));
    $gallery = collect([$product->primary_media_image ?? optional($product->image)->image ?? null])
        ->merge($product->relationLoaded('media') ? $product->media->pluck('path')->all() : [])
        ->merge($product->relationLoaded('images') ? $product->images->pluck('image')->all() : [])
        ->filter()
        ->unique()
        ->values();
    $displayStock = $hasVariants ? (int) $variants->sum('stock') : (int) ($product->stock ?? 0);
    $stockBadgeClass = $displayStock > 10 ? 'bg-success' : ($displayStock > 0 ? 'bg-warning text-dark' : 'bg-danger');
@endphp
<div class="product-preview-panel">
    <div class="row g-3">
        <div class="col-md-5">
            <div class="product-preview-image">
                <img src="{{ $thumb }}" alt="{{ $product->name }}" data-preview-main-image>
            </div>
            @if ($gallery->count() > 1)
                <div class="product-preview-gallery">
                    @foreach ($gallery->take(5) as $galleryImage)
                        <button type="button" class="product-preview-gallery-item js-preview-gallery-item"
                            data-preview-image="{{ asset($galleryImage) }}">
                            <img src="{{ asset($galleryImage) }}" alt="{{ $product->name }}">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="col-md-7">
            <div class="product-preview-hero">
                <div>
                    <div class="product-preview-title">{{ $product->name }}</div>
                    <div class="product-preview-price">
                        ৳{{ number_format((float) $product->new_price, 2, '.', '') }}
                        @if ($product->old_price)
                            <del>৳{{ number_format((float) $product->old_price, 2, '.', '') }}</del>
                        @endif
                    </div>
                </div>
                <div class="text-end">
                    @if ($hasVariants)
                        <div class="badge bg-warning text-dark">Variable Product</div>
                    @else
                        <div class="badge bg-success">Ready Stock</div>
                    @endif
                </div>
            </div>
            <div class="product-preview-badges">
                @if ($product->pro_barcode)
                    <span class="badge bg-light text-dark">Barcode: {{ $product->pro_barcode }}</span>
                @endif
                <span class="badge {{ $stockBadgeClass }}">Stock: {{ $displayStock }}</span>
                @if ($hasVariants)
                    <span class="badge bg-info text-dark">{{ $variants->count() }} Variations</span>
                @else
                    <span class="badge bg-success">Single Item</span>
                @endif
            </div>
            <div class="alert alert-info border-0 mt-3 mb-0 py-2 px-3">
                Barcode scan korle direct add hoy, na hole niche theke variation tap kore add korte parben.
            </div>
            @if ($shortDescription)
                <p class="product-preview-desc mt-3">{{ \Illuminate\Support\Str::limit($shortDescription, 220) }}</p>
            @endif
            <div class="mt-3">
                @if ($hasVariants)
                    <div class="product-preview-section-title">Choose Variation</div>
                    <div class="preview-variant-list">
                        @foreach ($variants as $variant)
                            @php
                                $label = collect([$variant->size, $variant->color, $variant->model, $variant->weight])->filter()->implode(' / ');
                                $label = $label ?: ('Variant #' . $variant->id);
                                $variantStock = (int) $variant->stock;
                            @endphp
                            <button type="button"
                                class="btn btn-outline-dark btn-sm preview-variant-item js-cart-add"
                                data-id="{{ $product->id }}"
                                data-variant-id="{{ $variant->id }}"
                                data-size="{{ $variant->size }}"
                                data-color="{{ $variant->color }}"
                                data-model="{{ $variant->model }}"
                                data-weight="{{ $variant->weight }}"
                                data-variant-barcode="{{ $variant->barcode }}"
                                data-stock="{{ $variantStock }}">
                                <span class="variant-selected-indicator"><i class="fa fa-check"></i></span>
                                <span class="d-block fw-semibold">{{ $label }}</span>
                                <div class="variant-attribute-pills">
                                    @if ($variant->size)
                                        <span class="variant-attr-pill">Size: {{ $variant->size }}</span>
                                    @endif
                                    @if ($variant->color)
                                        <span class="variant-attr-pill">
                                            <span class="variant-color-dot" style="background: {{ $variant->color }};"></span>
                                            {{ $variant->color }}
                                        </span>
                                    @endif
                                    @if ($variant->model)
                                        <span class="variant-attr-pill">Model: {{ $variant->model }}</span>
                                    @endif
                                    @if ($variant->weight)
                                        <span class="variant-attr-pill">Weight: {{ $variant->weight }}</span>
                                    @endif
                                </div>
                                <div class="variant-meta-row">
                                    <small class="text-muted">Stock: {{ $variantStock }}</small>
                                    @if ($variantStock <= 5)
                                        <span class="badge bg-danger-subtle text-danger">Low stock</span>
                                    @endif
                                </div>
                                @if ($variant->barcode)
                                    <small class="d-block text-muted">Barcode: {{ $variant->barcode }}</small>
                                @endif
                                <small class="d-block text-primary fw-bold">৳{{ number_format((float) ($product->usesSharedVariationPricing() ? $product->new_price : ($variant->new_price ?? $product->new_price)), 2, '.', '') }}</small>
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="product-preview-actions">
                        <button type="button" class="btn btn-success js-cart-add" data-id="{{ $product->id }}">
                            Add to Cart
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="product-preview-sticky">
        <div class="product-preview-sticky-text">
            @if ($hasVariants)
                Select any variation above to add directly to cart.
            @else
                Tap add to cart to send this item to checkout.
            @endif
        </div>
        <div class="d-flex gap-2">
            <div class="preview-qty-wrap">
                <label class="form-label mb-1 small text-muted">Qty</label>
                <input type="number" min="1" step="1" value="1" class="form-control preview-qty-input">
            </div>
            @if ($hasVariants)
                <button type="button" class="btn btn-primary js-preview-scroll-variants">Browse Variations</button>
            @else
                <button type="button" class="btn btn-success js-cart-add" data-id="{{ $product->id }}">Add to Cart</button>
            @endif
            <button type="button" class="btn btn-outline-secondary js-preview-close">Close</button>
        </div>
    </div>
</div>
