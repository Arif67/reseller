<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fund_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_financial_account_id');
            $table->unsignedBigInteger('to_financial_account_id');
            $table->unsignedBigInteger('account_head_id')->nullable();
            $table->date('transfer_date')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('reference_no')->nullable();
            $table->text('note')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fund_transfers');
    }
};
