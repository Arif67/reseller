<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses', 'financial_account_id')) {
                $table->unsignedBigInteger('financial_account_id')->nullable()->after('expense_cat_id');
            }
            if (! Schema::hasColumn('expenses', 'account_head_id')) {
                $table->unsignedBigInteger('account_head_id')->nullable()->after('financial_account_id');
            }
            if (! Schema::hasColumn('expenses', 'transaction_date')) {
                $table->date('transaction_date')->nullable()->after('amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            foreach (['transaction_date', 'account_head_id', 'financial_account_id'] as $column) {
                if (Schema::hasColumn('expenses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
