<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_tool_configs', function (Blueprint $table) {
            $table->id();
            $table->string('ga4_measurement_id')->nullable();
            $table->string('google_ads_id')->nullable();
            $table->string('google_ads_conversion_label')->nullable();
            $table->string('clarity_project_id')->nullable();
            $table->string('merchant_store_name')->nullable();
            $table->tinyInteger('merchant_feed_enabled')->default(0);
            $table->tinyInteger('utm_tracking_enabled')->default(1);
            $table->tinyInteger('abandoned_cart_enabled')->default(0);
            $table->integer('abandoned_cart_recovery_minutes')->default(60);
            $table->tinyInteger('abandoned_cart_sms_enabled')->default(0);
            $table->tinyInteger('abandoned_cart_whatsapp_enabled')->default(0);
            $table->string('whatsapp_number')->nullable();
            $table->string('whatsapp_api_url')->nullable();
            $table->string('whatsapp_api_key')->nullable();
            $table->string('whatsapp_sender')->nullable();
            $table->text('abandoned_cart_sms_template')->nullable();
            $table->text('abandoned_cart_whatsapp_template')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_tool_configs');
    }
};
