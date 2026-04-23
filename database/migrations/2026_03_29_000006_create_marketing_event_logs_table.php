<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_event_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('event_name');
            $table->string('event_id')->nullable();
            $table->string('status')->nullable();
            $table->string('order_id')->nullable();
            $table->decimal('value', 12, 2)->nullable();
            $table->string('currency')->nullable();
            $table->string('source_url')->nullable();
            $table->longText('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_event_logs');
    }
};
