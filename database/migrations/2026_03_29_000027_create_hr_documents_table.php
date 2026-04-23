<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('title');
            $table->string('document_type')->nullable();
            $table->string('document_number')->nullable();
            $table->string('file_path')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_documents');
    }
};
