<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            if (! Schema::hasColumn('order_details', 'vendor_return_received')) {
                $table->boolean('vendor_return_received')->default(0)->after('admin_received_at');
            }
            if (! Schema::hasColumn('order_details', 'vendor_return_received_at')) {
                $table->timestamp('vendor_return_received_at')->nullable()->after('vendor_return_received');
            }
        });
    }

    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            foreach (['vendor_return_received', 'vendor_return_received_at'] as $col) {
                if (Schema::hasColumn('order_details', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
