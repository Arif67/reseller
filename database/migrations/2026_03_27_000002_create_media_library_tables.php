<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('alt')->nullable();
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();
        });

        Schema::create('product_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id');
            $table->unsignedBigInteger('media_id');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->unique(['product_id', 'media_id']);
        });

        Schema::create('product_variable_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_variable_id');
            $table->unsignedBigInteger('media_id');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->unique(['product_variable_id', 'media_id']);
        });

        $this->importProductImages();
        $this->importVariableImages();
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variable_media');
        Schema::dropIfExists('product_media');
        Schema::dropIfExists('media');
    }

    private function importProductImages(): void
    {
        $productImages = DB::table('productimages')->orderBy('id')->get();

        foreach ($productImages as $index => $image) {
            $mediaId = DB::table('media')->insertGetId([
                'name' => pathinfo($image->image, PATHINFO_FILENAME),
                'alt' => pathinfo($image->image, PATHINFO_FILENAME),
                'path' => $image->image,
                'mime_type' => null,
                'size' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('product_media')->insert([
                'product_id' => $image->product_id,
                'media_id' => $mediaId,
                'position' => $index,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function importVariableImages(): void
    {
        $hasImagesColumn = Schema::hasColumn('product_variables', 'images');
        $variables = DB::table('product_variables')->orderBy('id')->get();

        foreach ($variables as $variable) {
            $paths = [];

            if ($hasImagesColumn && ! empty($variable->images)) {
                $decoded = json_decode($variable->images, true);
                if (is_array($decoded)) {
                    $paths = array_values(array_filter($decoded));
                }
            }

            if (empty($paths) && ! empty($variable->image)) {
                $paths = [$variable->image];
            }

            foreach ($paths as $position => $path) {
                $mediaId = DB::table('media')->insertGetId([
                    'name' => pathinfo($path, PATHINFO_FILENAME),
                    'alt' => pathinfo($path, PATHINFO_FILENAME),
                    'path' => $path,
                    'mime_type' => null,
                    'size' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('product_variable_media')->insert([
                    'product_variable_id' => $variable->id,
                    'media_id' => $mediaId,
                    'position' => $position,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
};
