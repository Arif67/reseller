<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('general_settings', 'vault_upload_enabled')) {
                $table->tinyInteger('vault_upload_enabled')->default(0)->after('default_robots');
            }
            if (! Schema::hasColumn('general_settings', 'vault_endpoint')) {
                $table->string('vault_endpoint')->nullable()->after('vault_upload_enabled');
            }
            if (! Schema::hasColumn('general_settings', 'vault_access_key_id')) {
                $table->string('vault_access_key_id')->nullable()->after('vault_endpoint');
            }
            if (! Schema::hasColumn('general_settings', 'vault_secret_key')) {
                $table->string('vault_secret_key')->nullable()->after('vault_access_key_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'vault_secret_key')) {
                $table->dropColumn('vault_secret_key');
            }
            if (Schema::hasColumn('general_settings', 'vault_access_key_id')) {
                $table->dropColumn('vault_access_key_id');
            }
            if (Schema::hasColumn('general_settings', 'vault_endpoint')) {
                $table->dropColumn('vault_endpoint');
            }
            if (Schema::hasColumn('general_settings', 'vault_upload_enabled')) {
                $table->dropColumn('vault_upload_enabled');
            }
        });
    }
};
