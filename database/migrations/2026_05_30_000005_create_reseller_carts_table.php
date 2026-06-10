<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reseller_carts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reseller_id')->index();
            $table->unsignedInteger('product_id');
            $table->string('product_name')->nullable();
            $table->string('image')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->integer('qty')->default(1);
            $table->float('wholesale_price')->default(0); // reseller-er cost
            $table->float('sell_price')->default(0);       // reseller-er bikroy mullo
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reseller_carts');
    }
};
