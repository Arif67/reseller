<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'float',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }
}
