<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_variables', function (Blueprint $table) {
            $table->string('model')->nullable();
            $table->string('weight')->nullable();
        });
    }

    public function down()
    {
        Schema::table('product_variables', function (Blueprint $table) {
            $table->dropColumn(['model', 'weight']);
        });
    }
};
