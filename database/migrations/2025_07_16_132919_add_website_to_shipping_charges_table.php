<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('shipping_charges', function (Blueprint $table) {
            $table->string('website')->nullable()->after('id'); // or any logical column
        });
    }

    public function down()
    {
        Schema::table('shipping_charges', function (Blueprint $table) {
            $table->dropColumn('website');
        });
    }

};
