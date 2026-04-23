<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrEmployee extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'joining_date' => 'date',
        'salary' => 'float',
        'annual_leave_days' => 'integer',
    ];

    public function department()
    {
        return $this->belongsTo(HrDepartment::class, 'department_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shift()
    {
        return $this->belongsTo(HrShift::class, 'shift_id');
    }

    public function designationMaster()
    {
        return $this->belongsTo(HrDesignation::class, 'designation_id');
    }

    public function attendances()
    {
        return $this->hasMany(HrAttendance::class, 'employee_id');
    }

    public function leaves()
    {
        return $this->hasMany(HrLeave::class, 'employee_id');
    }

    public function payrolls()
    {
        return $this->hasMany(HrPayroll::class, 'employee_id');
    }

    public function documents()
    {
        return $this->hasMany(HrDocument::class, 'employee_id');
    }

    public function performanceNotes()
    {
        return $this->hasMany(HrPerformanceNote::class, 'employee_id');
    }

    public function separations()
    {
        return $this->hasMany(HrSeparation::class, 'employee_id');
    }
}
