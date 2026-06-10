<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // null = platform-er nijer product, value = oi vendor-er product
            $table->unsignedInteger('vendor_id')->nullable()->after('id')->index();
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });
    }
};
