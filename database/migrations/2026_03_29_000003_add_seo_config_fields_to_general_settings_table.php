<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('general_settings', 'google_site_verification')) {
                $table->string('google_site_verification')->nullable()->after('meta_tag');
            }

            if (! Schema::hasColumn('general_settings', 'default_robots')) {
                $table->string('default_robots')->nullable()->after('google_site_verification');
            }
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $drops = [];

            if (Schema::hasColumn('general_settings', 'google_site_verification')) {
                $drops[] = 'google_site_verification';
            }

            if (Schema::hasColumn('general_settings', 'default_robots')) {
                $drops[] = 'default_robots';
            }

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }
};
