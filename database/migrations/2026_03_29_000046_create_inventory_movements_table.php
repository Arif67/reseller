<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->date('movement_date')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_variable_id')->nullable();
            $table->string('movement_type');
            $table->string('direction', 10);
            $table->integer('qty')->default(0);
            $table->integer('stock_before')->default(0);
            $table->integer('stock_after')->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_no')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
