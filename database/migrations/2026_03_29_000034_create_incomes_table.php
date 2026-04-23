<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('income_category_id')->nullable();
            $table->unsignedBigInteger('financial_account_id')->nullable();
            $table->unsignedBigInteger('account_head_id')->nullable();
            $table->string('name');
            $table->date('received_at')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->text('note')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
