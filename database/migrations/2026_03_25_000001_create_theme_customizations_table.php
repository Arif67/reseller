<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theme_customizations', function (Blueprint $table) {
            $table->id();
            $table->string('top_bar_background_color', 20)->default('#FF5722');
            $table->string('search_bar_background_color', 20)->default('#000000');
            $table->string('navbar_background_color', 20)->default('#e6e1e1');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_customizations');
    }
};
