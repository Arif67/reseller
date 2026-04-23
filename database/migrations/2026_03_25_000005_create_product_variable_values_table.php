<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variable_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_variable_id');
            $table->unsignedBigInteger('attribute_id');
            $table->unsignedBigInteger('value_id');
            $table->timestamps();

            $table->unique(['product_variable_id', 'attribute_id'], 'pvv_product_attribute_unique');
            $table->unique(['product_variable_id', 'value_id'], 'pvv_product_value_unique');
            $table->foreign('product_variable_id')->references('id')->on('product_variables')->onDelete('cascade');
            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
            $table->foreign('value_id')->references('id')->on('values')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variable_values');
    }
};
