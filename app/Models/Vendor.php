<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Vendor extends Authenticatable
{
    use HasFactory;

    protected $guard = 'vendor';

    protected $fillable = [
        'name', 'shop_name', 'shop_slug', 'phone', 'email', 'address',
        'trade_license', 'commission_rate', 'payout_method', 'payout_account',
        'balance', 'image', 'password', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'vendor_id');
    }

    public function payoutMethods()
    {
        return $this->hasMany(VendorPayoutMethod::class, 'vendor_id')->latest('is_default');
    }
}
