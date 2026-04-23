<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->string('supplier_name');
            $table->date('payment_date')->nullable();
            $table->unsignedBigInteger('financial_account_id')->nullable();
            $table->unsignedBigInteger('account_head_id')->nullable();
            $table->string('reference_no')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
