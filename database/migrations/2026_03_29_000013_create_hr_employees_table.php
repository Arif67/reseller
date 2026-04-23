<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hr_employees')) {
            return;
        }

        Schema::create('hr_employees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('department_id')->nullable();
            $table->string('employee_id')->nullable()->unique();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('designation')->nullable();
            $table->date('joining_date')->nullable();
            $table->decimal('salary', 14, 2)->default(0);
            $table->string('employment_type')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->index('department_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_employees');
    }
};
