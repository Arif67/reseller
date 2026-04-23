<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'low_stock_alert')) {
                $table->integer('low_stock_alert')->default(5)->after('stock');
            }
        });

        Schema::table('product_variables', function (Blueprint $table) {
            if (! Schema::hasColumn('product_variables', 'low_stock_alert')) {
                $table->integer('low_stock_alert')->default(5)->after('stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'low_stock_alert')) {
                $table->dropColumn('low_stock_alert');
            }
        });

        Schema::table('product_variables', function (Blueprint $table) {
            if (Schema::hasColumn('product_variables', 'low_stock_alert')) {
                $table->dropColumn('low_stock_alert');
            }
        });
    }
};
