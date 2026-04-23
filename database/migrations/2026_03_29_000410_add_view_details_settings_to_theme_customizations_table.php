<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            $table->boolean('show_view_details')->default(true)->after('product_card_price_color');
            $table->string('view_details_bg_color', 20)->default('#f8fafc')->after('show_view_details');
            $table->string('view_details_font_color', 20)->default('#0f172a')->after('view_details_bg_color');
        });
    }

    public function down(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            $table->dropColumn([
                'show_view_details',
                'view_details_bg_color',
                'view_details_font_color',
            ]);
        });
    }
};
