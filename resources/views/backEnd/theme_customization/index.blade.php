@extends('backEnd.layouts.master')
@section('title', 'Theme Customization')

@section('css')
<style>
    .theme-builder-page {
        max-width: 1200px;
        margin: 0 auto;
    }

    .theme-builder-hero {
        border: 0;
        border-radius: 20px;
        padding: 28px;
        background: linear-gradient(135deg, #101827 0%, #1f3a5f 100%);
        color: #fff;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.18);
    }

    .theme-builder-hero p {
        max-width: 720px;
        margin: 10px 0 0;
        color: rgba(255, 255, 255, 0.82);
    }

    .theme-builder-form {
        display: grid;
        gap: 24px;
        margin-top: 24px;
    }

    .theme-section-card {
        border: 1px solid #e6ebf1;
        border-radius: 20px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .theme-section-head {
        padding: 20px 22px 14px;
        background: #f8fafc;
        border-bottom: 1px solid #edf2f7;
    }

    .theme-section-head h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
    }

    .theme-section-head p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .theme-section-body {
        padding: 22px;
        background: #fff;
    }

    .theme-control-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .theme-control-card {
        border: 1px solid #e6ebf1;
        border-radius: 18px;
        padding: 16px;
        background: #fff;
        min-width: 0;
    }

    .theme-control-card label {
        display: block;
        margin-bottom: 10px;
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }

    .theme-control-tools {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .theme-color-input {
        flex: 0 0 56px;
        width: 56px;
        height: 56px;
        border: 0;
        border-radius: 14px;
        padding: 4px;
        background: #fff;
        box-shadow: inset 0 0 0 1px #dbe4ee;
        cursor: pointer;
    }

    .theme-color-input::-webkit-color-swatch-wrapper {
        padding: 0;
    }

    .theme-color-input::-webkit-color-swatch {
        border: 0;
        border-radius: 10px;
    }

    .theme-color-input::-moz-color-swatch {
        border: 0;
        border-radius: 10px;
    }

    .theme-hex-wrap {
        flex: 1 1 auto;
        min-width: 0;
    }

    .theme-hex-input {
        height: 46px;
        border-radius: 12px;
        border: 1px solid #dbe4ee;
        font-weight: 600;
        color: #0f172a;
        background: #f8fafc;
    }

    .theme-hex-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.12);
        background: #fff;
    }

    .theme-preview-chip {
        margin-top: 10px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        border-radius: 999px;
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
    }

    .theme-preview-chip span {
        display: inline-block;
        width: 14px;
        height: 14px;
        border-radius: 999px;
        border: 1px solid rgba(15, 23, 42, 0.14);
    }

    .theme-form-footer {
        position: sticky;
        bottom: 12px;
        z-index: 10;
        margin-top: 8px;
        padding: 16px 18px;
        border: 1px solid #dbe4ee;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(10px);
        box-shadow: 0 18px 35px rgba(15, 23, 42, 0.1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .theme-form-footer p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
    }

    .theme-save-btn {
        min-width: 170px;
        height: 46px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 15px;
    }

    .theme-sortable-list {
        display: grid;
        gap: 12px;
    }

    .theme-sortable-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 16px;
        border: 1px solid #e6ebf1;
        border-radius: 16px;
        background: #fff;
        cursor: grab;
    }

    .theme-sortable-item.is-dragging {
        opacity: 0.6;
        border-style: dashed;
    }

    .theme-sortable-meta strong {
        display: block;
        color: #0f172a;
        font-size: 14px;
    }

    .theme-sortable-meta span {
        color: #64748b;
        font-size: 12px;
    }

    .theme-sortable-handle {
        color: #94a3b8;
        font-size: 18px;
        line-height: 1;
    }

    .theme-slider-config-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .theme-slider-config-card {
        border: 1px solid #e6ebf1;
        border-radius: 18px;
        padding: 16px;
        background: #fff;
    }

    .theme-slider-config-card h6 {
        margin: 0 0 6px;
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .theme-slider-config-card p {
        margin: 0 0 14px;
        color: #64748b;
        font-size: 12px;
    }

    .theme-slider-breakpoints {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .theme-slider-breakpoint label {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
    }

    @media (max-width: 991.98px) {
        .theme-control-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .theme-builder-hero {
            padding: 22px 18px;
            border-radius: 18px;
        }

        .theme-section-head,
        .theme-section-body {
            padding-left: 16px;
            padding-right: 16px;
        }

        .theme-control-card {
            padding: 14px;
        }

        .theme-control-tools {
            align-items: stretch;
        }

        .theme-color-input {
            flex-basis: 52px;
            width: 52px;
            height: 52px;
        }

        .theme-form-footer {
            position: static;
            flex-direction: column;
            align-items: stretch;
        }

        .theme-save-btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
@php
    $hasContainerMaxWidthColumn = \Illuminate\Support\Facades\Schema::hasTable('theme_customizations')
        && \Illuminate\Support\Facades\Schema::hasColumn('theme_customizations', 'container_max_width');
    $sectionOrderLabels = [
        'home_slider' => ['title' => 'Hero Slider', 'field' => 'show_home_slider'],
        'marketing_banners' => ['title' => 'Marketing Banners', 'field' => 'show_marketing_banners'],
        'crazy_deal' => ['title' => 'Crazy Deal', 'field' => 'show_crazy_deal'],
        'category_slider' => ['title' => 'Category Slider', 'field' => 'show_category_slider'],
        'category_products' => ['title' => 'Category-wise Products', 'field' => 'show_category_products'],
        'best_selling_products' => ['title' => 'Best Selling Products', 'field' => 'show_best_selling_products'],
        'featured_products' => ['title' => 'Featured Products', 'field' => 'show_featured_products'],
        'new_popular' => ['title' => 'New Popular', 'field' => 'show_new_popular'],
        'service_features' => ['title' => 'Service Features', 'field' => 'show_service_features'],
    ];
    $storedSectionOrder = json_decode(old('home_section_order', $themeCustomization?->home_section_order ?? ''), true);
    $fallbackSectionOrder = ['home_slider', 'marketing_banners', 'crazy_deal', 'category_slider', 'category_products', 'best_selling_products', 'featured_products', 'new_popular', 'service_features'];
    $homeSectionOrder = is_array($storedSectionOrder) ? $storedSectionOrder : $fallbackSectionOrder;
    foreach ($fallbackSectionOrder as $sectionKey) {
        if (! in_array($sectionKey, $homeSectionOrder, true)) {
            $homeSectionOrder[] = $sectionKey;
        }
    }
    $sliderSettingDefinitions = [
        'hotdeals_slider' => ['title' => 'Hot Deals Slider', 'description' => 'Used by classic hot deals blocks.'],
        'hotdeals_slider1' => ['title' => 'Wide Product Slider', 'description' => 'Used where full-width category product slider appears.'],
        'hotdeals_slider111' => ['title' => 'Banner Product Slider', 'description' => 'Used beside category banner image.'],
        'showcase_product_slider' => ['title' => 'Showcase Product Slider', 'description' => 'Best selling, featured products, and category-wise products slider.'],
        'product_slider' => ['title' => 'Generic Product Slider', 'description' => 'Used by standard product carousel sections.'],
        'product_slider2' => ['title' => 'Compact Product Slider', 'description' => 'Used by compact multi-column product carousel.'],
        'product_sliders3' => ['title' => 'Large Product Slider', 'description' => 'Used by larger desktop product carousel blocks.'],
        'product_slider_category' => ['title' => 'Category Slider Layout 1', 'description' => 'Products category slider, layout 1.'],
        'featured_category_layout2_slider' => ['title' => 'Category Slider Layout 2', 'description' => 'Products category slider, layout 2.'],
        'details_slider' => ['title' => 'Product Details Main Slider', 'description' => 'Main gallery slider on product details page.'],
        'related_slider' => ['title' => 'Related Products Slider', 'description' => 'You may also like slider on product details page.'],
        'thumb_slider' => ['title' => 'Product Thumbnail Slider', 'description' => 'Thumbnail image slider on product details page.'],
        'product_slider12' => ['title' => 'Subcategory Product Slider', 'description' => 'Used on subcategory and child category pages.'],
        'campaign_main_slider' => ['title' => 'Campaign Main Slider', 'description' => 'Primary slider used on campaign page.'],
        'review_slider' => ['title' => 'Review Slider', 'description' => 'Customer review slider on campaign page.'],
    ];
    $defaultSliderItemSettings = [
        'hotdeals_slider' => ['mobile' => 3, 'tablet' => 3, 'desktop' => 6],
        'hotdeals_slider1' => ['mobile' => 2, 'tablet' => 3, 'desktop' => 6],
        'hotdeals_slider111' => ['mobile' => 2, 'tablet' => 3, 'desktop' => 4],
        'showcase_product_slider' => ['mobile' => 2, 'tablet' => 3, 'desktop' => 5],
        'product_slider' => ['mobile' => 2, 'tablet' => 3, 'desktop' => 5],
        'product_slider2' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 4],
        'product_sliders3' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 6],
        'product_slider_category' => ['mobile' => 2, 'tablet' => 3, 'desktop' => 4],
        'featured_category_layout2_slider' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 8],
        'details_slider' => ['mobile' => 1, 'tablet' => 1, 'desktop' => 1],
        'related_slider' => ['mobile' => 2, 'tablet' => 3, 'desktop' => 5],
        'thumb_slider' => ['mobile' => 3, 'tablet' => 4, 'desktop' => 5],
        'product_slider12' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 6],
        'campaign_main_slider' => ['mobile' => 1, 'tablet' => 1, 'desktop' => 1],
        'review_slider' => ['mobile' => 1, 'tablet' => 2, 'desktop' => 5],
    ];
    $storedSliderItemSettings = json_decode($themeCustomization?->slider_item_settings ?? '', true);
    $sliderItemSettings = is_array($storedSliderItemSettings) ? $storedSliderItemSettings : [];
    foreach ($defaultSliderItemSettings as $sliderKey => $breakpoints) {
        foreach ($breakpoints as $breakpoint => $defaultCount) {
            $sliderItemSettings[$sliderKey][$breakpoint] = (int) old(
                "slider_item_settings.$sliderKey.$breakpoint",
                $sliderItemSettings[$sliderKey][$breakpoint] ?? $defaultCount
            );
        }
    }
    $sections = [
        [
            'title' => 'Header Colors',
            'description' => 'Top header, search area, and navigation colors for the storefront header.',
            'fields' => [
                ['name' => 'top_bar_background_color', 'label' => 'Top Bar Background', 'default' => '#FF5722'],
                ['name' => 'search_bar_background_color', 'label' => 'Search Bar Background', 'default' => '#000000'],
                ['name' => 'search_font_color', 'label' => 'Search Icon / Font Color', 'default' => '#FFFFFF'],
                ['name' => 'navbar_background_color', 'label' => 'Navbar Background', 'default' => '#e6e1e1'],
            ],
        ],
        [
            'title' => 'Action Buttons',
            'description' => 'Colors used for track order, login, cart, and menu toggle actions.',
            'fields' => [
                ['name' => 'track_order_background_color', 'label' => 'Track Background', 'default' => '#677279'],
                ['name' => 'track_order_font_color', 'label' => 'Track Font', 'default' => '#FFFFFF'],
                ['name' => 'login_background_color', 'label' => 'Login Background', 'default' => '#677279'],
                ['name' => 'login_font_color', 'label' => 'Login Font', 'default' => '#FFFFFF'],
                ['name' => 'cart_background_color', 'label' => 'Cart Background', 'default' => '#677279'],
                ['name' => 'cart_font_color', 'label' => 'Cart Font', 'default' => '#FFFFFF'],
                ['name' => 'toggle_font_color', 'label' => 'Toggle Font', 'default' => '#FFFFFF'],
            ],
        ],
        [
            'title' => 'Card Component',
            'description' => 'Card Component styling for product cards.',
            'fields' => array_values(array_filter([
                $hasContainerMaxWidthColumn
                    ? ['name' => 'container_max_width', 'label' => 'Main Container Width (px)', 'default' => 1520, 'type' => 'number', 'min' => 960, 'max' => 2200]
                    : null,
                ['name' => 'card_bg_color', 'label' => 'Card Background', 'default' => '#ffffff'],
                ['name' => 'card_border_color', 'label' => 'Card Border', 'default' => '#e2e8f0'],
                ['name' => 'card_title_color', 'label' => 'Card Title Color', 'default' => '#0f172a'],
                ['name' => 'card_old_price_color', 'label' => 'Old Price Color', 'default' => '#94a3b8'],
                ['name' => 'card_image_bg_color', 'label' => 'Image Background', 'default' => '#f8fafc'],
                ['name' => 'card_radius', 'label' => 'Card Radius (px)', 'default' => 12, 'type' => 'number', 'min' => 0, 'max' => 40],
                ['name' => 'card_body_padding', 'label' => 'Card Padding (px)', 'default' => 12, 'type' => 'number', 'min' => 6, 'max' => 28],
                ['name' => 'card_shadow_opacity', 'label' => 'Card Shadow Opacity (0-1)', 'default' => 0.12, 'type' => 'number', 'min' => 0, 'max' => 1, 'step' => 0.01],
            ])),
        ],
        [
            'title' => 'Product Card Colors',
            'description' => 'Shared product listing card colors for category, subcategory, child category, search, hot deals, and related products.',
            'fields' => [
                ['name' => 'product_card_button_background_color', 'label' => 'Order Button Background', 'default' => '#DC2626'],
                ['name' => 'product_card_button_hover_background_color', 'label' => 'Order Button Hover', 'default' => '#B91C1C'],
                ['name' => 'product_card_button_font_color', 'label' => 'Order Button Font', 'default' => '#FFFFFF'],
                ['name' => 'product_card_badge_background_color', 'label' => 'Discount Badge Background', 'default' => '#DC2626'],
                ['name' => 'product_card_badge_font_color', 'label' => 'Discount Badge Font', 'default' => '#FFFFFF'],
                ['name' => 'product_card_price_color', 'label' => 'Price Color', 'default' => '#111827'],
                [
                    'name' => 'product_card_title_alignment',
                    'label' => 'Title Alignment',
                    'default' => 'left',
                    'type' => 'select',
                    'options' => [
                        ['value' => 'left', 'label' => 'Left'],
                        ['value' => 'center', 'label' => 'Center'],
                        ['value' => 'right', 'label' => 'Right'],
                    ],
                ],
                [
                    'name' => 'product_card_price_alignment',
                    'label' => 'Price Alignment',
                    'default' => 'left',
                    'type' => 'select',
                    'options' => [
                        ['value' => 'left', 'label' => 'Left'],
                        ['value' => 'center', 'label' => 'Center'],
                        ['value' => 'right', 'label' => 'Right'],
                    ],
                ],
            ],
        ],
        [
            'title' => 'Palette',
            'description' => 'Primary, accent, surface, and text colors used across homepage sections.',
            'fields' => [
                ['name' => 'primary_color', 'label' => 'Primary Color', 'default' => '#0f172a'],
                ['name' => 'accent_color', 'label' => 'Accent Color', 'default' => '#dc2626'],
                ['name' => 'surface_color', 'label' => 'Surface Color', 'default' => '#ffffff'],
                ['name' => 'section_bg_color', 'label' => 'Section Background', 'default' => '#f8fafc'],
                ['name' => 'text_color', 'label' => 'Text Color', 'default' => '#0f172a'],
                ['name' => 'muted_text_color', 'label' => 'Muted Text', 'default' => '#64748b'],
            ],
        ],
        [
            'title' => 'Footer',
            'description' => 'Footer top and bottom colors, headings, links, and accent highlights.',
            'fields' => [
                ['name' => 'footer_top_background_color', 'label' => 'Top Background', 'default' => '#020617'],
                ['name' => 'footer_text_color', 'label' => 'Top Text', 'default' => '#CBD5E1'],
                ['name' => 'footer_heading_color', 'label' => 'Heading Color', 'default' => '#FFFFFF'],
                ['name' => 'footer_link_color', 'label' => 'Link Color', 'default' => '#F8FAFC'],
                ['name' => 'footer_link_hover_color', 'label' => 'Link Hover Color', 'default' => '#F59E0B'],
                ['name' => 'footer_accent_color', 'label' => 'Accent Color', 'default' => '#F59E0B'],
                ['name' => 'footer_bottom_background_color', 'label' => 'Bottom Background', 'default' => '#000000'],
                ['name' => 'footer_bottom_text_color', 'label' => 'Bottom Text', 'default' => '#E2E8F0'],
            ],
        ],
        [
            'title' => 'View Details Button',
            'description' => 'Control Best Selling & Featured product card “View Details” button visibility and colors.',
            'fields' => [
                ['name' => 'view_details_bg_color', 'label' => 'Button Background', 'default' => '#f8fafc'],
                ['name' => 'view_details_font_color', 'label' => 'Button Font', 'default' => '#0f172a'],
            ],
            'toggle' => [
                'name' => 'show_view_details',
                'label' => 'Show View Details',
            ],
        ],
        [
            'title' => 'Product Details Layout',
            'description' => 'Choose which product details page layout should be shown on the storefront.',
            'fields' => [
                [
                    'name' => 'product_details_layout',
                    'label' => 'Layout Style',
                    'default' => 1,
                    'type' => 'select',
                    'options' => [
                        ['value' => 1, 'label' => 'Layout 1'],
                        ['value' => 2, 'label' => 'Layout 2 (Daraz Style)'],
                        ['value' => 3, 'label' => 'Layout 3 (Vibrant Style)'],
                    ],
                ],
            ],
        ],
        [
            'title' => 'Homepage Slider Layout',
            'description' => 'Choose which homepage hero slider layout should be shown on the storefront.',
            'fields' => [
                [
                    'name' => 'slider_layout',
                    'label' => 'Slider Style',
                    'default' => 1,
                    'type' => 'select',
                    'options' => [
                        ['value' => 1, 'label' => 'Layout 1'],
                        ['value' => 2, 'label' => 'Layout 2'],
                    ],
                ],
            ],
        ],
        [
            'title' => 'Products Category Layout',
            'description' => 'Choose which Products Category section layout and position should be shown on the storefront.',
            'fields' => [
                [
                    'name' => 'product_category_layout',
                    'label' => 'Category Style',
                    'default' => 1,
                    'type' => 'select',
                    'options' => [
                        ['value' => 1, 'label' => 'Layout 1'],
                        ['value' => 2, 'label' => 'Layout 2 (Simple Slider)'],
                    ],
                ],
                [
                    'name' => 'product_category_position',
                    'label' => 'Section Position',
                    'default' => 'default',
                    'type' => 'select',
                    'options' => [
                        ['value' => 'default', 'label' => 'Default Position'],
                        ['value' => 'after_new_popular', 'label' => 'Below New and Popular'],
                    ],
                ],
                [
                    'name' => 'product_category_product_limit',
                    'label' => 'Products Per Category',
                    'default' => 8,
                    'type' => 'number',
                    'min' => 4,
                    'max' => 16,
                ],
            ],
        ],
        [
            'title' => 'Homepage Section Visibility',
            'description' => 'Control which homepage sections are visible on the storefront.',
            'fields' => [
                ['name' => 'show_home_slider', 'label' => 'Show Hero Slider', 'type' => 'toggle', 'default' => 1],
                ['name' => 'show_marketing_banners', 'label' => 'Show Marketing Banners', 'type' => 'toggle', 'default' => 1],
                ['name' => 'show_crazy_deal', 'label' => 'Show Crazy Deal', 'type' => 'toggle', 'default' => 1],
                ['name' => 'show_category_slider', 'label' => 'Show Category Slider', 'type' => 'toggle', 'default' => 1],
                ['name' => 'show_category_products', 'label' => 'Show Category-wise Products', 'type' => 'toggle', 'default' => 1],
                ['name' => 'show_best_selling_products', 'label' => 'Show Best Selling Products', 'type' => 'toggle', 'default' => 1],
                ['name' => 'show_featured_products', 'label' => 'Show Featured Products', 'type' => 'toggle', 'default' => 1],
                ['name' => 'show_new_popular', 'label' => 'Show New Popular', 'type' => 'toggle', 'default' => 1],
                ['name' => 'show_service_features', 'label' => 'Show Service Features', 'type' => 'toggle', 'default' => 1],
            ],
        ],
        [
            'title' => 'Homepage Section Order',
            'description' => 'Drag and drop homepage sections to control which block appears first on the storefront.',
            'fields' => [],
            'custom' => 'home_section_order',
        ],
        [
            'title' => 'Slider Item Count',
            'description' => 'Set how many items each slider shows on mobile, tablet, and desktop.',
            'fields' => [],
            'custom' => 'slider_item_settings',
        ],
    ];
@endphp

<div class="container-fluid theme-builder-page">
    <div class="mb-4">
        <ul class="nav nav-pills flex-wrap gap-2">
            @foreach ($sections as $section)
                @php
                    $sectionId = \Illuminate\Support\Str::slug($section['title']);
                @endphp
                <li class="nav-item">
                    <a class="nav-link" href="#{{ $sectionId }}">{{ $section['title'] }}</a>
                </li>
            @endforeach
        </ul>
    </div>

    <form action="{{ route('theme.customization.update') }}" method="POST" class="theme-builder-form">
        @csrf

        @foreach ($sections as $section)
            @php
                $sectionId = \Illuminate\Support\Str::slug($section['title']);
            @endphp
            <div class="theme-section-card card" id="{{ $sectionId }}">
                <div class="theme-section-head">
                    <h5>{{ $section['title'] }}</h5>
                    <p>{{ $section['description'] }}</p>
                </div>
                <div class="theme-section-body">
                    @if (($section['custom'] ?? null) === 'home_section_order')
                        <input type="hidden" name="home_section_order" id="home-section-order-input" value="{{ e(json_encode($homeSectionOrder)) }}">
                        <div class="theme-sortable-list" data-home-section-sortable>
                            @foreach ($homeSectionOrder as $sectionKey)
                                @php
                                    $sectionConfig = $sectionOrderLabels[$sectionKey] ?? null;
                                    $sectionField = $sectionConfig['field'] ?? null;
                                    $sectionVisible = $sectionField ? (int) old($sectionField, $themeCustomization->{$sectionField} ?? 1) === 1 : true;
                                @endphp
                                @if ($sectionConfig)
                                    <div class="theme-sortable-item" draggable="true" data-section-key="{{ $sectionKey }}">
                                        <div class="theme-sortable-meta">
                                            <strong>{{ $sectionConfig['title'] }}</strong>
                                            <span>{{ $sectionVisible ? 'Visible on homepage' : 'Hidden, but order preserved' }}</span>
                                        </div>
                                        <div class="theme-sortable-handle">
                                            <i class="fa-solid fa-grip-vertical"></i>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @elseif (($section['custom'] ?? null) === 'slider_item_settings')
                        <div class="theme-slider-config-grid">
                            @foreach ($sliderSettingDefinitions as $sliderKey => $sliderConfig)
                                <div class="theme-slider-config-card">
                                    <h6>{{ $sliderConfig['title'] }}</h6>
                                    <p>{{ $sliderConfig['description'] }}</p>
                                    <div class="theme-slider-breakpoints">
                                        @foreach (['mobile' => 'Mobile', 'tablet' => 'Tablet', 'desktop' => 'Desktop'] as $breakpointKey => $breakpointLabel)
                                            <div class="theme-slider-breakpoint">
                                                <label for="slider-{{ $sliderKey }}-{{ $breakpointKey }}">{{ $breakpointLabel }}</label>
                                                <input
                                                    type="number"
                                                    min="1"
                                                    max="12"
                                                    class="form-control theme-hex-input"
                                                    id="slider-{{ $sliderKey }}-{{ $breakpointKey }}"
                                                    name="slider_item_settings[{{ $sliderKey }}][{{ $breakpointKey }}]"
                                                    value="{{ $sliderItemSettings[$sliderKey][$breakpointKey] ?? 1 }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                    @if (!empty($section['toggle']))
                        @php
                            $toggleName = $section['toggle']['name'];
                            $toggleValue = (int) (old($toggleName, $themeCustomization->{$toggleName} ?? 1));
                        @endphp
                        <div class="theme-toggle-row mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="{{ $toggleName }}" name="{{ $toggleName }}" value="1" @checked($toggleValue === 1)>
                                <label class="form-check-label" for="{{ $toggleName }}">{{ $section['toggle']['label'] }}</label>
                            </div>
                        </div>
                    @endif
                    <div class="theme-control-grid">
                        @foreach ($section['fields'] as $field)
                            @php
                                $fieldName = $field['name'];
                                $fieldValue = old($fieldName, $themeCustomization->{$fieldName} ?? $field['default']);
                                $fieldId = str_replace('_', '-', $fieldName);
                                $fieldType = $field['type'] ?? 'color';
                            @endphp
                            <div class="theme-control-card">
                                <label for="{{ $fieldId }}">{{ $field['label'] }}</label>
                                @if ($fieldType === 'color')
                                    <div class="theme-control-tools">
                                        <input
                                            type="color"
                                            class="theme-color-input"
                                            id="{{ $fieldId }}"
                                            name="{{ $fieldName }}"
                                            value="{{ $fieldValue }}"
                                            data-color-source="{{ $fieldId }}-text">

                                        <div class="theme-hex-wrap">
                                            <input
                                                type="text"
                                                class="form-control theme-hex-input @error($fieldName) is-invalid @enderror"
                                                id="{{ $fieldId }}-text"
                                                value="{{ $fieldValue }}"
                                                data-color-target="{{ $fieldId }}"
                                                inputmode="text"
                                                autocomplete="off">
                                            <div class="theme-preview-chip">
                                                <span style="background: {{ $fieldValue }};"></span>
                                                Preview
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @if ($fieldType === 'select')
                                        <select
                                            class="form-select theme-hex-input @error($fieldName) is-invalid @enderror"
                                            id="{{ $fieldId }}"
                                            name="{{ $fieldName }}">
                                            @foreach ($field['options'] as $option)
                                                <option value="{{ $option['value'] }}" @selected((string) $fieldValue === (string) $option['value'])>
                                                    {{ $option['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @elseif ($fieldType === 'toggle')
                                        @php($fieldToggleValue = (int) old($fieldName, $themeCustomization->{$fieldName} ?? $field['default']))
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                role="switch"
                                                id="{{ $fieldId }}"
                                                name="{{ $fieldName }}"
                                                value="1"
                                                @checked($fieldToggleValue === 1)>
                                        </div>
                                    @else
                                        <div class="theme-control-tools">
                                            <input
                                                type="{{ $fieldType }}"
                                                class="form-control theme-hex-input @error($fieldName) is-invalid @enderror"
                                                id="{{ $fieldId }}"
                                                name="{{ $fieldName }}"
                                                value="{{ $fieldValue }}"
                                                @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
                                                @if(isset($field['max'])) max="{{ $field['max'] }}" @endif
                                                @if(isset($field['step'])) step="{{ $field['step'] }}" @endif>
                                        </div>
                                    @endif
                                @endif
                                @error($fieldName)
                                    <span class="invalid-feedback d-block mt-2" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        @endforeach

        <div class="theme-form-footer">
            <p>Save korle storefront header, footer, ar shared product card color instantly updated hobe. Invalid hex dile validation error dekhabe.</p>
            <button type="submit" class="btn btn-success theme-save-btn">Save Changes</button>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    (function() {
        function normalizeHex(value) {
            if (!value) {
                return '';
            }

            let normalized = value.trim();
            if (!normalized.startsWith('#')) {
                normalized = '#' + normalized;
            }

            return normalized.toUpperCase();
        }

        function isValidHex(value) {
            return /^#([0-9A-F]{3}|[0-9A-F]{6})$/i.test(value);
        }

        document.querySelectorAll('[data-color-source]').forEach(function(colorInput) {
            const textInput = document.getElementById(colorInput.dataset.colorSource);
            const preview = colorInput.closest('.theme-control-card').querySelector('.theme-preview-chip span');

            if (!textInput || !preview) {
                return;
            }

            colorInput.addEventListener('input', function() {
                textInput.value = colorInput.value.toUpperCase();
                preview.style.background = colorInput.value;
            });

            textInput.addEventListener('input', function() {
                const value = normalizeHex(textInput.value);
                textInput.value = value;

                if (isValidHex(value)) {
                    colorInput.value = value;
                    preview.style.background = value;
                }
            });
        });

        const sortableList = document.querySelector('[data-home-section-sortable]');
        const orderInput = document.getElementById('home-section-order-input');

        if (sortableList && orderInput) {
            let draggedItem = null;

            function syncOrder() {
                const order = Array.from(sortableList.querySelectorAll('[data-section-key]')).map(function(item) {
                    return item.dataset.sectionKey;
                });

                orderInput.value = JSON.stringify(order);
            }

            sortableList.querySelectorAll('.theme-sortable-item').forEach(function(item) {
                item.addEventListener('dragstart', function() {
                    draggedItem = item;
                    item.classList.add('is-dragging');
                });

                item.addEventListener('dragend', function() {
                    item.classList.remove('is-dragging');
                    draggedItem = null;
                    syncOrder();
                });

                item.addEventListener('dragover', function(event) {
                    event.preventDefault();

                    if (!draggedItem || draggedItem === item) {
                        return;
                    }

                    const bounds = item.getBoundingClientRect();
                    const shouldInsertAfter = (event.clientY - bounds.top) > bounds.height / 2;

                    if (shouldInsertAfter) {
                        item.after(draggedItem);
                    } else {
                        item.before(draggedItem);
                    }
                });
            });

            syncOrder();
        }
    })();
</script>
@endsection
