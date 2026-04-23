<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketing_tool_configs', function (Blueprint $table) {
            if (! Schema::hasColumn('marketing_tool_configs', 'whatsapp_api_url')) {
                $table->string('whatsapp_api_url')->nullable()->after('whatsapp_number');
            }

            if (! Schema::hasColumn('marketing_tool_configs', 'whatsapp_api_key')) {
                $table->string('whatsapp_api_key')->nullable()->after('whatsapp_api_url');
            }

            if (! Schema::hasColumn('marketing_tool_configs', 'whatsapp_sender')) {
                $table->string('whatsapp_sender')->nullable()->after('whatsapp_api_key');
            }
        });
    }

    public function down(): void
    {
        Schema::table('marketing_tool_configs', function (Blueprint $table) {
            $drops = [];

            if (Schema::hasColumn('marketing_tool_configs', 'whatsapp_sender')) {
                $drops[] = 'whatsapp_sender';
            }

            if (Schema::hasColumn('marketing_tool_configs', 'whatsapp_api_key')) {
                $drops[] = 'whatsapp_api_key';
            }

            if (Schema::hasColumn('marketing_tool_configs', 'whatsapp_api_url')) {
                $drops[] = 'whatsapp_api_url';
            }

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }
};
