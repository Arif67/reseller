<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            if (! Schema::hasColumn('order_details', 'admin_received')) {
                $table->boolean('admin_received')->default(0)->after('vendor_collected_at');
            }
            if (! Schema::hasColumn('order_details', 'admin_received_at')) {
                $table->timestamp('admin_received_at')->nullable()->after('admin_received');
            }
        });
    }

    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            foreach (['admin_received', 'admin_received_at'] as $col) {
                if (Schema::hasColumn('order_details', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
