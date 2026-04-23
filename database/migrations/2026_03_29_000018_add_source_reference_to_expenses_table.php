<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses', 'source_type')) {
                $table->string('source_type')->nullable()->after('note');
            }

            if (! Schema::hasColumn('expenses', 'source_id')) {
                $table->unsignedInteger('source_id')->nullable()->after('source_type');
                $table->index(['source_type', 'source_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('expenses', 'source_id')) {
                $dropColumns[] = 'source_id';
            }

            if (Schema::hasColumn('expenses', 'source_type')) {
                $dropColumns[] = 'source_type';
            }

            if (! empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
