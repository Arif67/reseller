@push('css')
    <style>
        .featured-category-layout2-shell {
            padding: 0;
            border: 0;
            background: transparent;
            box-shadow: none;
        }

        .featured-category-layout2-head {
            margin-bottom: 16px;
        }

        .featured-category-layout2-kicker {
            display: none;
        }

        .featured-category-layout2-title {
            margin: 0;
            color: var(--theme-text);
            font-size: clamp(1.25rem, 2.2vw, 1.8rem);
            line-height: 1.1;
            font-weight: 700;
        }

        .featured-category-layout2-slider .item {
            height: 100%;
        }

        .featured-category-layout2-card {
            display: block;
            height: 100%;
            padding: 0;
            border-radius: 10px;
            background: #fff;
            border: 1px solid rgba(148, 163, 184, 0.14);
            text-decoration: none;
            overflow: hidden;
            transition: border-color 0.18s ease, transform 0.18s ease;
        }

        .featured-category-layout2-card:hover {
            transform: translateY(-2px);
            border-color: rgba(15, 23, 42, 0.2);
        }

        .featured-category-layout2-media {
            aspect-ratio: 1 / 1;
            overflow: hidden;
            background: #f8fafc;
        }

        .featured-category-layout2-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .featured-category-layout2-foot {
            padding: 10px 8px 12px;
            text-align: center;
        }

        .featured-category-layout2-name {
            margin: 0;
            color: var(--theme-text);
            font-size: 0.86rem;
            line-height: 1.35;
            font-weight: 600;
        }

        .featured-category-layout2-slider .owl-stage {
            display: flex;
        }

        .featured-category-layout2-slider .owl-item {
            height: auto;
        }
    </style>
@endpush

<section class="homeproduct featured-category-section">
    <div class="custom-container">
        <div class="featured-category-layout2-shell">
            <div class="featured-category-layout2-head">
                <div>
                    <span class="featured-category-layout2-kicker"></span>
                    <h4 class="featured-category-layout2-title">Products Category</h4>
                </div>
            </div>

            <div class="owl-carousel featured-category-layout2-slider">
                @foreach ($frontcategory as $value)
                    <div class="item">
                        <a href="{{ route('category', $value->slug) }}" class="featured-category-layout2-card">
                            <div class="featured-category-layout2-media">
                                <img src="{{ asset($value->image) }}" alt="{{ $value->name }}">
                            </div>
                            <div class="featured-category-layout2-foot">
                                <h3 class="featured-category-layout2-name">{{ $value->name }}</h3>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
