<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'float',
        'received_at' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(IncomeCategory::class, 'income_category_id');
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function accountHead()
    {
        return $this->belongsTo(AccountHead::class, 'account_head_id');
    }
}
