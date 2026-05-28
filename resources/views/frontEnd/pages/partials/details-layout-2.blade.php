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

    $layout2Price = $details?->variable_count > 0 && $details?->type == 0 ? $details?->display_new_price : $details?->new_price;
    $layout2OldPrice = $details?->variable_count > 0 && $details?->type == 0 ? $details?->display_old_price : $details?->old_price;
@endphp

@push('css')
    <style>
        .layout2-page,
        .layout2-page * {
            font-family: "Poppins", "Hind Siliguri", sans-serif;
        }

        .layout2-page {
            padding: 20px 0 40px;
            background:
                radial-gradient(circle at 85% 12%, rgba(249, 115, 22, 0.14), transparent 32%),
                radial-gradient(circle at 8% 18%, rgba(59, 130, 246, 0.08), transparent 24%),
                linear-gradient(180deg, #f8fafc 0%, #ffffff 35%);
        }

        .layout2-shell {
            max-width: 1380px;
            margin: 0 auto;
        }

        .layout2-breadcrumb {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 12px;
            color: #475569;
            margin-bottom: 14px;
            padding: 10px 14px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e2e8f0;
        }

        .layout2-breadcrumb a {
            color: #334155;
            text-decoration: none;
        }

        .layout2-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(340px, 0.9fr) minmax(240px, 0.5fr);
            gap: 16px;
            align-items: start;
        }

        .layout2-gallery,
        .layout2-buybox,
        .layout2-shipbox,
        .layout2-panel {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.05);
        }

        .layout2-gallery {
            padding: 14px;
            border-top: 4px solid #f97316;
        }

        .layout2-main-image {
            border-radius: 14px;
            overflow: hidden;
            background: linear-gradient(180deg, #fff7ed 0%, #ffedd5 100%);
            aspect-ratio: 1 / 1;
            border: 1px solid #fed7aa;
        }

        .layout2-main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .layout2-thumbs {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(82px, 1fr));
            gap: 10px;
            margin-top: 12px;
        }

        .layout2-thumb {
            border: 1px solid #dbe4ee;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            background: #fff;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .layout2-thumb:hover {
            transform: translateY(-2px);
            border-color: #fb923c;
            box-shadow: 0 10px 18px rgba(249, 115, 22, 0.18);
        }

        .layout2-thumb img {
            width: 100%;
            height: 82px;
            object-fit: cover;
            display: block;
        }

        .layout2-buybox {
            position: sticky;
            top: 18px;
            padding: 16px;
            border-top: 4px solid #f97316;
        }

        .layout2-title {
            font-size: 26px;
            line-height: 1.22;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 10px;
        }

        .layout2-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .layout2-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 10px;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
        }

        .layout2-price {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 16px;
            border-radius: 16px;
            background: linear-gradient(135deg, #fff7ed 0%, #fff1e6 100%);
            border: 1px solid #fdba74;
            margin-bottom: 10px;
        }

        .layout2-price .new_price,
        .layout2-price .details-price {
            font-size: 28px;
            font-weight: 900;
            color: #ea580c;
            margin: 0;
            letter-spacing: -0.03em;
        }

        .layout2-price .old_price,
        .layout2-price del {
            color: #94a3b8;
            font-size: 14px;
            font-weight: 700;
        }

        .layout2-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }

        .layout2-summary-card {
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .layout2-summary-card span {
            display: block;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            margin-bottom: 2px;
        }

        .layout2-summary-card strong {
            color: #0f172a;
            font-size: 12px;
        }

        .layout2-shipbox .layout2-summary-grid {
            grid-template-columns: 1fr;
        }

        .layout2-group {
            padding: 4px 0;
            border-top: 1px solid #f1f5f9;
        }

        .layout2-group:first-of-type {
            border-top: 0;
            padding-top: 0;
        }

        .layout2-label {
            font-size: 13px;
            font-weight: 800;
            color: #334155;
            margin-bottom: 4px;
        }

        .layout2-option-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
        }

        .layout2-option-row small {
            color: #64748b;
            font-weight: 600;
            font-size: 11px;
        }

        .layout2-qty-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .layout2-qty {
            display: inline-flex;
            align-items: center;
            border: 1px solid #fdba74;
            border-radius: 999px;
            overflow: hidden;
            background: #fff7ed;
            box-shadow: inset 0 0 0 1px rgba(249, 115, 22, 0.12);
        }

        .layout2-qty .minus,
        .layout2-qty .plus {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 800;
            color: #ea580c;
            cursor: pointer;
            user-select: none;
            background: #fff;
        }

        .layout2-qty input {
            width: 58px;
            height: 40px;
            border: 0;
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            background: #fff7ed;
        }

        .layout2-cta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 14px;
        }

        .layout2-btn {
            min-height: 52px;
            border: 0;
            border-radius: 14px;
            font-weight: 800;
            color: #fff;
            box-shadow: 0 12px 22px rgba(15, 23, 42, 0.12);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .layout2-btn:hover {
            transform: translateY(-1px);
        }

        .layout2-add {
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            font-size: 13px;
            opacity: 0.85;
        }

        .layout2-buy {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            box-shadow: 0 4px 14px rgba(185, 28, 28, 0.4);
            font-size: 16px;
            letter-spacing: 0.3px;
        }

        .layout2-buy:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
            box-shadow: 0 6px 20px rgba(185, 28, 28, 0.5);
        }

        .layout2-phone {
            width: 100%;
            margin-top: 10px;
            min-height: 46px;
            border-radius: 14px;
            background: #1e40af;
            border: 1px solid #1e40af;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .layout2-phone:hover {
            background: #1e3a8a;
            color: #fff;
        }

        .layout2-whatsapp {
            width: 100%;
            margin-top: 10px;
            min-height: 46px;
            border-radius: 14px;
            background: #25d366;
            border: 1px solid #25d366;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .layout2-whatsapp:hover {
            background: #22c55e;
            color: #fff;
        }

        .layout2-shipbox {
            position: sticky;
            top: 18px;
            padding: 16px;
            border-top: 4px solid #0ea5e9;
        }

        .layout2-shipbox h6 {
            margin: 0 0 8px;
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
        }

        .layout2-ship-row {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            padding: 10px 0;
            border-top: 1px dashed #e2e8f0;
        }

        .layout2-ship-row:first-of-type {
            border-top: 0;
            padding-top: 0;
        }

        .layout2-ship-row i {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 12px;
            flex: 0 0 28px;
        }

        .layout2-ship-row strong {
            display: block;
            font-size: 13px;
            color: #0f172a;
        }

        .layout2-ship-row span {
            display: block;
            font-size: 12px;
            color: #64748b;
        }

        .layout2-seller {
            margin-top: 12px;
            padding: 10px;
            border: 1px solid #dbeafe;
            border-radius: 12px;
            background: #eff6ff;
        }

        .layout2-seller strong {
            font-size: 13px;
            color: #1e40af;
        }

        .layout2-seller p {
            margin: 4px 0 0;
            font-size: 12px;
            color: #334155;
        }

        .layout2-panel {
            margin-top: 20px;
            padding: 18px;
        }

        .layout2-section-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .layout2-accordion .accordion-item {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .layout2-accordion .accordion-button {
            background: #fff;
            font-weight: 800;
            color: #0f172a;
            box-shadow: none !important;
        }

        .layout2-related {
            margin-top: 22px;
        }

        @media (max-width: 991.98px) {
            .layout2-grid {
                grid-template-columns: 1fr;
            }

            .layout2-buybox,
            .layout2-shipbox {
                position: static;
            }
        }

        @media (max-width: 767.98px) {
            .layout2-page {
                padding-top: 12px;
            }

            .layout2-summary-grid,
            .layout2-cta-grid {
                grid-template-columns: 1fr;
            }

            .layout2-title {
                font-size: 22px;
            }
        }
    </style>
@endpush

<div class="layout2-page">
    <div class="custom-container layout2-shell">
        <div class="layout2-grid">
            <div class="layout2-gallery">
                @php($mainImage = $galleryImages->first())
                <div class="layout2-main-image">
                    @php($mainImageValue = is_string($mainImage) ? $mainImage : (data_get($mainImage, 'path', data_get($mainImage, 'image'))))
                    @php($mainImageUrl = \Illuminate\Support\Str::startsWith($mainImageValue, ['http://', 'https://']) ? $mainImageValue : asset($mainImageValue ?: 'uploads/logo.png'))
                    <img src="{{ $mainImageUrl }}" alt="{{ $details?->name }}">
                </div>

                <div class="layout2-thumbs">
                    @foreach ($galleryImages as $key => $value)
                        @php($imageValue = is_string($value) ? $value : (data_get($value, 'path', data_get($value, 'image'))))
                        @if ($imageValue)
                            @php($imageUrl = \Illuminate\Support\Str::startsWith($imageValue, ['http://', 'https://']) ? $imageValue : asset($imageValue))
                            <div class="layout2-thumb" data-layout2-thumb data-image-src="{{ $imageUrl }}">
                                <img src="{{ $imageUrl }}" alt="{{ $details?->name }}">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="layout2-buybox">
                <h1 class="layout2-title">{{ $details?->name }}</h1>

                <div class="layout2-meta">
                    <span class="layout2-chip">SKU: {{ $details?->product_code ?: 'N/A' }}</span>
                    <span class="layout2-chip"><i class="fa-solid fa-star text-warning"></i> Reviews {{ $reviews->count() }}</span>
                    <span class="layout2-chip"><i class="fa-solid fa-truck-fast"></i> Fast delivery</span>
                </div>

                <div class="layout2-price">
                    <div>
                        <div class="product-price-caption">Price</div>
                        <p class="details-price mb-0">
                            <del data-old-price-wrapper @if (!$layout2OldPrice) style="display:none;" @endif>৳<span class="old_price">{{ $layout2OldPrice }}</span></del>
                            ৳<span class="new_price">{{ $layout2Price }}</span>
                        </p>
                    </div>
                    <span class="stock-pill">{{ $details?->variable_count > 0 && $details?->type == 0 ? 'Choose options' : 'Ready to order' }}</span>
                </div>

                <form action="{{ route('cart.store') }}" method="POST" name="formName">
                    @csrf
                    <input type="hidden" name="id" value="{{ $details?->id }}">

                    <div class="layout2-group">
                        <div class="layout2-label">Options</div>
                        @foreach ($productAttributeGroups as $attributeGroup)
                            <div class="mb-1 product-attribute-group" data-attribute-group="{{ $attributeGroup['attribute_id'] }}">
                                <div class="layout2-option-row">
                                    <p class="layout2-label mb-0">{{ $attributeGroup['title'] }}</p>
                                    <small>Select one</small>
                                </div>
                                <div class="selector">
                                    @foreach ($attributeGroup['options'] as $option)
                                        <div class="selector-item">
                                            <input
                                                type="radio"
                                                id="layout2-attribute-{{ $attributeGroup['attribute_id'] }}-{{ $loop->index }}"
                                                value="{{ $option['id'] }}"
                                                name="attribute_values[{{ $attributeGroup['attribute_id'] }}]"
                                                class="selector-item_radio emptyalert stock_check attribute-option"
                                                data-attribute-title="{{ $attributeGroup['title'] }}"
                                                data-option-title="{{ $option['title'] }}"
                                                @if (!$option['id']) disabled @endif>
                                            <label for="layout2-attribute-{{ $attributeGroup['attribute_id'] }}-{{ $loop->index }}" class="selector-item_label">
                                                {{ $option['title'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        @if ($details?->pro_unit)
                            <input type="hidden" name="pro_unit" value="{{ $details?->pro_unit }}">
                            <div class="layout2-chip mt-2"><i class="fa-solid fa-ruler-combined"></i> Unit: {{ $details?->pro_unit }}</div>
                        @endif
                    </div>

                    <div class="layout2-group">
                        <div class="layout2-label">Quantity</div>
                        <div class="layout2-qty-wrap">
                            <div class="layout2-qty">
                                <span class="minus">-</span>
                                <input type="text" name="qty" value="1">
                                <span class="plus">+</span>
                            </div>
                            <span class="layout2-chip">Tap + / - for quantity</span>
                        </div>
                    </div>

                    <div class="layout2-cta-grid">
                        <button type="submit" name="add_cart" onclick="return sendSuccess();" class="layout2-btn layout2-add">
                            কার্টে যোগ করুন
                        </button>
                        <button type="submit" name="order_now" value="order_now" onclick="return sendSuccess();" class="layout2-btn layout2-buy">
                            অর্ডার করুন
                        </button>
                    </div>

                    <a href="tel:{{ $contact?->hotline }}" class="layout2-phone">
                        <i class="fa-solid fa-phone"></i> ফোনে অর্ডার করুন ({{ $contact?->hotline }})
                    </a>
                    <a href="https://api.whatsapp.com/send?phone=88{{ $contact?->hotline }}&text=Hello, I want to order {{ $details?->name }}" target="_blank" class="layout2-whatsapp">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp এ অর্ডার করুন
                    </a>
                </form>

                <div class="store-note-card mt-3">
                    <i class="fa-solid fa-store"></i>
                    <div>
                        <p class="fw-semibold mb-1 text-success">{{ $generalsetting->pickup_title }}</p>
                        <p class="text-muted small mb-2">{{ $generalsetting->pickup_description }}</p>
                        <a href="{{ route('storepage') }}" class="btn btn-outline-success btn-sm">Check store availability</a>
                    </div>
                </div>
            </div>

            <aside class="layout2-shipbox">
                <div class="layout2-summary-grid mb-3">
                    <div class="layout2-summary-card">
                        <span>Category</span>
                        <strong>{{ $details?->category?->name ?? 'N/A' }}</strong>
                    </div>
                    <div class="layout2-summary-card">
                        <span>Support</span>
                        <strong>Cash on delivery available</strong>
                    </div>
                    <div class="layout2-summary-card">
                        <span>Delivery</span>
                        <strong>Pickup & home delivery</strong>
                    </div>
                </div>

                <h6>Delivery & Service</h6>
                <div class="layout2-ship-row">
                    <i class="fa-solid fa-truck-fast"></i>
                    <div>
                        <strong>Regular Delivery</strong>
                        <span>Inside Dhaka 1-2 days, outside 2-4 days</span>
                    </div>
                </div>
                <div class="layout2-ship-row">
                    <i class="fa-solid fa-money-bill-wave"></i>
                    <div>
                        <strong>Cash on Delivery</strong>
                        <span>Pay after receiving product</span>
                    </div>
                </div>
                <div class="layout2-ship-row">
                    <i class="fa-solid fa-shield-halved"></i>
                    <div>
                        <strong>100% Authentic</strong>
                        <span>Seller verified quality assurance</span>
                    </div>
                </div>
                <div class="layout2-seller">
                    <strong>Seller: {{ $generalsetting?->name ?? 'Verified Store' }}</strong>
                    <p>Positive seller rating with active customer support.</p>
                </div>
            </aside>
        </div>

        <div class="layout2-panel">
            <div class="layout2-section-title">Product details</div>
            <div class="accordion layout2-accordion" id="layout2Accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="layout2DescHead">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#layout2Desc">
                            Description
                        </button>
                    </h2>
                    <div id="layout2Desc" class="accordion-collapse collapse show" data-bs-parent="#layout2Accordion">
                        <div class="accordion-body">
                            {!! $details?->description !!}
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="layout2CareHead">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#layout2Care">
                            Care Tips
                        </button>
                    </h2>
                    <div id="layout2Care" class="accordion-collapse collapse" data-bs-parent="#layout2Accordion">
                        <div class="accordion-body">
                            {!! $details?->care_tips !!}
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="layout2PolicyHead">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#layout2Policy">
                            Delivery & Return Policy
                        </button>
                    </h2>
                    <div id="layout2Policy" class="accordion-collapse collapse" data-bs-parent="#layout2Accordion">
                        <div class="accordion-body">
                            {!! $page?->description !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="layout2-related">
            <section class="related-product-section">
                <div class="custom-container px-0">
                    <div class="row mt-2">
                        <div class="col-12 text-center">
                            <div class="section-kicker">You may also like</div>
                            <h2 class="fw-bold mb-2" style="font-size: 28px; font-weight: 900; color: #0f172a;">Related Products</h2>
                            <p class="text-muted mb-0">More styles from the same category.</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="product-inner owl-carousel related_slider">
                                @foreach ($products as $key => $value)
                                    @include('frontEnd.partials.product-card', ['product' => $value, 'titleLimit' => 56])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            @if (($recentlyViewedProducts ?? collect())->isNotEmpty())
            <section class="related-product-section">
                <div class="custom-container px-0">
                    <div class="row mt-2">
                        <div class="col-12 text-center">
                            <div class="section-kicker">Continue browsing</div>
                            <h2 class="fw-bold mb-2" style="font-size: 28px; font-weight: 900; color: #0f172a;">Recently Viewed Products</h2>
                            <p class="text-muted mb-0">Products you checked recently.</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="product-inner owl-carousel recently_viewed_details_slider">
                                @foreach ($recentlyViewedProducts as $value)
                                    @include('frontEnd.partials.product-card', ['product' => $value, 'titleLimit' => 56])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @endif
        </div>
    </div>
</div>

@push('script')
    <script>
        document.addEventListener('click', function(event) {
            const thumb = event.target.closest('[data-layout2-thumb]');
            if (!thumb) {
                return;
            }

            const mainImage = document.querySelector('.layout2-main-image img');
            const imageSrc = thumb.getAttribute('data-image-src');

            if (mainImage && imageSrc) {
                mainImage.src = imageSrc;
            }
        });
    </script>
@endpush
