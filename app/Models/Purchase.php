<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'purchase_date' => 'date',
        'subtotal' => 'float',
        'discount_amount' => 'float',
        'transport_cost' => 'float',
        'other_cost' => 'float',
        'grand_total' => 'float',
        'paid_amount' => 'float',
        'due_amount' => 'float',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function accountHead()
    {
        return $this->belongsTo(AccountHead::class, 'account_head_id');
    }

    public function returns()
    {
        return $this->hasMany(PurchaseReturn::class, 'purchase_id');
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class, 'purchase_id');
    }
}
