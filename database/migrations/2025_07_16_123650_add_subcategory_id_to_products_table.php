<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('subcategory_id')->nullable()->after('category_id');

            // Optional: add foreign key if you have a subcategories table
            // $table->foreign('subcategory_id')->references('id')->on('subcategories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('subcategory_id');
        });
    }
};
