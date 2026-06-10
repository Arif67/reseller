<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->length('155');
            $table->string('shop_name')->length('155');
            $table->string('shop_slug')->length('155')->unique();
            $table->string('phone')->length('55');
            $table->string('email')->length('100')->nullable();
            $table->string('address')->nullable();
            $table->string('trade_license')->nullable();
            // commission platform kate (percent)
            $table->float('commission_rate')->default(0);
            // payout details
            $table->string('payout_method')->nullable(); // bank / bkash / nagad
            $table->string('payout_account')->nullable();
            $table->float('balance')->default(0);
            $table->string('image')->default('public/uploads/default/user.png');
            $table->string('password');
            $table->string('remember_token')->nullable();
            // pending / active / suspended
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendors');
    }
};
