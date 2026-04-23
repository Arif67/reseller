<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->unsignedBigInteger('product_variable_id')->nullable()->after('product_id');
            $table->index(['product_id', 'product_variable_id'], 'order_details_product_variant_index');
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropIndex('order_details_product_variant_index');
            $table->dropColumn('product_variable_id');
        });
    }
};
