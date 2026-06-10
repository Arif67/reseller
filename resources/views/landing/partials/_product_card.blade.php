@php
    $raw = $p->primary_media_image ?? 'uploads/logo.png';
    $img = \Illuminate\Support\Str::startsWith($raw, ['http://', 'https://']) ? $raw : asset($raw);
    $wholesale = (float) ($p->wholesale_price ?? 0) > 0 ? (float) $p->wholesale_price : (float) $p->new_price;
@endphp
<a href="{{ route('landing.product.show', $p->slug) }}" class="product-card" title="{{ $p->name }}">
    <div class="pc-img">
        <img src="{{ $img }}" alt="{{ $p->name }}" loading="lazy">
    </div>
    <div class="pc-body">
        <h4>{{ $p->name }}</h4>
        <div class="pc-price">
            <span class="pc-label">হোলসেল প্রাইস</span>
            <b>৳{{ number_format($wholesale) }}</b>
        </div>
        <span class="pc-btn">বিস্তারিত দেখুন <i class="fa-solid fa-arrow-right"></i></span>
    </div>
</a>
