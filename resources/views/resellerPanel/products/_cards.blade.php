@foreach($products as $product)
    <div class="col mb-3">
        <div class="card rp-card mb-0 h-100">
            @php
                $cardImg = $product->primary_media_image ?? 'public/uploads/default/user.png';
                $cardImgSrc = \Illuminate\Support\Str::startsWith($cardImg, ['http://','https://']) ? $cardImg : asset($cardImg);
            @endphp
            <div class="position-relative">
                <a href="{{ route('reseller.products.details', $product->id) }}">
                    <img src="{{ $cardImgSrc }}" alt="{{ $product->name }}" class="rp-thumb card-img-top">
                </a>
                @php $isFav = in_array($product->id, $favouriteIds ?? []); @endphp
                <button type="button" data-fav-toggle
                    class="rp-fav-btn {{ $isFav ? 'is-fav' : '' }}"
                    data-product-id="{{ $product->id }}"
                    data-favourited="{{ $isFav ? '1' : '0' }}"
                    @if($favPage ?? false) data-remove-on-unfav="1" @endif
                    title="Favourite e add/remove korun">
                    <i class="mdi {{ $isFav ? 'mdi-heart' : 'mdi-heart-outline' }}"></i>
                </button>
                @if($product->stock <= 0)
                    <span class="badge bg-danger position-absolute" style="top:8px; right:8px;">Stock Out</span>
                @endif
            </div>
            <div class="card-body p-2 d-flex flex-column">
                <a href="{{ route('reseller.products.details', $product->id) }}" class="text-dark text-decoration-none">
                    <p class="rp-name mb-1" title="{{ $product->name }}">{{ $product->name }}</p>
                </a>
                <div class="mb-2">
                    <small class="text-muted d-block" style="font-size:11px;">Wholesale Price</small>
                    <h5 class="mb-0 text-success">৳ {{ number_format($product->wholesale_price, 0) }}</h5>
                </div>
                <div class="mt-auto">
                    <span class="badge bg-light text-dark">Stock: {{ $product->stock }}</span>
                </div>
            </div>
        </div>
    </div>
@endforeach
