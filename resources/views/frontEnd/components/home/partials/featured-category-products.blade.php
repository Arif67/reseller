@push('css')
    <style>
        .theme-category-section {
            padding: 0 0 28px;
            background: transparent;
        }

        .theme-category-banner {
            border-radius: 14px;
            overflow: hidden;
            min-height: 100%;
            background: transparent;
        }

        .theme-category-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .theme-category-products {
            position: relative;
            width: 100%;
            min-width: 0;
        }

        .theme-category-products.owl-carousel,
        .theme-category-products .owl-stage-outer,
        .theme-category-products .owl-stage {
            width: 100%;
        }

        .theme-category-slider-column {
            min-width: 0;
        }

        .theme-category-products .item {
            padding: 3px;
        }

        .theme-category-products .np-item,
        .theme-category-products .np-product-card {
            height: 100%;
        }

        .theme-category-products .np-product-card {
            min-height: 100%;
        }

        .theme-category-products .owl-stage {
            display: flex;
        }

        .theme-category-products .owl-item {
            height: auto;
        }

        .theme-category-products .owl-nav {
            display: none !important;
        }

        .theme-category-products .owl-dots {
            margin-top: 12px;
            text-align: center;
        }

        .theme-category-products .owl-dots .owl-dot span {
            background: rgba(148, 163, 184, 0.5);
        }

        .theme-category-products .owl-dots .owl-dot.active span {
            background: var(--theme-accent, #1a81b7);
        }

        @media (max-width: 767.98px) {
            .theme-category-section {
                padding-bottom: 22px;
            }

        }
    </style>
@endpush

@foreach ($homecategory as $homecat)
    @php
        $products = $homecat->products;
        $isOdd = $loop->index % 2 !== 0;
        $hasBanner = (int) ($homecat->banner_image ?? 0) === 1 && !empty($homecat->image);
        $bannerImage = $homecat->image_url ?: asset($homecat->image);
    @endphp

    <section class="homeproduct theme-category-section">
        <div class="custom-container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="showcase-product-head">
                        <div>
                            <h5 class="showcase-product-title">{{ $homecat->name }}</h5>
                        </div>
                        <a href="{{ route('category', $homecat->slug) }}" class="showcase-product-link">
                            View More
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row align-items-stretch">
                @if ($hasBanner)
                    <div class="col-md-3 col-sm-12 mb-3 mb-md-0 d-flex {{ $isOdd ? 'order-md-2' : 'order-md-1' }}">
                        <div class="theme-category-banner w-100">
                            <img class="w-100 img-height-responsive" src="{{ $bannerImage }}" alt="{{ $homecat->name }}">
                        </div>
                    </div>
                @endif

                <div class="@if ($hasBanner) col-md-9 @else col-md-12 @endif col-sm-12 mt-3 mt-md-0 d-flex flex-column theme-category-slider-column {{ $isOdd ? 'order-md-1' : 'order-md-2' }}">
                    <div class="theme-category-products category-products-slider owl-carousel h-100">
                        @foreach ($products as $value)
                            <div class="item">
                                @include('frontEnd.partials.product-card', ['product' => $value, 'titleLimit' => 52])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endforeach
