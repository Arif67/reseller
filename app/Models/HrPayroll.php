<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrPayroll extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'payment_date' => 'date',
        'month' => 'integer',
        'year' => 'integer',
        'basic_salary' => 'float',
        'bonus' => 'float',
        'deduction' => 'float',
        'net_salary' => 'float',
    ];

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }
}
