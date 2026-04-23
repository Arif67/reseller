<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'opening_balance' => 'float',
        'current_balance' => 'float',
    ];
}
