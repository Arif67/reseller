<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrSalaryStructure extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'effective_date' => 'date',
        'basic_salary' => 'float',
        'house_rent' => 'float',
        'medical_allowance' => 'float',
        'transport_allowance' => 'float',
        'other_allowance' => 'float',
        'gross_salary' => 'float',
    ];

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }
}
