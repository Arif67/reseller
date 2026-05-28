@push('css')
<style>
/* ===== Layout 3 wrapper ===== */
.l3-section {
    padding: 8px 0 14px;
    background: #EFF0F5;
}
.l3-section .custom-container {
    max-width: 1520px;
    margin: 0 auto;
    padding-left: 15px;
    padding-right: 15px;
}
.l3-row {
    display: flex;
    gap: 10px;
    align-items: stretch;
}

/* ===== Slider ===== */
.l3-slider-wrap {
    flex: 0 0 calc(83.333% - 5px);
    max-width: calc(83.333% - 5px);
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    background: #eee;
}
.l3-slider-wrap .home-slider-container,
.l3-slider-wrap .main_slider,
.l3-slider-wrap .owl-stage-outer,
.l3-slider-wrap .owl-stage,
.l3-slider-wrap .owl-item {
    height: 100%;
}
/* aspect ratio 1976:688 ≈ 2.87:1  (Daraz / আপনার existing image হিসাবে) */
.l3-slide {
    aspect-ratio: 1976 / 688;
    min-height: 260px;
    max-height: 520px;
    width: 100%;
}
.l3-slide a { display: block; width: 100%; height: 100%; }
.l3-slide img { width: 100%; height: 100%; object-fit: cover; display: block; }

/* nav arrows */
.l3-slider-wrap .owl-prev,
.l3-slider-wrap .owl-next {
    width: 36px; height: 36px;
    display: flex !important;
    align-items: center; justify-content: center;
    border-radius: 50%;
    background: rgba(0,0,0,0.35) !important;
    border: 0 !important;
    opacity: 0; visibility: hidden;
    transition: opacity .2s, visibility .2s;
    color: #fff;
}
.l3-slider-wrap:hover .owl-prev,
.l3-slider-wrap:hover .owl-next { opacity: 1; visibility: visible; }
.l3-slider-wrap .owl-prev { left: 12px; }
.l3-slider-wrap .owl-next { right: 12px; }
.l3-slider-wrap .owl-prev:hover,
.l3-slider-wrap .owl-next:hover { background: rgba(0,0,0,.55) !important; }
.l3-slider-wrap .owl-prev i,
.l3-slider-wrap .owl-next i { font-size: 13px; color: #fff; }

/* dots */
.l3-slider-wrap .owl-dots {
    position: absolute; bottom: 12px; left: 50%;
    transform: translateX(-50%);
    display: flex; align-items: center; gap: 6px; margin: 0;
}
.l3-slider-wrap .owl-dots .owl-dot span {
    width: 7px; height: 7px; margin: 0;
    background: rgba(255,255,255,.5);
    border-radius: 99px;
    transition: width .25s, background .25s;
}
.l3-slider-wrap .owl-dots .owl-dot.active span {
    width: 22px; background: #fff;
}

/* ===== Download panel ===== */
.l3-app-panel {
    flex: 1 1 0;
    min-width: 0;
    border-radius: 10px;
    background: #fff;
    border: 1px solid #e8e8e8;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 18px 12px 16px;
    text-align: center;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

/* top: logo + name row */
.l3-app-head {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    width: 100%;
}
.l3-app-logo {
    width: 40px; height: 40px;
    border-radius: 8px;
    object-fit: contain;
    background: #fff5f0;
    border: 1px solid #fddccc;
    flex-shrink: 0;
    padding: 3px;
}
.l3-app-name {
    font-size: 11px;
    font-weight: 700;
    color: #333;
    line-height: 1.3;
    text-align: left;
}

/* divider */
.l3-divider {
    width: 100%;
    height: 1px;
    background: #f0f0f0;
    margin-bottom: 10px;
}

/* rating */
.l3-rating {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    color: #555;
    margin-bottom: 4px;
}
.l3-rating-stars { color: #F85606; font-size: 10px; letter-spacing: 1px; }
.l3-rating-val { font-weight: 700; color: #333; font-size: 11px; }

.l3-download-label {
    font-size: 10px;
    font-weight: 600;
    color: #F85606;
    margin-bottom: 10px;
    letter-spacing: .3px;
}

/* feature badges */
.l3-features {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 12px;
}
.l3-feature-item {
    display: flex;
    align-items: center;
    gap: 7px;
    background: #fff8f5;
    border: 1px solid #fde0d0;
    border-radius: 7px;
    padding: 5px 8px;
}
.l3-feature-icon {
    width: 24px; height: 24px;
    border-radius: 6px;
    background: #F85606;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.l3-feature-icon i { color: #fff; font-size: 10px; }
.l3-feature-txt {
    font-size: 9.5px;
    font-weight: 600;
    color: #444;
    text-align: left;
    line-height: 1.2;
}

/* QR + store buttons */
.l3-qr-wrap {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}
.l3-qr-img {
    width: 72px; height: 72px;
    border-radius: 8px;
    border: 1px solid #eee;
    padding: 4px;
    background: #fff;
    object-fit: contain;
}
.l3-qr-placeholder {
    width: 72px; height: 72px;
    border-radius: 8px;
    border: 1.5px dashed #F85606;
    background: #fff8f5;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: #F85606; gap: 3px;
}
.l3-qr-placeholder i { font-size: 22px; }
.l3-qr-placeholder span { font-size: 9px; font-weight: 600; }

.l3-scan-tip {
    font-size: 9px;
    color: #999;
    margin: 0;
    line-height: 1.4;
}

/* store buttons */
.l3-store-btns {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-top: 2px;
}
.l3-store-btn {
    display: flex;
    align-items: center;
    gap: 7px;
    border-radius: 7px;
    padding: 7px 10px;
    text-decoration: none;
    color: #fff;
    width: 100%;
    transition: transform .15s, box-shadow .15s;
}
.l3-store-btn:hover {
    text-decoration: none;
    transform: translateY(-1px);
    color: #fff;
}
.l3-store-btn.android {
    background: #1a1a1a;
    box-shadow: 0 3px 8px rgba(0,0,0,.20);
}
.l3-store-btn.android:hover { background: #000; box-shadow: 0 5px 12px rgba(0,0,0,.30); }
.l3-store-btn.ios {
    background: #1a1a1a;
    box-shadow: 0 3px 8px rgba(0,0,0,.20);
}
.l3-store-btn.ios:hover { background: #000; box-shadow: 0 5px 12px rgba(0,0,0,.30); }
.l3-store-btn i { font-size: 18px; flex-shrink: 0; }
.l3-store-btn .l3-btn-text { text-align: left; }
.l3-store-btn .l3-btn-sub { display: block; font-size: 8px; opacity: .7; line-height: 1; margin-bottom: 1px; }
.l3-store-btn .l3-btn-label { display: block; font-size: 10.5px; font-weight: 700; line-height: 1.2; }

/* ===== Responsive ===== */
@media (max-width: 991.98px) {
    .l3-slider-wrap {
        flex: 0 0 calc(75% - 5px);
        max-width: calc(75% - 5px);
    }
}
@media (max-width: 767.98px) {
    .l3-section { padding-bottom: 6px; }
    .l3-section .custom-container { padding-left: 0; padding-right: 0; }
    .l3-row { flex-direction: column; gap: 0; }
    .l3-slider-wrap {
        flex: none; max-width: 100%;
        border-radius: 0;
    }
    .l3-slide { aspect-ratio: 1976 / 688; min-height: 160px; max-height: 280px; }
    .l3-app-panel {
        border-radius: 0;
        border-top: 0;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        padding: 14px;
        gap: 10px;
    }
    .l3-slider-wrap .owl-prev,
    .l3-slider-wrap .owl-next { display: none !important; }
}
</style>
@endpush

<section class="l3-section slider-section">
    <div class="custom-container">
        <div class="l3-row">

            {{-- ── Banner Slider ── --}}
            <div class="l3-slider-wrap">
                <div class="home-slider-container">
                    <div class="main_slider owl-carousel">
                        @foreach ($sliders as $slider)
                            <div class="l3-slide">
                                <a href="{{ $slider->link ?? '#' }}">
                                    <img src="{{ asset($slider->image) }}" alt="Banner">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── App Download Panel ── --}}
            <div class="l3-app-panel">

                {{-- Logo + Name --}}
                <div class="l3-app-head">
                    @if($generalsetting?->white_logo)
                        <img src="{{ asset($generalsetting->white_logo) }}"
                             alt="{{ $generalsetting->name ?? config('app.name') }}"
                             class="l3-app-logo">
                    @endif
                    <span class="l3-app-name">{{ $generalsetting?->name ?? config('app.name') }}</span>
                </div>

                <div class="l3-divider"></div>

                {{-- Rating --}}
                <div class="l3-rating">
                    <span class="l3-rating-stars">&#9733;</span>
                    <span class="l3-rating-val">4.8</span>
                    <span>Rated</span>
                </div>
                <p class="l3-download-label">Download the App</p>

                {{-- Features --}}
                <div class="l3-features">
                    <div class="l3-feature-item">
                        <div class="l3-feature-icon"><i class="fa-solid fa-truck-fast"></i></div>
                        <span class="l3-feature-txt">Free Delivery</span>
                    </div>
                    <div class="l3-feature-item">
                        <div class="l3-feature-icon"><i class="fa-solid fa-bolt"></i></div>
                        <span class="l3-feature-txt">Flash Sale</span>
                    </div>
                    <div class="l3-feature-item">
                        <div class="l3-feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
                        <span class="l3-feature-txt">Secure Payment</span>
                    </div>
                </div>

                {{-- QR Code --}}
                <div class="l3-qr-wrap">
                    @if($generalsetting?->app_qr_code)
                        <img src="{{ asset($generalsetting->app_qr_code) }}"
                             alt="Scan to Download" class="l3-qr-img">
                        <p class="l3-scan-tip">Scan to download the app</p>
                    @else
                        <div class="l3-qr-placeholder">
                            <i class="fa-solid fa-qrcode"></i>
                            <span>QR Code</span>
                        </div>
                        <p class="l3-scan-tip">Scan to download the app</p>
                    @endif
                </div>

                {{-- Store Buttons --}}
                <div class="l3-store-btns">
                    <a href="{{ $generalsetting?->android_app_link ?? '#' }}" class="l3-store-btn android">
                        <i class="fa-brands fa-google-play"></i>
                        <div class="l3-btn-text">
                            <span class="l3-btn-sub">Get it on</span>
                            <span class="l3-btn-label">Google Play</span>
                        </div>
                    </a>
                    <a href="{{ $generalsetting?->ios_app_link ?? '#' }}" class="l3-store-btn ios">
                        <i class="fa-brands fa-apple"></i>
                        <div class="l3-btn-text">
                            <span class="l3-btn-sub">Download on the</span>
                            <span class="l3-btn-label">App Store</span>
                        </div>
                    </a>
                </div>

            </div>{{-- end panel --}}

        </div>
    </div>
</section>
