<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abandoned_cart_leads', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->string('recovery_token')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('cart_payload')->nullable();
            $table->integer('cart_count')->default(0);
            $table->decimal('cart_total', 12, 2)->default(0);
            $table->string('landing_url')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('last_recovery_attempt_at')->nullable();
            $table->timestamp('sms_sent_at')->nullable();
            $table->timestamp('whatsapp_sent_at')->nullable();
            $table->timestamp('recovered_at')->nullable();
            $table->text('last_recovery_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abandoned_cart_leads');
    }
};
