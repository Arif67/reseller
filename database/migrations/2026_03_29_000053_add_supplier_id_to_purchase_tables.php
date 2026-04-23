<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (! Schema::hasColumn('purchases', 'supplier_id')) {
                $table->unsignedBigInteger('supplier_id')->nullable()->after('purchase_no');
            }
        });

        Schema::table('supplier_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('supplier_payments', 'supplier_id')) {
                $table->unsignedBigInteger('supplier_id')->nullable()->after('purchase_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'supplier_id')) {
                $table->dropColumn('supplier_id');
            }
        });

        Schema::table('supplier_payments', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_payments', 'supplier_id')) {
                $table->dropColumn('supplier_id');
            }
        });
    }
};
