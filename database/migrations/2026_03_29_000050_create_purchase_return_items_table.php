<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_return_id');
            $table->unsignedBigInteger('purchase_item_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_variable_id')->nullable();
            $table->string('product_name');
            $table->string('variant_name')->nullable();
            $table->integer('qty')->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_return_items');
    }
};
