<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeCustomization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Toastr;

class ThemeCustomizationController extends Controller
{
    private const HOME_SECTIONS = [
        'home_slider',
        'marketing_banners',
        'promo_strip',
        'crazy_deal',
        'category_slider',
        'category_products',
        'best_selling_products',
        'featured_products',
        'new_popular',
        'service_features',
        'all_products',
    ];

    /**
     * Editable text fields for the reseller landing hero (see migration
     * 2026_06_10_000001_add_hero_to_theme_customizations_table).
     */
    private const HERO_TEXT_FIELDS = [
        'hero_badge',
        'hero_title',
        'hero_highlight',
        'hero_subtitle',
        'hero_primary_text',
        'hero_primary_link',
        'hero_secondary_text',
        'hero_secondary_link',
        'hero_visual_title',
        'hero_visual_text',
        'hero_stat1_value',
        'hero_stat1_label',
        'hero_stat2_value',
        'hero_stat2_label',
        'hero_stat3_value',
        'hero_stat3_label',
        'hero_stat4_value',
        'hero_stat4_label',
    ];

    public function index()
    {
        $defaults = [
            'top_bar_background_color' => '#FF5722',
            'search_bar_background_color' => '#000000',
            'search_font_color' => '#FFFFFF',
            'navbar_background_color' => '#e6e1e1',
            'track_order_background_color' => '#677279',
            'track_order_font_color' => '#FFFFFF',
            'login_background_color' => '#677279',
            'login_font_color' => '#FFFFFF',
            'cart_background_color' => '#677279',
            'cart_font_color' => '#FFFFFF',
            'toggle_font_color' => '#FFFFFF',
            'product_card_button_background_color' => '#DC2626',
            'product_card_button_hover_background_color' => '#B91C1C',
            'product_card_button_font_color' => '#FFFFFF',
            'product_card_badge_background_color' => '#DC2626',
            'product_card_badge_font_color' => '#FFFFFF',
            'product_card_price_color' => '#111827',
            'product_card_title_alignment' => 'left',
            'product_card_price_alignment' => 'left',
            'card_bg_color' => '#ffffff',
            'card_border_color' => '#e2e8f0',
            'card_title_color' => '#0f172a',
            'card_old_price_color' => '#94a3b8',
            'card_image_bg_color' => '#f8fafc',
            'card_radius' => 12,
            'card_body_padding' => 12,
            'card_shadow_opacity' => 0.12,
            'show_view_details' => 1,
            'view_details_bg_color' => '#f8fafc',
            'view_details_font_color' => '#0f172a',
            'product_details_layout' => 1,
            'focused_checkout' => 0,
            'search_button_style' => 'full',
            'product_card_layout' => 'default',
            'slider_layout' => 1,
            'slider_item_settings' => json_encode($this->defaultSliderItemSettings()),
            'product_category_layout' => 1,
            'product_category_position' => 'default',
            'product_category_display_mode' => 'category_products',
            'product_category_product_limit' => 8,
            'show_home_slider' => 1,
            'show_marketing_banners' => 1,
            'show_crazy_deal' => 1,
            'show_category_slider' => 1,
            'show_category_products' => 1,
            'show_featured_categories' => 1,
            'show_best_selling_products' => 1,
            'show_featured_products' => 1,
            'show_new_popular' => 1,
            'show_service_features' => 1,
            'show_all_products' => 1,
            'home_section_order' => json_encode($this->defaultHomeSectionOrder('default')),
            'primary_color' => '#0f172a',
            'accent_color' => '#dc2626',
            'surface_color' => '#ffffff',
            'section_bg_color' => '#f8fafc',
            'text_color' => '#0f172a',
            'muted_text_color' => '#64748b',
            'footer_top_background_color' => '#020617',
            'footer_text_color' => '#CBD5E1',
            'footer_heading_color' => '#FFFFFF',
            'footer_link_color' => '#F8FAFC',
            'footer_link_hover_color' => '#F59E0B',
            'footer_accent_color' => '#F59E0B',
            'footer_bottom_background_color' => '#000000',
            'footer_bottom_text_color' => '#E2E8F0',
        ];

        if ($this->hasThemeCustomizationColumn('container_max_width')) {
            $defaults['container_max_width'] = 1520;
        }

        $themeCustomization = ThemeCustomization::firstOrCreate([], $defaults);

        return view('backEnd.theme_customization.index', compact('themeCustomization'));
    }

    public function update(Request $request)
    {
        $rules = [
            'top_bar_background_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'search_bar_background_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'search_font_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'navbar_background_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'track_order_background_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'track_order_font_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'login_background_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'login_font_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'cart_background_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'cart_font_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'toggle_font_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'product_card_button_background_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'product_card_button_hover_background_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'product_card_button_font_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'product_card_badge_background_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'product_card_badge_font_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'product_card_price_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'product_card_title_alignment' => ['required', 'string', 'in:left,center,right'],
            'product_card_price_alignment' => ['required', 'string', 'in:left,center,right'],
            'card_bg_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'card_border_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'card_title_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'card_old_price_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'card_image_bg_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'card_radius' => ['required', 'integer', 'min:0', 'max:40'],
            'card_body_padding' => ['required', 'integer', 'min:6', 'max:28'],
            'card_shadow_opacity' => ['required', 'numeric', 'min:0', 'max:1'],
            'view_details_bg_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'view_details_font_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'product_details_layout' => ['required', 'integer', 'in:1,2,3'],
            'focused_checkout' => ['nullable', 'boolean'],
            'search_button_style' => ['required', 'string', 'in:full,compact,icon'],
            'product_card_layout' => ['required', 'string', 'in:default,daraz'],
            'slider_layout' => ['required', 'integer', 'in:1,2,3'],
            'slider_item_settings' => ['nullable'],
            'product_category_layout' => ['required', 'integer', 'in:1,2'],
            'product_category_position' => ['required', 'string', 'in:default,after_new_popular'],
            'product_category_product_limit' => ['required', 'integer', 'min:4', 'max:16'],
            'show_home_slider' => ['nullable', 'boolean'],
            'show_marketing_banners' => ['nullable', 'boolean'],
            'show_crazy_deal' => ['nullable', 'boolean'],
            'show_category_slider' => ['nullable', 'boolean'],
            'show_category_products' => ['nullable', 'boolean'],
            'show_featured_categories' => ['nullable', 'boolean'],
            'show_best_selling_products' => ['nullable', 'boolean'],
            'show_featured_products' => ['nullable', 'boolean'],
            'show_new_popular' => ['nullable', 'boolean'],
            'show_service_features' => ['nullable', 'boolean'],
            'show_all_products' => ['nullable', 'boolean'],
            'home_section_order' => ['nullable', 'string'],
            'primary_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'accent_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'surface_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'section_bg_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'text_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'muted_text_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'footer_top_background_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'footer_text_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'footer_heading_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'footer_link_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'footer_link_hover_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'footer_accent_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'footer_bottom_background_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'footer_bottom_text_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ];

        if ($this->hasThemeCustomizationColumn('container_max_width')) {
            $rules['container_max_width'] = ['required', 'integer', 'min:960', 'max:2200'];
        }

        $validated = $request->validate($rules);

        $validated['show_view_details'] = $request->has('show_view_details') ? 1 : 0;
        foreach ([
            'show_home_slider',
            'show_marketing_banners',
            'show_crazy_deal',
            'show_category_slider',
            'show_category_products',
            'show_featured_categories',
            'show_best_selling_products',
            'show_featured_products',
            'show_new_popular',
            'show_service_features',
            'show_all_products',
            'focused_checkout',
        ] as $toggleField) {
            $validated[$toggleField] = $request->has($toggleField) ? 1 : 0;
        }

        $validated['home_section_order'] = json_encode(
            $this->sanitizeHomeSectionOrder(
                $request->input('home_section_order'),
                $validated['product_category_position'] ?? 'default'
            )
        );
        $validated['slider_item_settings'] = json_encode(
            $this->sanitizeSliderItemSettings($request->input('slider_item_settings', []))
        );

        if (! $this->hasThemeCustomizationColumn('container_max_width')) {
            unset($validated['container_max_width']);
        }

        $themeCustomization = ThemeCustomization::firstOrCreate([]);
        $themeCustomization->update($validated);

        Cache::forget('shared_view_data_v2');
        Cache::forget('shared_view_data_v3');
        Cache::forget('shared_view_data_v4');
        foreach (range(4, 16) as $limit) {
            Cache::forget('home_categories_v2_' . $limit);
        }
        \App\Http\Controllers\Frontend\LandingController::flushCache();

        Toastr::success('Success', 'Theme customization updated successfully');
        return redirect()->route('theme.customization.index');
    }

    /**
     * Dedicated editor page for the reseller landing page hero section.
     */
    public function hero()
    {
        $themeCustomization = ThemeCustomization::firstOrCreate([]);

        return view('backEnd.theme_customization.hero', compact('themeCustomization'));
    }

    public function heroUpdate(Request $request)
    {
        if (! $this->hasThemeCustomizationColumn('hero_title')) {
            Toastr::error('Please run database migrations first.', 'Hero columns missing');
            return redirect()->route('theme.hero.index');
        }

        $rules = ['hero_bg_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/']];
        foreach (self::HERO_TEXT_FIELDS as $heroField) {
            $rules[$heroField] = ['nullable', 'string', 'max:500'];
        }

        $validated = $request->validate($rules);

        $themeCustomization = ThemeCustomization::firstOrCreate([]);
        $themeCustomization->update($validated);

        Cache::forget('shared_view_data_v2');
        Cache::forget('shared_view_data_v3');
        Cache::forget('shared_view_data_v4');
        \App\Http\Controllers\Frontend\LandingController::flushCache();

        Toastr::success('Success', 'Landing hero updated successfully');
        return redirect()->route('theme.hero.index');
    }

    private function sanitizeHomeSectionOrder(?string $rawOrder, string $productCategoryPosition = 'default'): array
    {
        $decoded = json_decode($rawOrder ?? '', true);

        if (! is_array($decoded)) {
            return $this->defaultHomeSectionOrder($productCategoryPosition);
        }

        $filtered = array_values(array_filter($decoded, fn ($section) => in_array($section, self::HOME_SECTIONS, true)));
        $default = $this->defaultHomeSectionOrder($productCategoryPosition);

        foreach ($default as $section) {
            if (! in_array($section, $filtered, true)) {
                $filtered[] = $section;
            }
        }

        return $filtered;
    }

    private function defaultHomeSectionOrder(string $productCategoryPosition = 'default'): array
    {
        $sections = [
            'home_slider',
            'marketing_banners',
            'crazy_deal',
            'best_selling_products',
            'featured_products',
            'new_popular',
            'service_features',
            'all_products',
        ];

        if ($productCategoryPosition === 'after_new_popular') {
            array_splice($sections, 5, 0, ['category_slider', 'category_products']);
        } else {
            array_splice($sections, 3, 0, ['category_slider', 'category_products']);
        }

        return $sections;
    }

    private function defaultSliderItemSettings(): array
    {
        return [
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
    }

    private function sanitizeSliderItemSettings($rawSettings): array
    {
        $defaults = $this->defaultSliderItemSettings();
        $sanitized = [];

        foreach ($defaults as $sliderKey => $breakpoints) {
            foreach ($breakpoints as $breakpoint => $defaultValue) {
                $value = data_get($rawSettings, $sliderKey . '.' . $breakpoint, $defaultValue);
                $sanitized[$sliderKey][$breakpoint] = max(1, min(12, (int) $value));
            }
        }

        return $sanitized;
    }

    private function hasThemeCustomizationColumn(string $column): bool
    {
        return Schema::hasTable('theme_customizations')
            && Schema::hasColumn('theme_customizations', $column);
    }
}
