<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbandonedCartLead extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'recovered_at' => 'datetime',
    ];
}
