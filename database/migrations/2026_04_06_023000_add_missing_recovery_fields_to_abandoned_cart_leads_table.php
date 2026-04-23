<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('abandoned_cart_leads', function (Blueprint $table) {
            if (! Schema::hasColumn('abandoned_cart_leads', 'recovery_token')) {
                $table->string('recovery_token')->nullable()->unique()->after('session_id');
            }

            if (! Schema::hasColumn('abandoned_cart_leads', 'last_recovery_attempt_at')) {
                $table->timestamp('last_recovery_attempt_at')->nullable()->after('last_activity_at');
            }

            if (! Schema::hasColumn('abandoned_cart_leads', 'sms_sent_at')) {
                $table->timestamp('sms_sent_at')->nullable()->after('last_recovery_attempt_at');
            }

            if (! Schema::hasColumn('abandoned_cart_leads', 'whatsapp_sent_at')) {
                $table->timestamp('whatsapp_sent_at')->nullable()->after('sms_sent_at');
            }

            if (! Schema::hasColumn('abandoned_cart_leads', 'last_recovery_error')) {
                $table->text('last_recovery_error')->nullable()->after('recovered_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('abandoned_cart_leads', function (Blueprint $table) {
            $drops = [];

            if (Schema::hasColumn('abandoned_cart_leads', 'last_recovery_error')) {
                $drops[] = 'last_recovery_error';
            }

            if (Schema::hasColumn('abandoned_cart_leads', 'whatsapp_sent_at')) {
                $drops[] = 'whatsapp_sent_at';
            }

            if (Schema::hasColumn('abandoned_cart_leads', 'sms_sent_at')) {
                $drops[] = 'sms_sent_at';
            }

            if (Schema::hasColumn('abandoned_cart_leads', 'last_recovery_attempt_at')) {
                $drops[] = 'last_recovery_attempt_at';
            }

            if (Schema::hasColumn('abandoned_cart_leads', 'recovery_token')) {
                $drops[] = 'recovery_token';
            }

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }
};
