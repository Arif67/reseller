<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_salary_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('house_rent', 12, 2)->default(0);
            $table->decimal('medical_allowance', 12, 2)->default(0);
            $table->decimal('transport_allowance', 12, 2)->default(0);
            $table->decimal('other_allowance', 12, 2)->default(0);
            $table->decimal('gross_salary', 12, 2)->default(0);
            $table->date('effective_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_salary_structures');
    }
};
