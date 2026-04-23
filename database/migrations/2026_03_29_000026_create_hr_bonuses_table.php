<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_bonuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('title');
            $table->string('bonus_type')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('bonus_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_bonuses');
    }
};
