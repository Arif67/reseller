<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('vendor_payout_methods')) {
            Schema::create('vendor_payout_methods', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_id');
                $table->string('method');           // bKash / Nagad / Rocket / Bank
                $table->string('account');          // number / account
                $table->string('holder_name')->nullable(); // account holder (optional)
                $table->boolean('is_default')->default(0);
                $table->timestamps();

                $table->index('vendor_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('vendor_payout_methods');
    }
};
