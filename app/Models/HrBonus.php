<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrBonus extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'float',
        'bonus_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }
}
