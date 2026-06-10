<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reseller_payment_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reseller_id')->index();
            $table->string('type');            // bkash / nagad / bank
            $table->string('account_number');  // number / account no
            $table->string('account_name')->nullable(); // holder name (mainly bank)
            $table->string('bank_name')->nullable();     // bank holder bank name
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reseller_payment_methods');
    }
};
