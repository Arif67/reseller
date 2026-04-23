<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            if (! Schema::hasColumn('theme_customizations', 'product_details_layout')) {
                $table->unsignedTinyInteger('product_details_layout')->default(1)->after('view_details_font_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            if (Schema::hasColumn('theme_customizations', 'product_details_layout')) {
                $table->dropColumn('product_details_layout');
            }
        });
    }
};
