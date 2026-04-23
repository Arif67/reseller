<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDue extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'float',
        'paid_amount' => 'float',
        'due_amount' => 'float',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
