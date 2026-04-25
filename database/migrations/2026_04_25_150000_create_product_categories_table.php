<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('category_id');
            $table->timestamps();

            $table->unique(['product_id', 'category_id']);
            $table->index('category_id');
        });

        $now = now();

        $rows = DB::table('products')
            ->select('id as product_id', 'category_id')
            ->whereNotNull('category_id')
            ->where('category_id', '>', 0)
            ->get()
            ->map(fn ($row) => [
                'product_id' => $row->product_id,
                'category_id' => $row->category_id,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->all();

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('product_categories')->insertOrIgnore($chunk);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
