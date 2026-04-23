<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fraud_checker_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('BD Courier Fraud Checker');
            $table->string('url')->nullable();
            $table->string('api_key')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        $legacyConfig = DB::table('courierapis')->where('type', 'fraud_checker')->first();

        DB::table('fraud_checker_configs')->insert([
            'name' => 'BD Courier Fraud Checker',
            'url' => $legacyConfig->url ?? null,
            'api_key' => $legacyConfig->api_key ?? null,
            'status' => $legacyConfig->status ?? 0,
            'created_at' => $legacyConfig->created_at ?? now(),
            'updated_at' => $legacyConfig->updated_at ?? now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_checker_configs');
    }
};
