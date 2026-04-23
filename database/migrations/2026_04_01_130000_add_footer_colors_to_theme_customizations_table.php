<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table): void {
            $table->string('footer_top_background_color', 20)->default('#020617');
            $table->string('footer_text_color', 20)->default('#CBD5E1');
            $table->string('footer_heading_color', 20)->default('#FFFFFF');
            $table->string('footer_link_color', 20)->default('#F8FAFC');
            $table->string('footer_link_hover_color', 20)->default('#F59E0B');
            $table->string('footer_accent_color', 20)->default('#F59E0B');
            $table->string('footer_bottom_background_color', 20)->default('#000000');
            $table->string('footer_bottom_text_color', 20)->default('#E2E8F0');
        });
    }

    public function down(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table): void {
            $table->dropColumn([
                'footer_top_background_color',
                'footer_text_color',
                'footer_heading_color',
                'footer_link_color',
                'footer_link_hover_color',
                'footer_accent_color',
                'footer_bottom_background_color',
                'footer_bottom_text_color',
            ]);
        });
    }
};
