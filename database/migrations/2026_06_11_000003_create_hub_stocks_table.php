<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('hub_stocks')) {
            Schema::create('hub_stocks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('product_variable_id')->nullable();
                $table->integer('qty')->default(0);
                $table->timestamps();

                $table->unique(['product_id', 'product_variable_id'], 'hub_stock_product_variant_unique');
                $table->index('product_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('hub_stocks');
    }
};
