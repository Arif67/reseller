<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_ledgers', function (Blueprint $table) {
            if (! Schema::hasColumn('supplier_ledgers', 'reference_type')) {
                $table->string('reference_type')->nullable()->after('reference_no');
            }

            if (! Schema::hasColumn('supplier_ledgers', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('supplier_ledgers', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_ledgers', 'reference_id')) {
                $table->dropColumn('reference_id');
            }

            if (Schema::hasColumn('supplier_ledgers', 'reference_type')) {
                $table->dropColumn('reference_type');
            }
        });
    }
};
