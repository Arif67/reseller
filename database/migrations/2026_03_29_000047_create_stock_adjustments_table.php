<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->date('adjustment_date')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_variable_id')->nullable();
            $table->string('adjustment_type');
            $table->integer('qty')->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->string('reason')->nullable();
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
