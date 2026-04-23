<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_employees', function (Blueprint $table) {
            if (! Schema::hasColumn('hr_employees', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('department_id');
            }
            if (! Schema::hasColumn('hr_employees', 'shift_id')) {
                $table->unsignedBigInteger('shift_id')->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('hr_employees', 'designation_id')) {
                $table->unsignedBigInteger('designation_id')->nullable()->after('shift_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hr_employees', function (Blueprint $table) {
            foreach (['designation_id', 'shift_id', 'user_id'] as $column) {
                if (Schema::hasColumn('hr_employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
