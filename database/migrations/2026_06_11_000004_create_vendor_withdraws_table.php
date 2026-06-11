<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('vendor_withdraws')) {
            Schema::create('vendor_withdraws', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_id');
                $table->decimal('amount', 14, 2);
                $table->string('method')->nullable();      // bkash / nagad / bank
                $table->string('account')->nullable();     // number / account
                $table->string('status')->default('pending'); // pending | paid | rejected
                $table->string('admin_note')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();

                $table->index(['vendor_id', 'status']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('vendor_withdraws');
    }
};
