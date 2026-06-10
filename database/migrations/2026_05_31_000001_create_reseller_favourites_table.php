<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reseller_favourites', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reseller_id')->index();
            $table->unsignedInteger('product_id');
            $table->timestamps();

            // ekই reseller ekই product duvabe favourite korte parbe na
            $table->unique(['reseller_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reseller_favourites');
    }
};
