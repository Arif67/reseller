<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->string('android_app_link')->nullable()->after('copyright');
            $table->string('ios_app_link')->nullable()->after('android_app_link');
            $table->string('app_qr_code')->nullable()->after('ios_app_link');
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['android_app_link', 'ios_app_link', 'app_qr_code']);
        });
    }
};
