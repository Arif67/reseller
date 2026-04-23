<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->text('meta_description')->nullable();
            $table->string('meta_tag')->nullable();

        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['meta_description', 'meta_tag']);
        });
    }
};
