<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('invoice_id')->nullable();
            $table->date('refund_date')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('return_type')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('reason')->nullable();
            $table->string('status')->default('processed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_refunds');
    }
};
