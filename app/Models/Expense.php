<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'float',
        'transaction_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategories::class, 'expense_cat_id');
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
