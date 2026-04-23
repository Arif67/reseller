<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table): void {
            $table->string('card_bg_color')->default('#ffffff');
            $table->string('card_border_color')->default('#e2e8f0');
            $table->string('card_title_color')->default('#0f172a');
            $table->string('card_old_price_color')->default('#94a3b8');
            $table->string('card_image_bg_color')->default('#f8fafc');
            $table->unsignedSmallInteger('card_radius')->default(12);
            $table->unsignedSmallInteger('card_body_padding')->default(12);
            $table->decimal('card_shadow_opacity', 4, 2)->default(0.12);
        });
    }

    public function down(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table): void {
            $table->dropColumn([
                'card_bg_color',
                'card_border_color',
                'card_title_color',
                'card_old_price_color',
                'card_image_bg_color',
                'card_radius',
                'card_body_padding',
                'card_shadow_opacity',
            ]);
        });
    }
};
