<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierLedger extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'float',
        'paid_amount' => 'float',
        'due_amount' => 'float',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'reference_id');
    }
}
