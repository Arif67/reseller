<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorWithdraw extends Model
{
    protected $fillable = [
        'vendor_id', 'amount', 'method', 'account',
        'status', 'admin_note', 'processed_at',
    ];

    protected $casts = [
        'amount'       => 'float',
        'processed_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
