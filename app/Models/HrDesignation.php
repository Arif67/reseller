<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrDesignation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(HrDepartment::class, 'department_id');
    }
}
