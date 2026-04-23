<section class="hero-slider-section">
    <div class="custom-container">
        <div class="hero-slider-shell">
            <div class="home-slider-container hero-home-slider">
                <div class="main_slider owl-carousel">
                    @foreach ($sliders as $key => $value)
                        <div class="slider-item mt-0">
                            <img src="{{ asset($value->image) }}" alt="Hero banner {{ $key + 1 }}" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
