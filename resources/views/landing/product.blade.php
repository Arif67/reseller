@extends('landing.master')

@section('title', $product->name . ' | ' . ($generalsetting?->name ?? 'ShopBase BD'))
@section('description', \Illuminate\Support\Str::limit(strip_tags($product->meta_description ?: $product->description), 160))

@push('styles')
    <style>
        .pd-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .pd-main {
            border: 1px solid var(--line);
            border-radius: 18px;
            overflow: hidden;
            aspect-ratio: 1 / 1;
            background: #f6f7f9;
        }

        .pd-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pd-thumbs {
            display: flex;
            gap: 10px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .pd-thumbs img {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border: 2px solid var(--line);
            border-radius: 10px;
            cursor: pointer;
            transition: border-color .15s ease;
        }

        .pd-thumbs img:hover,
        .pd-thumbs img.active {
            border-color: var(--brand);
        }

        .pd-crumb {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 10px;
        }

        .pd-crumb a {
            color: var(--brand);
        }

        .pd-info h1 {
            font-size: 28px;
            line-height: 1.3;
            margin-bottom: 12px;
        }

        .pd-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 6px;
        }

        .pd-tag {
            background: var(--brand-soft);
            color: var(--brand-dark);
            font-size: 13px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 50px;
        }

        .pd-price-box {
            background: var(--brand-soft);
            border-radius: 14px;
            padding: 18px 22px;
            margin: 18px 0;
        }

        .pd-price-box .label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--muted);
            font-weight: 600;
        }

        .pd-price-box .amt {
            color: var(--brand);
            font-size: 32px;
            font-weight: 700;
        }

        .pd-desc {
            margin-top: 26px;
        }

        .pd-desc h3 {
            font-size: 18px;
            margin-bottom: 12px;
        }

        .pd-desc .content {
            color: #374151;
            line-height: 1.85;
        }

        .pd-desc .content img {
            max-width: 100%;
            border-radius: 10px;
            margin: 8px 0;
        }

        @media (max-width: 800px) {
            .pd-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $registerUrl = Route::has('reseller.register') ? route('reseller.register') : '#';
        $toUrl = fn ($raw) => \Illuminate\Support\Str::startsWith($raw, ['http://', 'https://']) ? $raw : asset($raw);

        $gallery = collect();
        foreach ($product->media as $m) { $gallery->push($m->path); }
        foreach ($product->images as $im) { $gallery->push($im->image); }
        if ($product->image?->image) { $gallery->push($product->image->image); }
        $gallery = $gallery->filter()->unique()->values();
        if ($gallery->isEmpty()) { $gallery->push('uploads/logo.png'); }

        $wholesale = (float) ($product->wholesale_price ?? 0) > 0 ? (float) $product->wholesale_price : (float) $product->new_price;
    @endphp

    <section class="section" style="padding-top:40px">
        <div class="container">
            <div class="pd-crumb">
                <a href="{{ route('home') }}">হোম</a> /
                <a href="{{ route('landing.products') }}">প্রোডাক্ট</a>
                @if ($product->category)
                    / <a href="{{ route('landing.products', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
                @endif
            </div>

            <div class="pd-grid">
                {{-- Gallery --}}
                <div class="pd-gallery">
                    <div class="pd-main">
                        <img id="pdMainImage" src="{{ $toUrl($gallery->first()) }}" alt="{{ $product->name }}">
                    </div>
                    @if ($gallery->count() > 1)
                        <div class="pd-thumbs">
                            @foreach ($gallery as $i => $g)
                                <img src="{{ $toUrl($g) }}" alt="{{ $product->name }}"
                                    class="{{ $i === 0 ? 'active' : '' }}"
                                    onclick="pdSetImage(this, '{{ $toUrl($g) }}')">
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="pd-info">
                    <div class="pd-tags">
                        @if ($product->category)<span class="pd-tag">{{ $product->category->name }}</span>@endif
                        @if ($product->subcategory)<span class="pd-tag">{{ $product->subcategory->subcategoryName }}</span>@endif
                        @if ($product->brand)<span class="pd-tag">{{ $product->brand->name }}</span>@endif
                    </div>

                    <h1>{{ $product->name }}</h1>

                    @if ($product->product_code)
                        <div style="color:var(--muted);font-size:14px">প্রোডাক্ট কোড: {{ $product->product_code }}</div>
                    @endif

                    <div class="pd-price-box">
                        <div class="label">হোলসেল প্রাইস</div>
                        <div class="amt">৳{{ number_format($wholesale) }}</div>
                    </div>

                    <a href="{{ $registerUrl }}" class="btn btn-primary">
                        <i class="fa-solid fa-user-plus"></i> রেজিস্ট্রেশন করে অর্ডার করুন
                    </a>

                    @if ($product->description)
                        <div class="pd-desc">
                            <h3>বিস্তারিত</h3>
                            <div class="content">{!! $product->description !!}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Related products --}}
            @if ($related->isNotEmpty())
                <div class="section-head" style="margin-top:60px;text-align:left">
                    <h2 style="font-size:24px">রিলেটেড প্রোডাক্ট</h2>
                </div>
                <div class="product-grid">
                    @foreach ($related as $p)
                        @include('landing.partials._product_card', ['p' => $p])
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function pdSetImage(el, src) {
            document.getElementById('pdMainImage').src = src;
            document.querySelectorAll('.pd-thumbs img').forEach(i => i.classList.remove('active'));
            el.classList.add('active');
        }
    </script>
@endpush
