<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hr_leaves')) {
            return;
        }

        Schema::create('hr_leaves', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('employee_id');
            $table->string('leave_type')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days')->default(1);
            $table->string('status')->default('pending');
            $table->text('reason')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index('employee_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_leaves');
    }
};
