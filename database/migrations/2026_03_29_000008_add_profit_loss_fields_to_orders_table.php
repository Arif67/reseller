<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('item_subtotal', 14, 2)->default(0)->after('referrer_url');
            $table->decimal('line_discount_total', 14, 2)->default(0)->after('item_subtotal');
            $table->decimal('item_revenue', 14, 2)->default(0)->after('line_discount_total');
            $table->decimal('product_cost', 14, 2)->default(0)->after('item_revenue');
            $table->decimal('courier_cost', 14, 2)->default(0)->after('product_cost');
            $table->decimal('packaging_cost', 14, 2)->default(0)->after('courier_cost');
            $table->decimal('payment_gateway_fee', 14, 2)->default(0)->after('packaging_cost');
            $table->decimal('misc_cost', 14, 2)->default(0)->after('payment_gateway_fee');
            $table->decimal('additional_cost', 14, 2)->default(0)->after('misc_cost');
            $table->decimal('total_expense', 14, 2)->default(0)->after('additional_cost');
            $table->decimal('gross_profit', 14, 2)->default(0)->after('total_expense');
            $table->decimal('net_profit', 14, 2)->default(0)->after('gross_profit');
            $table->string('profit_status')->nullable()->after('net_profit');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'item_subtotal',
                'line_discount_total',
                'item_revenue',
                'product_cost',
                'courier_cost',
                'packaging_cost',
                'payment_gateway_fee',
                'misc_cost',
                'additional_cost',
                'total_expense',
                'gross_profit',
                'net_profit',
                'profit_status',
            ]);
        });
    }
};
