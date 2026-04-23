<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'shipping_revenue')) {
                $table->decimal('shipping_revenue', 14, 2)->default(0)->after('product_cost');
            }

            if (! Schema::hasColumn('orders', 'discount_total')) {
                $table->decimal('discount_total', 14, 2)->default(0)->after('shipping_revenue');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('orders', 'shipping_revenue')) {
                $dropColumns[] = 'shipping_revenue';
            }

            if (Schema::hasColumn('orders', 'discount_total')) {
                $dropColumns[] = 'discount_total';
            }

            if (! empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
