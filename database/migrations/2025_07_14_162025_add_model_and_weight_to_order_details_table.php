<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->string('product_model')->nullable()->after('product_name'); // Change 'product_name' to the correct previous column
            $table->string('product_weight')->nullable()->after('product_model');
        });
    }

    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn(['product_model', 'product_weight']);
        });
    }
};
