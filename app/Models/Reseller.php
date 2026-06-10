<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Reseller extends Authenticatable
{
    use HasFactory;

    protected $guard = 'reseller';

    protected $fillable = [
        'name', 'business_name', 'phone', 'email', 'address',
        'default_margin_type', 'default_margin_value', 'balance',
        'payout_method', 'payout_account', 'image', 'password', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // delivered (order_status = 7) order theke total earned margin
    public function deliveredMargin(): float
    {
        return (float) Order::where('reseller_id', $this->id)
            ->where('order_status', 7)
            ->sum('reseller_margin');
    }

    // pending (delivery hoy nai, cancel/refund/return chara) margin
    public function pendingMargin(): float
    {
        return (float) Order::where('reseller_id', $this->id)
            ->whereNotIn('order_status', [7, 9, 10, 11, 12]) // delivered/cancel/refund/return/failed bad
            ->sum('reseller_margin');
    }

    // withdraw kora ba request kora amount (rejected bad)
    public function withdrawnAmount(): float
    {
        return (float) ResellerWithdrawal::where('reseller_id', $this->id)
            ->where('status', '!=', 'rejected')
            ->sum('amount');
    }

    // ekhon withdraw kora jabe emon balance
    public function availableBalance(): float
    {
        return $this->deliveredMargin() - $this->withdrawnAmount();
    }
}
