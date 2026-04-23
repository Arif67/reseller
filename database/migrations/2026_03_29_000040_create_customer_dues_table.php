<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_dues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('invoice_id')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('due_amount', 14, 2)->default(0);
            $table->text('note')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_dues');
    }
};
