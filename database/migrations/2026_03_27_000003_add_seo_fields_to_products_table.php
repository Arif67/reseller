<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('stock');
            }

            if (! Schema::hasColumn('products', 'meta_tag')) {
                $table->text('meta_tag')->nullable()->after('meta_title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $drops = [];

            if (Schema::hasColumn('products', 'meta_title')) {
                $drops[] = 'meta_title';
            }

            if (Schema::hasColumn('products', 'meta_tag')) {
                $drops[] = 'meta_tag';
            }

            if (! empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
