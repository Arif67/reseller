@php
    $cardButtonBg = $themeCustomization?->product_card_button_background_color ?? '#DC2626';
    $cardButtonHoverBg = $themeCustomization?->product_card_button_hover_background_color ?? '#B91C1C';
    $cardButtonFont = $themeCustomization?->product_card_button_font_color ?? '#FFFFFF';
    $cardBadgeBg = $themeCustomization?->product_card_badge_background_color ?? '#DC2626';
    $cardBadgeFont = $themeCustomization?->product_card_badge_font_color ?? '#FFFFFF';
    $cardPriceColor = $themeCustomization?->product_card_price_color ?? '#111827';
    $cardTitleAlignment = $themeCustomization?->product_card_title_alignment ?? 'left';
    $cardPriceAlignment = $themeCustomization?->product_card_price_alignment ?? 'left';
    $priceJustify = match ($cardPriceAlignment) {
        'center' => 'center',
        'right' => 'flex-end',
        default => 'flex-start',
    };
@endphp
<style>
    :root {
        --catalog-card-button-bg: {{ $cardButtonBg }};
        --catalog-card-button-hover-bg: {{ $cardButtonHoverBg }};
        --catalog-card-button-font: {{ $cardButtonFont }};
        --catalog-card-badge-bg: {{ $cardBadgeBg }};
        --catalog-card-badge-font: {{ $cardBadgeFont }};
        --catalog-card-price-color: {{ $cardPriceColor }};
        --catalog-card-title-align: {{ $cardTitleAlignment }};
        --catalog-card-price-align: {{ $cardPriceAlignment }};
        --catalog-card-price-justify: {{ $priceJustify }};
    }

    .catalog-product-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.6rem;
    }

    .np-product-card {
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid var(--card-border);
        border-radius: calc(var(--card-radius, 12) * 1px);
        background: var(--card-bg);
        box-shadow: 0 12px 24px rgb(15 23 42 / var(--card-shadow-opacity, 0.12));
        transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .np-product-card:hover {
        transform: translateY(-3px);
        border-color: rgba(148, 163, 184, 0.35);
        box-shadow: none;
    }

    .np-media {
        position: relative;
        display: block;
        overflow: hidden;
        border-radius: 0;
        background: var(--card-image-bg);
        aspect-ratio: 1 / 1.04;
    }

    .np-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .np-media .catalog-product-image {
        position: relative;
        inset: auto;
        width: 100%;
        height: 100%;
    }

    .np-media .catalog-product-image--primary,
    .np-media .catalog-product-image--secondary {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        transition: opacity 0.65s ease, transform 0.75s ease, filter 0.55s ease;
    }

    .np-media .catalog-product-image--primary {
        opacity: 1;
        transform: scale(1);
    }

    .np-media .catalog-product-image--secondary {
        opacity: 0;
        transform: scale(1.06);
        filter: saturate(1.06);
    }

    .has-hover-image:hover .catalog-product-image--primary {
        opacity: 0;
        transform: scale(0.98);
    }

    .has-hover-image:hover .catalog-product-image--secondary {
        opacity: 1;
        transform: scale(1.02);
    }

    .np-product-card:hover .catalog-product-image--primary {
        transform: scale(1.03);
    }

    .np-discount {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 20px;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
        background: var(--catalog-card-badge-bg) !important;
        color: var(--catalog-card-badge-font) !important;
        font-size: 0.68rem;
        font-weight: 700;
        box-shadow: none;
    }

    .np-body {
        padding: calc(var(--card-body-padding, 12) * 1px);
        display: grid;
        gap: 0.35rem;
        text-align: left;
    }

    .np-name {
        display: block;
        color: var(--card-title);
        font-size: 0.85rem;
        line-height: 1.35;
        font-weight: 700;
        min-height: 2.4em;
        text-decoration: none;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        text-align: var(--catalog-card-title-align);
    }

    .np-name:hover {
        color: var(--card-title);
        text-decoration: none;
    }

    .np-price {
        margin: 0;
        padding: 0;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: var(--catalog-card-price-justify);
        gap: 0.4rem;
        color: var(--catalog-card-price-color) !important;
        font-size: 0.86rem;
        font-weight: 800;
        line-height: 1.3;
        text-align: var(--catalog-card-price-align);
    }

    .np-price del {
        color: var(--card-old-price);
        font-size: 0.74rem;
        font-weight: 500;
    }

    .np-action-form {
        display: block;
        width: 100%;
        margin: 0;
    }

    .np-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 0;
        padding: 0.52rem 0.6rem;
        border: 0;
        border-radius: 8px;
        background: var(--catalog-card-button-bg) !important;
        color: var(--catalog-card-button-font) !important;
        font-size: 0.76rem;
        font-weight: 700;
        text-decoration: none;
        transition: background 0.2s ease, transform 0.2s ease;
        box-shadow: none !important;
    }

    .np-action-btn:hover {
        color: var(--catalog-card-button-font) !important;
        background: var(--catalog-card-button-hover-bg) !important;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .related_slider .np-item {
        margin: 4px;
    }

    @media (min-width: 576px) {
        .catalog-product-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (min-width: 768px) {
        .catalog-product-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.75rem;
        }
    }

    @media (min-width: 992px) {
        .catalog-product-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }
    }

    @media (min-width: 1200px) {
        .catalog-product-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .np-product-card {
            border-radius: 11px;
        }

        .np-body {
            padding: 0.58rem;
            gap: 0.3rem;
        }

        .np-name {
            font-size: 0.78rem;
            min-height: 2.35em;
        }

        .np-price {
            font-size: 0.78rem;
        }

        .np-action-btn {
            font-size: 0.72rem;
            padding: 0.48rem 0.52rem;
        }
    }
</style>
