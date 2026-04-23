<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hr_payrolls')) {
            return;
        }

        Schema::create('hr_payrolls', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('employee_id');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->date('payment_date')->nullable();
            $table->decimal('basic_salary', 14, 2)->default(0);
            $table->decimal('bonus', 14, 2)->default(0);
            $table->decimal('deduction', 14, 2)->default(0);
            $table->decimal('net_salary', 14, 2)->default(0);
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('employee_id');
            $table->index(['month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_payrolls');
    }
};
