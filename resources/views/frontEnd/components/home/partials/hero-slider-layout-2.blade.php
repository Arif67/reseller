@php
    $bannerPool = $sliders->values();
    $mainSliderItems = collect();
    $primarySlider = $bannerPool->get(0);
    $topRightSlider = $bannerPool->get(1) ?? $bannerPool->get(0);
    $bottomRightSlider = $bannerPool->get(2) ?? $bannerPool->get(1) ?? $bannerPool->get(0);

    if ($primarySlider) {
        $mainSliderItems->push($primarySlider);
    }

    foreach ($bannerPool->slice(3) as $slider) {
        $mainSliderItems->push($slider);
    }

    if ($mainSliderItems->isEmpty() && $bannerPool->isNotEmpty()) {
        $mainSliderItems = $bannerPool->take(1);
    }
@endphp

@push('css')
    <style>
        .hero-slider-layout-2 {
            margin-top: 0;
            padding: 8px 0 14px;
            background: #fff;
        }

        .hero-slider-layout-2 .custom-container {
            max-width: 1520px;
            margin: 0 auto;
            padding-left: 15px;
            padding-right: 15px;
        }

        .hero-slider-layout-2 .layout2-row {
            --bs-gutter-x: 10px;
            --bs-gutter-y: 10px;
            align-items: stretch;
        }

        .hero-slider-layout-2 .layout2-main,
        .hero-slider-layout-2 .layout2-side-card {
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            border: 1px solid #e9edf3;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        }

        .hero-slider-layout-2 .layout2-main {
            height: 100%;
        }

        .hero-slider-layout-2 .layout2-row > [class*="col-"] {
            display: flex;
            flex-direction: column;
        }

        .hero-slider-layout-2 .layout2-main .home-slider-container {
            height: 100%;
        }

        .hero-slider-layout-2 .layout2-main .main_slider,
        .hero-slider-layout-2 .layout2-main .owl-stage-outer,
        .hero-slider-layout-2 .layout2-main .owl-stage,
        .hero-slider-layout-2 .layout2-main .owl-item,
        .hero-slider-layout-2 .layout2-slide {
            height: 100%;
        }

        .hero-slider-layout-2 .layout2-slide {
            min-height: clamp(300px, 35vw, 430px);
        }

        .hero-slider-layout-2 .layout2-slide a,
        .hero-slider-layout-2 .layout2-side-card a {
            display: block;
            width: 100%;
            height: 100%;
        }

        .hero-slider-layout-2 .layout2-slide img,
        .hero-slider-layout-2 .layout2-side-card img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        .hero-slider-layout-2 .layout2-side-card {
            height: 100%;
            min-height: 140px;
        }

        .hero-slider-layout-2 .layout2-side-grid {
            height: 100%;
        }

        .hero-slider-layout-2 .layout2-side-grid > .col-12 {
            display: flex;
        }

        .hero-slider-layout-2 .layout2-side-grid .layout2-side-card {
            flex: 1 1 auto;
            min-height: calc((clamp(300px, 35vw, 430px) - 10px) / 2);
        }

        .hero-slider-layout-2 .owl-dots {
            position: absolute;
            left: 18px;
            bottom: 14px;
            display: flex;
            align-items: center;
            gap: 7px;
            margin: 0;
        }

        .hero-slider-layout-2 .owl-dots .owl-dot span {
            width: 9px;
            height: 9px;
            margin: 0;
            background: rgba(255, 255, 255, 0.55);
        }

        .hero-slider-layout-2 .owl-dots .owl-dot.active span {
            width: 28px;
            background: #fff;
        }

        .hero-slider-layout-2 .owl-prev,
        .hero-slider-layout-2 .owl-next {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.42) !important;
            border: 0 !important;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
        }

        .hero-slider-layout-2 .owl-prev {
            left: 16px;
        }

        .hero-slider-layout-2 .owl-next {
            right: 16px;
        }

        .hero-slider-layout-2 .owl-prev i,
        .hero-slider-layout-2 .owl-next i {
            color: #fff;
        }

        .hero-slider-layout-2 .layout2-main:hover .owl-prev,
        .hero-slider-layout-2 .layout2-main:hover .owl-next {
            opacity: 1;
            visibility: visible;
        }

        .hero-slider-layout-2 .owl-prev:hover,
        .hero-slider-layout-2 .owl-next:hover {
            transform: scale(1.05);
            background: rgba(15, 23, 42, 0.58) !important;
        }

        @media (max-width: 767.98px) {
            .hero-slider-layout-2 {
                padding-bottom: 6px;
            }

            .hero-slider-layout-2 .custom-container {
                padding-left: 0;
                padding-right: 0;
            }

            .hero-slider-layout-2 .layout2-main {
                border-radius: 0;
                box-shadow: none;
                border: 0;
            }

            .hero-slider-layout-2 .layout2-side-card {
                min-height: 140px;
            }

            .hero-slider-layout-2 .layout2-side-grid {
                height: auto;
            }

            .hero-slider-layout-2 .layout2-side-grid .layout2-side-card {
                min-height: 140px;
            }

            .hero-slider-layout-2 .layout2-slide {
                min-height: unset;
                height: clamp(220px, 68vw, 340px);
            }

            .hero-slider-layout-2 .owl-prev,
            .hero-slider-layout-2 .owl-next {
                display: none !important;
            }
        }
    </style>
@endpush

<section class="hero-slider-layout-2 slider-section">
    <div class="custom-container">
        <div class="row layout2-row align-items-stretch">
            <div class="col-sm-8 col-12">
                <div class="layout2-main">
                    <div class="home-slider-container">
                        <div class="main_slider owl-carousel">
                            @foreach ($mainSliderItems as $slider)
                                <div class="layout2-slide">
                                    <a href="{{ $slider->link ?? '#' }}">
                                        <img src="{{ asset($slider->image) }}" alt="Slider Image">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4 d-none d-sm-block">
                <div class="row layout2-row layout2-side-grid h-100">
                    @if ($topRightSlider)
                        <div class="col-12">
                            <div class="layout2-side-card">
                                <a href="{{ $topRightSlider->link ?? '#' }}">
                                    <img src="{{ asset($topRightSlider->image) }}" alt="Side Image Top">
                                </a>
                            </div>
                        </div>
                    @endif

                    @if ($bottomRightSlider)
                        <div class="col-12">
                            <div class="layout2-side-card">
                                <a href="{{ $bottomRightSlider->link ?? '#' }}">
                                    <img src="{{ asset($bottomRightSlider->image) }}" alt="Side Image Bottom">
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
