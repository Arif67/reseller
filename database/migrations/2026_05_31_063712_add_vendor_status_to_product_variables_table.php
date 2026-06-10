<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_variables', function (Blueprint $table) {
            if (! Schema::hasColumn('product_variables', 'vendor_status')) {
                $table->boolean('vendor_status')->default(1)->after('stock');
            }
        });
    }

    public function down()
    {
        Schema::table('product_variables', function (Blueprint $table) {
            if (Schema::hasColumn('product_variables', 'vendor_status')) {
                $table->dropColumn('vendor_status');
            }
        });
    }
};
