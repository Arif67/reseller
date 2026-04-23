<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variables', function (Blueprint $table) {
            $table->text('images')->nullable()->after('image');
        });

        DB::table('product_variables')
            ->whereNotNull('image')
            ->orderBy('id')
            ->get(['id', 'image'])
            ->each(function ($variable) {
                DB::table('product_variables')
                    ->where('id', $variable->id)
                    ->update([
                        'images' => json_encode([$variable->image]),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('product_variables', function (Blueprint $table) {
            $table->dropColumn('images');
        });
    }
};
