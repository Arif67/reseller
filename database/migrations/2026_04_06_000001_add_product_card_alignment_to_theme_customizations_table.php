<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            if (! Schema::hasColumn('theme_customizations', 'product_card_title_alignment')) {
                $table->string('product_card_title_alignment', 10)->default('left')->after('product_card_price_color');
            }

            if (! Schema::hasColumn('theme_customizations', 'product_card_price_alignment')) {
                $table->string('product_card_price_alignment', 10)->default('left')->after('product_card_title_alignment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            if (Schema::hasColumn('theme_customizations', 'product_card_price_alignment')) {
                $table->dropColumn('product_card_price_alignment');
            }

            if (Schema::hasColumn('theme_customizations', 'product_card_title_alignment')) {
                $table->dropColumn('product_card_title_alignment');
            }
        });
    }
};
