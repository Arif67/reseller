@php
    $galleryImages = collect();

    foreach ($details?->variables ?? [] as $variable) {
        if ($variable->media->count() > 0) {
            foreach ($variable->media as $mediaItem) {
                $galleryImages->push($mediaItem->path);
            }
            continue;
        }

        foreach ($variable->gallery_images as $galleryImage) {
            $galleryImages->push($galleryImage);
        }
    }

    if ($galleryImages->isEmpty()) {
        $galleryImages = collect($details?->media->count() > 0 ? $details->media : $details?->images);
    }

    $layout3Price = $details?->variable_count > 0 && $details?->type == 0 ? $details?->display_new_price : $details?->new_price;
    $layout3OldPrice = $details?->variable_count > 0 && $details?->type == 0 ? $details?->display_old_price : $details?->old_price;
@endphp

@push('css')
    <style>
        .layout3-page,
        .layout3-page * {
            font-family: "Montserrat", "Hind Siliguri", sans-serif;
        }

        .layout3-page {
            padding: 18px 0 40px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .layout3-wrap {
            width: min(100% - 32px, 1480px);
            max-width: none;
            margin: 0 auto;
        }

        .layout3-breadcrumb {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 12px;
        }

        .layout3-breadcrumb a {
            color: #334155;
            text-decoration: none;
        }

        .layout3-top {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 24px;
            align-items: start;
        }

        .layout3-media {
            display: grid;
            grid-template-columns: 92px minmax(0, 1fr);
            gap: 12px;
            padding: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
        }

        .layout3-left {
            min-width: 0;
        }

        .layout3-thumbs {
            display: grid;
            grid-auto-rows: 88px;
            gap: 10px;
            max-height: 560px;
            overflow: auto;
        }

        .layout3-thumb {
            border: 1px solid #dbe4ee;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            cursor: pointer;
            transition: border-color 0.18s ease, transform 0.18s ease;
        }

        .layout3-thumb:hover {
            border-color: #0f172a;
            transform: translateY(-1px);
        }

        .layout3-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .layout3-main {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            aspect-ratio: 1 / 1;
        }

        .layout3-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .layout3-buy {
            position: sticky;
            top: 16px;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #fff;
            padding: 18px;
            box-shadow: 0 12px 26px rgba(15, 23, 42, 0.05);
        }

        .layout3-kicker {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid #dbe4ee;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 10px;
        }

        .layout3-title {
            margin: 0;
            color: #0f172a;
            font-size: 30px;
            line-height: 1.2;
            font-weight: 700;
        }

        .layout3-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .layout3-meta-chip {
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            color: #334155;
            font-weight: 600;
        }

        .layout3-price {
            margin-top: 14px;
            padding: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #ffffff;
        }

        .layout3-price .details-price {
            margin: 0;
            font-size: 34px;
            line-height: 1;
            color: #0f172a;
            font-weight: 900;
            letter-spacing: -0.03em;
        }

        .layout3-price del {
            color: #94a3b8;
            font-size: 16px;
            font-weight: 700;
            margin-right: 8px;
        }

        .layout3-stock {
            margin-top: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #166534;
        }

        .layout3-group {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid #eef2f7;
        }

        .layout3-group-title {
            margin: 0 0 9px;
            font-size: 13px;
            color: #334155;
            font-weight: 800;
        }

        .layout3-qty {
            display: inline-flex;
            align-items: center;
            border: 1px solid #dbe4ee;
            border-radius: 999px;
            overflow: hidden;
            background: #fff;
        }

        .layout3-qty .minus,
        .layout3-qty .plus {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            cursor: pointer;
            user-select: none;
            background: #f8fafc;
        }

        .layout3-qty input {
            width: 62px;
            height: 42px;
            border: 0;
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
        }

        .layout3-cta {
            margin-top: 14px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .layout3-btn {
            min-height: 52px;
            border: 0;
            border-radius: 12px;
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            transition: transform 0.18s ease;
        }

        .layout3-btn:hover {
            transform: translateY(-1px);
        }

        .layout3-btn-add {
            background: #111827;
        }

        .layout3-btn-buy {
            background: #dc2626;
        }

        .layout3-phone {
            width: 100%;
            min-height: 44px;
            margin-top: 10px;
            border-radius: 12px;
            border: 1px solid #dbe4ee;
            background: #fff;
            color: #0f172a;
            font-size: 14px;
            font-weight: 700;
        }

        .layout3-desc {
            margin-top: 20px;
            padding: 18px;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #fff;
        }

        .layout3-desc h4 {
            margin: 0 0 10px;
            font-size: 20px;
            color: #0f172a;
            font-weight: 700;
        }

        .layout3-related {
            margin-top: 22px;
        }

        .layout3-related-head {
            margin-bottom: 16px;
            text-align: left;
        }

        .layout3-related-head h2 {
            margin: 0 0 6px;
            font-size: 28px;
            font-weight: 900;
            color: #0f172a;
        }

        .layout3-related-head p {
            margin: 0;
            color: #64748b;
        }

        .layout3-related-grid .np-item {
            min-width: 0;
        }

        @media (max-width: 991.98px) {
            .layout3-top {
                grid-template-columns: 1fr;
            }

            .layout3-buy {
                position: static;
            }
        }

        @media (max-width: 767.98px) {
            .layout3-wrap {
                width: min(100% - 24px, 1480px);
            }

            .layout3-media {
                grid-template-columns: 1fr;
            }

            .layout3-thumbs {
                grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
                grid-auto-rows: 72px;
                max-height: none;
            }

            .layout3-title {
                font-size: 24px;
            }

            .layout3-cta {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

<div class="layout3-page">
    <div class="layout3-wrap">
        <div class="layout3-breadcrumb">
            <a href="{{ url('/') }}">Home</a> /
            <a href="{{ url('/category/' . $details?->category?->slug) }}">{{ $details?->category?->name }}</a> /
            <span>{{ $details?->name }}</span>
        </div>

        <div class="layout3-top">
            <div class="layout3-left">
                <div class="layout3-media">
                    <div class="layout3-thumbs">
                        @foreach ($galleryImages as $value)
                            @php($imageValue = is_string($value) ? $value : (data_get($value, 'path', data_get($value, 'image'))))
                            @if ($imageValue)
                                @php($imageUrl = \Illuminate\Support\Str::startsWith($imageValue, ['http://', 'https://']) ? $imageValue : asset($imageValue))
                                <div class="layout3-thumb" data-layout3-thumb data-image-src="{{ $imageUrl }}">
                                    <img src="{{ $imageUrl }}" alt="{{ $details?->name }}">
                                </div>
                            @endif
                        @endforeach
                    </div>

                    @php($mainImage = $galleryImages->first())
                    @php($mainImageValue = is_string($mainImage) ? $mainImage : (data_get($mainImage, 'path', data_get($mainImage, 'image'))))
                    @php($mainImageUrl = \Illuminate\Support\Str::startsWith($mainImageValue, ['http://', 'https://']) ? $mainImageValue : asset($mainImageValue ?: 'uploads/logo.png'))
                    <div class="layout3-main">
                        <img src="{{ $mainImageUrl }}" alt="{{ $details?->name }}">
                    </div>
                </div>

                <div class="layout3-desc">
                    <h4>Product Details</h4>
                    <div class="accordion" id="layout3Accordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="layout3DescHead">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#layout3Desc">Description</button>
                            </h2>
                            <div id="layout3Desc" class="accordion-collapse collapse show" data-bs-parent="#layout3Accordion">
                                <div class="accordion-body">{!! $details?->description !!}</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="layout3CareHead">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#layout3Care">Care Tips</button>
                            </h2>
                            <div id="layout3Care" class="accordion-collapse collapse" data-bs-parent="#layout3Accordion">
                                <div class="accordion-body">{!! $details?->care_tips !!}</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="layout3PolicyHead">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#layout3Policy">Delivery & Return Policy</button>
                            </h2>
                            <div id="layout3Policy" class="accordion-collapse collapse" data-bs-parent="#layout3Accordion">
                                <div class="accordion-body">{!! $page?->description !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="layout3-buy">
                <h1 class="layout3-title">{{ $details?->name }}</h1>

                <div class="layout3-meta">
                    <span class="layout3-meta-chip">SKU: {{ $details?->product_code ?: 'N/A' }}</span>
                    <span class="layout3-meta-chip">Reviews: {{ $reviews->count() }}</span>
                    <span class="layout3-meta-chip">COD Available</span>
                </div>

                <div class="layout3-price">
                    <p class="details-price">
                        <del data-old-price-wrapper @if (!$layout3OldPrice) style="display:none;" @endif>৳<span class="old_price">{{ $layout3OldPrice }}</span></del>
                        ৳<span class="new_price">{{ $layout3Price }}</span>
                    </p>
                    <div class="layout3-stock">{{ $details?->variable_count > 0 && $details?->type == 0 ? 'Select options' : 'In stock' }}</div>
                </div>

                <form action="{{ route('cart.store') }}" method="POST" name="formName">
                    @csrf
                    <input type="hidden" name="id" value="{{ $details?->id }}">

                    <div class="layout3-group">
                        <p class="layout3-group-title">Select Options</p>
                        @foreach ($productAttributeGroups as $attributeGroup)
                            <div class="mb-3 product-attribute-group" data-attribute-group="{{ $attributeGroup['attribute_id'] }}">
                                <p class="layout3-group-title mb-2">{{ $attributeGroup['title'] }}</p>
                                <div class="selector">
                                    @foreach ($attributeGroup['options'] as $option)
                                        <div class="selector-item">
                                            <input
                                                type="radio"
                                                id="layout3-attribute-{{ $attributeGroup['attribute_id'] }}-{{ $loop->index }}"
                                                value="{{ $option['id'] }}"
                                                name="attribute_values[{{ $attributeGroup['attribute_id'] }}]"
                                                class="selector-item_radio emptyalert stock_check attribute-option"
                                                data-attribute-title="{{ $attributeGroup['title'] }}"
                                                data-option-title="{{ $option['title'] }}"
                                                @if (!$option['id']) disabled @endif>
                                            <label for="layout3-attribute-{{ $attributeGroup['attribute_id'] }}-{{ $loop->index }}" class="selector-item_label">
                                                {{ $option['title'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        @if ($details?->pro_unit)
                            <input type="hidden" name="pro_unit" value="{{ $details?->pro_unit }}">
                        @endif
                    </div>

                    <div class="layout3-group">
                        <p class="layout3-group-title">Quantity</p>
                        <div class="layout3-qty">
                            <span class="minus">-</span>
                            <input type="text" name="qty" value="1">
                            <span class="plus">+</span>
                        </div>
                    </div>

                    <div class="layout3-cta">
                        <button type="submit" name="add_cart" onclick="return sendSuccess();" class="layout3-btn layout3-btn-add">Add to cart</button>
                        <button type="submit" name="order_now" value="order_now" onclick="return sendSuccess();" class="layout3-btn layout3-btn-buy">Buy now</button>
                    </div>
                </form>

                <button type="button" class="layout3-phone">Order by phone (01832-883232)</button>
            </div>
        </div>

        <div class="layout3-related">
            <section class="related-product-section">
                <div class="layout3-related-head">
                    <div class="section-kicker">You may also like</div>
                    <h2>Related Products</h2>
                    <p>More styles from the same category.</p>
                </div>
                <div class="catalog-product-grid layout3-related-grid">
                    @foreach ($products as $key => $value)
                        @include('frontEnd.partials.product-card', ['product' => $value, 'titleLimit' => 56])
                    @endforeach
                </div>
            </section>

            @if (($recentlyViewedProducts ?? collect())->isNotEmpty())
            <section class="related-product-section">
                <div class="layout3-related-head">
                    <div class="section-kicker">Continue browsing</div>
                    <h2>Recently Viewed Products</h2>
                    <p>Products you checked recently.</p>
                </div>
                <div class="product-inner owl-carousel recently_viewed_details_slider">
                    @foreach ($recentlyViewedProducts as $value)
                        @include('frontEnd.partials.product-card', ['product' => $value, 'titleLimit' => 56])
                    @endforeach
                </div>
            </section>
            @endif
        </div>
    </div>
</div>

@push('script')
    <script>
        document.addEventListener('click', function(event) {
            const thumb = event.target.closest('[data-layout3-thumb]');
            if (!thumb) {
                return;
            }

            const mainImage = document.querySelector('.layout3-main img');
            const imageSrc = thumb.getAttribute('data-image-src');

            if (mainImage && imageSrc) {
                mainImage.src = imageSrc;
            }
        });
    </script>
@endpush
