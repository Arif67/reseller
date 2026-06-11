<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorPayoutMethod extends Model
{
    protected $fillable = [
        'vendor_id', 'method', 'account', 'holder_name', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function getLabelAttribute(): string
    {
        return $this->method . ' — ' . $this->account;
    }
}
