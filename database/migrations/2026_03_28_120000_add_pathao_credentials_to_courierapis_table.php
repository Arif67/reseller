<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courierapis', function (Blueprint $table) {
            $table->string('client_id')->nullable()->after('secret_key');
            $table->string('client_secret')->nullable()->after('client_id');
            $table->string('username')->nullable()->after('client_secret');
            $table->string('password')->nullable()->after('username');
            $table->string('grant_type')->nullable()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('courierapis', function (Blueprint $table) {
            $table->dropColumn(['client_id', 'client_secret', 'username', 'password', 'grant_type']);
        });
    }
};
