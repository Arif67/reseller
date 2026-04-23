<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRefund extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'refund_date' => 'date',
        'amount' => 'float',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
