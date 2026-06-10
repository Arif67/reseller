@extends('landing.master')

@section('title', 'প্রোডাক্ট | ' . ($generalsetting?->name ?? 'ShopBase BD'))

@section('content')
    @include('landing.partials._banner', [
        'crumb' => 'Products',
        'title' => 'আমাদের প্রোডাক্ট',
        'text' => 'ক্যাটাগরি বা সাব-ক্যাটাগরি বেছে নিয়ে পছন্দের প্রোডাক্ট দেখুন।',
    ])

    @php
        $registerUrl = Route::has('reseller.register') ? route('reseller.register') : '#';
    @endphp

    <section class="section" style="padding-top:40px">
        <div class="container">

            {{-- Categories --}}
            @if ($categories->isNotEmpty())
                <div class="cat-row">
                    @foreach ($categories as $cat)
                        @php $isActive = $selectedCategory && $selectedCategory->id === $cat->id && !$selectedSubcategory; @endphp
                        <a href="{{ route('landing.products', ['category' => $cat->slug]) }}"
                            class="cat-tile {{ $isActive ? 'active' : '' }}">
                            @if ($cat->image)
                                <div class="thumb"><img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" loading="lazy"></div>
                            @else
                                <div class="ic"><i class="fa-solid fa-tag"></i></div>
                            @endif
                            <span>{{ $cat->name }}</span>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Subcategories of the selected category --}}
            @if ($subcategories->isNotEmpty())
                <div class="subcat-chips">
                    <a href="{{ route('landing.products', ['category' => $selectedCategory->slug]) }}"
                        class="chip {{ !$selectedSubcategory ? 'active' : '' }}">সব</a>
                    @foreach ($subcategories as $sub)
                        <a href="{{ route('landing.products', ['subcategory' => $sub->slug]) }}"
                            class="chip {{ $selectedSubcategory && $selectedSubcategory->id === $sub->id ? 'active' : '' }}">
                            {{ $sub->subcategoryName }}
                        </a>
                    @endforeach
                </div>
            @endif

            <h3 class="browse-title">
                {{ $selectedSubcategory->subcategoryName ?? $selectedCategory->name ?? 'সকল প্রোডাক্ট' }}
                <span style="color:var(--muted);font-weight:400;font-size:15px">({{ $products->count() }})</span>
            </h3>

            {{-- Products --}}
            @if ($products->isNotEmpty())
                <div class="product-grid">
                    @foreach ($products as $p)
                        @include('landing.partials._product_card', ['p' => $p])
                    @endforeach
                </div>
            @else
                <p style="text-align:center;color:var(--muted);padding:30px 0">এই ক্যাটাগরিতে এখনো কোনো প্রোডাক্ট যোগ করা হয়নি।</p>
            @endif
        </div>
    </section>

    @include('landing.partials._cta')
@endsection
