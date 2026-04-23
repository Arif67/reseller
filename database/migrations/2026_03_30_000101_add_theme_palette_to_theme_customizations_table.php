<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            $table->string('primary_color', 20)->default('#0f172a')->after('view_details_font_color');
            $table->string('accent_color', 20)->default('#dc2626')->after('primary_color');
            $table->string('surface_color', 20)->default('#ffffff')->after('accent_color');
            $table->string('section_bg_color', 20)->default('#f8fafc')->after('surface_color');
            $table->string('text_color', 20)->default('#0f172a')->after('section_bg_color');
            $table->string('muted_text_color', 20)->default('#64748b')->after('text_color');
        });
    }

    public function down(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            $table->dropColumn([
                'primary_color',
                'accent_color',
                'surface_color',
                'section_bg_color',
                'text_color',
                'muted_text_color',
            ]);
        });
    }
};
