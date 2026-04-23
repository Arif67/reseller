<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            $columns = [
                'show_home_slider',
                'show_marketing_banners',
                'show_crazy_deal',
                'show_featured_categories',
                'show_best_selling_products',
                'show_featured_products',
                'show_new_popular',
                'show_service_features',
            ];

            foreach ($columns as $column) {
                if (! Schema::hasColumn('theme_customizations', $column)) {
                    $table->boolean($column)->default(true);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            foreach ([
                'show_home_slider',
                'show_marketing_banners',
                'show_crazy_deal',
                'show_featured_categories',
                'show_best_selling_products',
                'show_featured_products',
                'show_new_popular',
                'show_service_features',
            ] as $column) {
                if (Schema::hasColumn('theme_customizations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
