<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            $table->string('track_order_background_color', 20)->default('#677279')->after('header_action_background_color');
            $table->string('track_order_font_color', 20)->default('#FFFFFF')->after('track_order_background_color');
            $table->string('login_background_color', 20)->default('#677279')->after('track_order_font_color');
            $table->string('login_font_color', 20)->default('#FFFFFF')->after('login_background_color');
            $table->string('cart_background_color', 20)->default('#677279')->after('login_font_color');
            $table->string('cart_font_color', 20)->default('#FFFFFF')->after('cart_background_color');
            $table->string('toggle_font_color', 20)->default('#FFFFFF')->after('cart_font_color');
        });
    }

    public function down(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            $table->dropColumn([
                'track_order_background_color',
                'track_order_font_color',
                'login_background_color',
                'login_font_color',
                'cart_background_color',
                'cart_font_color',
                'toggle_font_color',
            ]);
        });
    }
};
