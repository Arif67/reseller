<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            $table->string('product_card_button_background_color', 20)->default('#DC2626')->after('toggle_font_color');
            $table->string('product_card_button_hover_background_color', 20)->default('#B91C1C')->after('product_card_button_background_color');
            $table->string('product_card_button_font_color', 20)->default('#FFFFFF')->after('product_card_button_hover_background_color');
            $table->string('product_card_badge_background_color', 20)->default('#DC2626')->after('product_card_button_font_color');
            $table->string('product_card_badge_font_color', 20)->default('#FFFFFF')->after('product_card_badge_background_color');
            $table->string('product_card_price_color', 20)->default('#111827')->after('product_card_badge_font_color');
        });
    }

    public function down(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            $table->dropColumn([
                'product_card_button_background_color',
                'product_card_button_hover_background_color',
                'product_card_button_font_color',
                'product_card_badge_background_color',
                'product_card_badge_font_color',
                'product_card_price_color',
            ]);
        });
    }
};
