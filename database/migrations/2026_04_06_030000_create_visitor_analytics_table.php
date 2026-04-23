<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 120);
            $table->string('ip_address', 64)->nullable();
            $table->string('country', 120)->nullable();
            $table->string('region', 120)->nullable();
            $table->string('district', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('page_path')->nullable();
            $table->text('page_url')->nullable();
            $table->text('referrer_url')->nullable();
            $table->string('device_type', 20)->nullable();
            $table->unsignedInteger('view_count')->default(1);
            $table->date('visit_date');
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->index(['visit_date', 'district']);
            $table->index(['visit_date', 'country']);
            $table->index(['session_id', 'visit_date']);
            $table->unique(['session_id', 'page_path', 'visit_date'], 'visitor_analytics_unique_session_page_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_analytics');
    }
};
