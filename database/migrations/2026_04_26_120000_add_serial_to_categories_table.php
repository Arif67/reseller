<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'serial')) {
                $table->unsignedInteger('serial')->default(0)->after('name');
            }
        });

        if (Schema::hasColumn('categories', 'serial')) {
            DB::table('categories')->orderBy('id')->get(['id'])->each(function ($category): void {
                DB::table('categories')
                    ->where('id', $category->id)
                    ->update(['serial' => $category->id]);
            });
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'serial')) {
                $table->dropColumn('serial');
            }
        });
    }
};
