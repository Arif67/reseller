<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('variation_pricing_mode')->nullable()->after('type');
        });

        DB::table('products')
            ->where('type', 0)
            ->update(['variation_pricing_mode' => 'different']);

        DB::table('products')
            ->where('type', '!=', 0)
            ->orWhereNull('type')
            ->update(['variation_pricing_mode' => 'same']);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('variation_pricing_mode');
        });
    }
};
