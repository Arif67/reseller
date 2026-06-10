<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resellers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->length('155');
            $table->string('business_name')->length('155')->nullable();
            $table->string('phone')->length('55');
            $table->string('email')->length('100')->nullable();
            $table->string('address')->nullable();
            // default margin reseller-er bikroy er upor
            $table->string('default_margin_type')->default('percent'); // percent / flat
            $table->float('default_margin_value')->default(0);
            $table->float('balance')->default(0);
            // withdraw details
            $table->string('payout_method')->nullable();
            $table->string('payout_account')->nullable();
            $table->string('image')->default('public/uploads/default/user.png');
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->string('status')->default('pending'); // pending/active/suspended
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('resellers');
    }
};
