<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            if (! Schema::hasColumn('theme_customizations', 'product_category_layout')) {
                $table->unsignedTinyInteger('product_category_layout')->default(1)->after('slider_layout');
            }
        });
    }

    public function down(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            if (Schema::hasColumn('theme_customizations', 'product_category_layout')) {
                $table->dropColumn('product_category_layout');
            }
        });
    }
};
