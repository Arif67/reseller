@php
    $resolvedSearchFontColor = $themeCustomization?->search_font_color;

    if (! $resolvedSearchFontColor) {
        $searchButtonBackground = $themeCustomization?->navbar_background_color ?? '#e6e1e1';
        $normalizedSearchButtonBackground = ltrim($searchButtonBackground, '#');

        if (strlen($normalizedSearchButtonBackground) === 3) {
            $normalizedSearchButtonBackground = collect(str_split($normalizedSearchButtonBackground))
                ->map(fn ($part) => $part . $part)
                ->implode('');
        }

        $resolvedSearchFontColor = '#FFFFFF';

        if (preg_match('/^[A-Fa-f0-9]{6}$/', $normalizedSearchButtonBackground)) {
            $red = hexdec(substr($normalizedSearchButtonBackground, 0, 2));
            $green = hexdec(substr($normalizedSearchButtonBackground, 2, 2));
            $blue = hexdec(substr($normalizedSearchButtonBackground, 4, 2));
            $luminance = (0.299 * $red) + (0.587 * $green) + (0.114 * $blue);
            $resolvedSearchFontColor = $luminance > 186 ? '#111111' : '#000000';
        }
    }
@endphp
:root {
    --theme-top-bar-bg: {{ $themeCustomization?->top_bar_background_color ?? '#FF5722' }};
    --theme-search-bar-bg: {{ $themeCustomization?->search_bar_background_color ?? '#000000' }};
    --theme-navbar-bg: {{ $themeCustomization?->navbar_background_color ?? '#e6e1e1' }};
    --theme-track-order-bg: {{ $themeCustomization?->track_order_background_color ?? '#677279' }};
    --theme-track-order-color: {{ $themeCustomization?->track_order_font_color ?? '#FFFFFF' }};
    --theme-login-bg: {{ $themeCustomization?->login_background_color ?? '#677279' }};
    --theme-login-color: {{ $themeCustomization?->login_font_color ?? '#FFFFFF' }};
    --theme-cart-bg: {{ $themeCustomization?->cart_background_color ?? '#677279' }};
    --theme-cart-color: {{ $themeCustomization?->cart_font_color ?? '#FFFFFF' }};
    --theme-toggle-color: {{ $themeCustomization?->toggle_font_color ?? '#000000' }};
    --theme-search-icon-color: {{ $resolvedSearchFontColor }};
    --product-card-btn-bg: {{ $themeCustomization?->product_card_button_background_color ?? '#DC2626' }};
    --product-card-btn-hover: {{ $themeCustomization?->product_card_button_hover_background_color ?? '#B91C1C' }};
    --product-card-btn-color: {{ $themeCustomization?->product_card_button_font_color ?? '#FFFFFF' }};
    --product-card-badge-bg: {{ $themeCustomization?->product_card_badge_background_color ?? '#DC2626' }};
    --product-card-badge-color: {{ $themeCustomization?->product_card_badge_font_color ?? '#FFFFFF' }};
    --product-card-price-color: {{ $themeCustomization?->product_card_price_color ?? '#111827' }};
    --card-bg: {{ $themeCustomization?->card_bg_color ?? '#ffffff' }};
    --card-border: {{ $themeCustomization?->card_border_color ?? '#e2e8f0' }};
    --card-title: {{ $themeCustomization?->card_title_color ?? '#0f172a' }};
    --card-old-price: {{ $themeCustomization?->card_old_price_color ?? '#94a3b8' }};
    --card-image-bg: {{ $themeCustomization?->card_image_bg_color ?? '#f8fafc' }};
    --theme-container-width: {{ (int) ($themeCustomization?->container_max_width ?? 1520) }}px;
    --card-radius: {{ $themeCustomization?->card_radius ?? 12 }};
    --card-body-padding: {{ $themeCustomization?->card_body_padding ?? 12 }};
    --card-shadow-opacity: {{ $themeCustomization?->card_shadow_opacity ?? 0.12 }};
    --theme-view-details-bg: {{ $themeCustomization?->view_details_bg_color ?? '#f8fafc' }};
    --theme-view-details-color: {{ $themeCustomization?->view_details_font_color ?? '#0f172a' }};
    --theme-primary: {{ $themeCustomization?->primary_color ?? '#0f172a' }};
    --theme-accent: {{ $themeCustomization?->accent_color ?? '#dc2626' }};
    --theme-surface: {{ $themeCustomization?->surface_color ?? '#ffffff' }};
    --theme-section-bg: {{ $themeCustomization?->section_bg_color ?? '#f8fafc' }};
    --theme-text: {{ $themeCustomization?->text_color ?? '#0f172a' }};
    --theme-muted: {{ $themeCustomization?->muted_text_color ?? '#64748b' }};
    --theme-footer-top-bg: {{ $themeCustomization?->footer_top_background_color ?? '#020617' }};
    --theme-footer-text: {{ $themeCustomization?->footer_text_color ?? '#CBD5E1' }};
    --theme-footer-heading: {{ $themeCustomization?->footer_heading_color ?? '#FFFFFF' }};
    --theme-footer-link: {{ $themeCustomization?->footer_link_color ?? '#F8FAFC' }};
    --theme-footer-link-hover: {{ $themeCustomization?->footer_link_hover_color ?? '#F59E0B' }};
    --theme-footer-accent: {{ $themeCustomization?->footer_accent_color ?? '#F59E0B' }};
    --theme-footer-bottom-bg: {{ $themeCustomization?->footer_bottom_background_color ?? '#000000' }};
    --theme-footer-bottom-text: {{ $themeCustomization?->footer_bottom_text_color ?? '#E2E8F0' }};
}
