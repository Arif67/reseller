<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('marketing_source')->nullable()->after('tracking_id');
            $table->string('utm_source')->nullable()->after('marketing_source');
            $table->string('utm_medium')->nullable()->after('utm_source');
            $table->string('utm_campaign')->nullable()->after('utm_medium');
            $table->string('utm_term')->nullable()->after('utm_campaign');
            $table->string('utm_content')->nullable()->after('utm_term');
            $table->text('landing_url')->nullable()->after('utm_content');
            $table->text('referrer_url')->nullable()->after('landing_url');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'marketing_source',
                'utm_source',
                'utm_medium',
                'utm_campaign',
                'utm_term',
                'utm_content',
                'landing_url',
                'referrer_url',
            ]);
        });
    }
};
