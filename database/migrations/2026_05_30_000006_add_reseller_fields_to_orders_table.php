<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // ei order kon reseller place koreche (null = normal/customer order)
            $table->unsignedInteger('reseller_id')->nullable()->after('customer_id')->index();
            // reseller-er total margin ei order theke
            $table->float('reseller_margin')->default(0)->after('reseller_id');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['reseller_id', 'reseller_margin']);
        });
    }
};
