<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('general_settings', function (Blueprint $table) {
        $table->string('pickup_title')->nullable()->after('id');
        $table->text('pickup_description')->nullable()->after('pickup_title');
    });
}

public function down()
{
    Schema::table('general_settings', function (Blueprint $table) {
        $table->dropColumn(['pickup_title', 'pickup_description']);
    });
}

};
