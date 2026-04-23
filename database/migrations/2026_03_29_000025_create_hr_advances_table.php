<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_advances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('advance_type')->default('advance');
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('installment_amount', 12, 2)->default(0);
            $table->date('issue_date')->nullable();
            $table->date('deduction_start_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_advances');
    }
};
