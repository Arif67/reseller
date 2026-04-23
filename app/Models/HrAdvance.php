<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrAdvance extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'float',
        'installment_amount' => 'float',
        'issue_date' => 'date',
        'deduction_start_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }
}
