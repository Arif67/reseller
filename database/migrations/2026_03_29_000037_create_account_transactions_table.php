<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('financial_account_id')->nullable();
            $table->unsignedBigInteger('account_head_id')->nullable();
            $table->date('transaction_date')->nullable();
            $table->string('direction');
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('description')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('note')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_transactions');
    }
};
