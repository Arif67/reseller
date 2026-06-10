<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reseller_withdrawals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reseller_id')->index();
            $table->float('amount');
            $table->string('method')->nullable();   // bkash / nagad / bank
            $table->string('account')->nullable();
            $table->string('status')->default('pending'); // pending / approved / paid / rejected
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reseller_withdrawals');
    }
};
