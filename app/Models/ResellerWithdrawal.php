<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerWithdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_id', 'amount', 'method', 'account', 'status', 'note',
    ];

    public function reseller()
    {
        return $this->belongsTo(Reseller::class, 'reseller_id');
    }
}
