<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorAnalytic extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'visit_date' => 'date',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];
}
