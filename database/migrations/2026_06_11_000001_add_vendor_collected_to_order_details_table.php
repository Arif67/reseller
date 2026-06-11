<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            if (! Schema::hasColumn('order_details', 'vendor_collected')) {
                $table->boolean('vendor_collected')->default(0)->after('qty');
            }
            if (! Schema::hasColumn('order_details', 'vendor_collected_at')) {
                $table->timestamp('vendor_collected_at')->nullable()->after('vendor_collected');
            }
        });
    }

    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            foreach (['vendor_collected', 'vendor_collected_at'] as $col) {
                if (Schema::hasColumn('order_details', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
