<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('featured')->default(false)->after('status'); // or after 'name', adjust as needed
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('featured');
        });
    }
};
