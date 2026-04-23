<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('account_type');
            $table->string('account_no')->nullable();
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->decimal('current_balance', 14, 2)->default(0);
            $table->text('note')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_accounts');
    }
};
