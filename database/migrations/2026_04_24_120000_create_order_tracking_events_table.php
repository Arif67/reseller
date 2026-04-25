<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('order_tracking_events')) {
            return;
        }

        Schema::create('order_tracking_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_id')->nullable();
            $table->string('invoice_id')->nullable()->index();
            $table->string('event_name')->index();
            $table->unsignedBigInteger('previous_status_id')->nullable();
            $table->string('previous_status_name')->nullable();
            $table->unsignedBigInteger('current_status_id')->nullable()->index();
            $table->string('current_status_name')->nullable();
            $table->string('source')->nullable()->index();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_tracking_events');
    }
};
