<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('invoice_id')->nullable()->index();
            $table->string('event_name')->index();
            $table->unsignedBigInteger('previous_status_id')->nullable();
            $table->string('previous_status_name')->nullable();
            $table->unsignedBigInteger('current_status_id')->nullable()->index();
            $table->string('current_status_name')->nullable();
            $table->string('source')->nullable()->index();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_tracking_events');
    }
};
