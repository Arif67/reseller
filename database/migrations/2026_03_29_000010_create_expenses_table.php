<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('expenses')) {
            return;
        }

        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('expense_cat_id')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->index('expense_cat_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
