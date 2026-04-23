<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            if (! Schema::hasColumn('theme_customizations', 'show_category_slider')) {
                $table->boolean('show_category_slider')->default(true)->after('show_crazy_deal');
            }

            if (! Schema::hasColumn('theme_customizations', 'show_category_products')) {
                $table->boolean('show_category_products')->default(true)->after('show_category_slider');
            }
        });
    }

    public function down(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            if (Schema::hasColumn('theme_customizations', 'show_category_products')) {
                $table->dropColumn('show_category_products');
            }

            if (Schema::hasColumn('theme_customizations', 'show_category_slider')) {
                $table->dropColumn('show_category_slider');
            }
        });
    }
};
