<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'pro_barcode')) {
                $table->string('pro_barcode')->nullable()->unique()->after('product_code');
            }
        });

        Schema::table('product_variables', function (Blueprint $table) {
            if (! Schema::hasColumn('product_variables', 'barcode')) {
                $table->string('barcode')->nullable()->unique()->after('product_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_variables', function (Blueprint $table) {
            if (Schema::hasColumn('product_variables', 'barcode')) {
                $table->dropColumn('barcode');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'pro_barcode')) {
                $table->dropColumn('pro_barcode');
            }
        });
    }
};
