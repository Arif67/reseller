<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['order_status', 'id'], 'orders_status_id_index');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->index(['product_id', 'order_id'], 'order_details_product_order_index');
            $table->index(['order_id', 'product_id'], 'order_details_order_product_index');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_id_index');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropIndex('order_details_product_order_index');
            $table->dropIndex('order_details_order_product_index');
        });
    }
};
