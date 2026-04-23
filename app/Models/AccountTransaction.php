<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'float',
    ];

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function accountHead()
    {
        return $this->belongsTo(AccountHead::class, 'account_head_id');
    }
}
