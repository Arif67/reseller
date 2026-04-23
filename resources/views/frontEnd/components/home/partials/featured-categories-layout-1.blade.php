<section class="homeproduct featured-category-section">
    <div class="custom-container">
        <div class="featured-category-shell">
            <div class="featured-category-head">
                <div>
                    <span class="featured-category-kicker">
                        <i class="fa-solid fa-layer-group"></i>
                        Top Collections
                    </span>
                    <h4 class="featured-category-title">Products Category</h4>
                </div>
            </div>

            <div class="owl-carousel product_slider-category featured-category-slider">
                @foreach ($frontcategory as $value)
                    <div class="item">
                        <a href="{{ route('category', $value->slug) }}" class="featured-category-card text-decoration-none">
                            <div class="featured-category-media">
                                <img src="{{ asset($value->image) }}" alt="{{ $value->name }}" class="img-fluid">
                            </div>
                            <div class="featured-category-foot">
                                <h3 class="featured-category-name">{{ $value->name }}</h3>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
