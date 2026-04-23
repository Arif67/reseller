<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecom_pixels', function (Blueprint $table) {
            if (! Schema::hasColumn('ecom_pixels', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            if (! Schema::hasColumn('ecom_pixels', 'provider')) {
                $table->string('provider')->default('facebook')->after('name');
            }
            if (! Schema::hasColumn('ecom_pixels', 'access_token')) {
                $table->text('access_token')->nullable()->after('code');
            }
            if (! Schema::hasColumn('ecom_pixels', 'test_event_code')) {
                $table->string('test_event_code')->nullable()->after('access_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ecom_pixels', function (Blueprint $table) {
            $dropColumns = [];
            foreach (['name', 'provider', 'access_token', 'test_event_code'] as $column) {
                if (Schema::hasColumn('ecom_pixels', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
