<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $attributeMap = [
            'Color' => ['table' => 'colors', 'column' => 'colorName', 'status_column' => 'status'],
            'Size' => ['table' => 'sizes', 'column' => 'sizeName', 'status_column' => 'status'],
            'Weight' => ['table' => 'weights', 'column' => 'title', 'status_column' => 'status'],
            'Model' => ['table' => 'admin_models', 'column' => 'title', 'status_column' => 'status'],
        ];

        foreach ($attributeMap as $attributeTitle => $config) {
            $attributeId = DB::table('attributes')->where('title', $attributeTitle)->value('id');

            if (! $attributeId) {
                $attributeId = DB::table('attributes')->insertGetId([
                    'title' => $attributeTitle,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if (! Schema::hasTable($config['table']) || ! Schema::hasColumn($config['table'], $config['column'])) {
                continue;
            }

            $query = DB::table($config['table'])
                ->select($config['column'])
                ->whereNotNull($config['column'])
                ->where($config['column'], '!=', '');

            if (! empty($config['status_column']) && Schema::hasColumn($config['table'], $config['status_column'])) {
                $query->where($config['status_column'], 1);
            }

            $titles = $query->distinct()->pluck($config['column']);

            foreach ($titles as $title) {
                $exists = DB::table('values')
                    ->where('attribute_id', $attributeId)
                    ->where('title', $title)
                    ->exists();

                if (! $exists) {
                    DB::table('values')->insert([
                        'attribute_id' => $attributeId,
                        'title' => $title,
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // keep seeded attributes and values in place
    }
};
