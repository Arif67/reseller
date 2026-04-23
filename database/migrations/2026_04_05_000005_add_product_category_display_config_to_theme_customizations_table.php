<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            if (! Schema::hasColumn('theme_customizations', 'product_category_display_mode')) {
                $table->string('product_category_display_mode', 32)
                    ->default('category_products')
                    ->after('product_category_position');
            }

            if (! Schema::hasColumn('theme_customizations', 'product_category_product_limit')) {
                $table->unsignedTinyInteger('product_category_product_limit')
                    ->default(8)
                    ->after('product_category_display_mode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            if (Schema::hasColumn('theme_customizations', 'product_category_product_limit')) {
                $table->dropColumn('product_category_product_limit');
            }

            if (Schema::hasColumn('theme_customizations', 'product_category_display_mode')) {
                $table->dropColumn('product_category_display_mode');
            }
        });
    }
};
