<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reseller_carts', function (Blueprint $table) {
            $table->unsignedInteger('product_variable_id')->nullable()->after('product_id');
            $table->json('selected_attributes')->nullable()->after('color');   // [{attribute, value}]
            $table->json('selected_value_ids')->nullable()->after('selected_attributes');
        });
    }

    public function down()
    {
        Schema::table('reseller_carts', function (Blueprint $table) {
            $table->dropColumn(['product_variable_id', 'selected_attributes', 'selected_value_ids']);
        });
    }
};
