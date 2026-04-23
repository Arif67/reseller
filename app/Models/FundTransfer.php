<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundTransfer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'transfer_date' => 'date',
        'amount' => 'float',
    ];

    public function fromAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'from_financial_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'to_financial_account_id');
    }
}
