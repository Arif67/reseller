<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Editable hero/banner content for the reseller landing page.
     */
    private array $columns = [
        'hero_badge',
        'hero_title',
        'hero_highlight',
        'hero_subtitle',
        'hero_primary_text',
        'hero_primary_link',
        'hero_secondary_text',
        'hero_secondary_link',
        'hero_bg_color',
        'hero_visual_title',
        'hero_visual_text',
        'hero_stat1_value',
        'hero_stat1_label',
        'hero_stat2_value',
        'hero_stat2_label',
        'hero_stat3_value',
        'hero_stat3_label',
        'hero_stat4_value',
        'hero_stat4_label',
    ];

    public function up(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            foreach ($this->columns as $column) {
                if (! Schema::hasColumn('theme_customizations', $column)) {
                    $table->string($column)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('theme_customizations', function (Blueprint $table) {
            foreach ($this->columns as $column) {
                if (Schema::hasColumn('theme_customizations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
