<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('childcategory_id')->nullable()->after('category_id');

            // Optional: foreign key constraint
            // $table->foreign('childcategory_id')->references('id')->on('childcategories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('childcategory_id');
        });
    }

};
