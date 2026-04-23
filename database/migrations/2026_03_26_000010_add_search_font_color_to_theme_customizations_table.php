<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            $table->string('search_font_color', 20)->default('#FFFFFF')->after('search_bar_background_color');
        });
    }

    public function down(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            $table->dropColumn('search_font_color');
        });
    }
};