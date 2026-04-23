@php
    $productBrowserItems = $catalogProducts ?? collect();
    $currentCount = method_exists($productBrowserItems, 'count') ? $productBrowserItems->count() : count($productBrowserItems);
    $totalCount = method_exists($productBrowserItems, 'total') ? $productBrowserItems->total() : $currentCount;
    $hasMorePages = method_exists($productBrowserItems, 'hasMorePages') ? $productBrowserItems->hasMorePages() : false;
    $nextPage = method_exists($productBrowserItems, 'currentPage') ? $productBrowserItems->currentPage() + 1 : 2;
@endphp

<div id="catalogProductsGrid" class="product-browser-grid" data-current="{{ $currentCount }}" data-total="{{ $totalCount }}" data-next-page="{{ $nextPage }}">
    @forelse ($productBrowserItems as $product)
        @php
            $variantCount = $product->variables?->count() ?? 0;
            $thumb = asset($product->primary_media_image ?? optional($product->image)->image ?? 'uploads/logo.png');
            $hasVariants = (int) $product->type === 0 && $variantCount > 0;
        @endphp
        <div class="product-browser-card">
            <div class="product-browser-thumb">
                <img src="{{ $thumb }}" alt="{{ $product->name }}">
            </div>
            <div class="product-browser-body">
                <div class="product-browser-title">{{ $product->name }}</div>
                <div class="product-browser-meta">
                    <span>৳{{ number_format((float) $product->new_price, 2, '.', '') }}</span>
                    @if ($product->old_price)
                        <del>৳{{ number_format((float) $product->old_price, 2, '.', '') }}</del>
                    @endif
                </div>
                <div class="product-browser-submeta">
                    @if ($product->pro_barcode)
                        <span class="badge bg-light text-dark">Barcode: {{ $product->pro_barcode }}</span>
                    @endif
                    @if ($hasVariants)
                        <span class="badge bg-info text-dark">{{ $variantCount }} Variations</span>
                    @else
                        <span class="badge bg-success">Single Item</span>
                    @endif
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="button"
                        class="btn btn-outline-primary btn-sm flex-grow-1 js-product-preview"
                        data-id="{{ $product->id }}">
                        Preview
                    </button>
                    @if (! $hasVariants)
                        <button type="button"
                            class="btn btn-success btn-sm flex-grow-1 js-cart-add"
                            data-id="{{ $product->id }}">
                            Add
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="text-muted small">No active product found.</div>
    @endforelse
</div>
@if ($hasMorePages)
    <div class="catalog-load-more-wrap">
        <button type="button" class="btn btn-outline-dark w-100 catalog-load-more-btn" data-next-page="{{ $nextPage }}">
            Load More Products
        </button>
    </div>
@endif
