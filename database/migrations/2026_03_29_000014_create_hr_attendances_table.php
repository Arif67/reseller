<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hr_attendances')) {
            return;
        }

        Schema::create('hr_attendances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('employee_id');
            $table->date('attendance_date');
            $table->string('status')->default('present');
            $table->string('check_in')->nullable();
            $table->string('check_out')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('employee_id');
            $table->unique(['employee_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_attendances');
    }
};
