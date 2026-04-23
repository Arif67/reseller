<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up()
    {
        Schema::create('values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_id')->nullable(); // Optional relationship
            $table->string('title');
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('values');
    }
};
