<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrPerformanceNote extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'note_date' => 'date',
        'rating' => 'float',
    ];

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }
}
