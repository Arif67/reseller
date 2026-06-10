<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerPaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_id', 'type', 'account_number', 'account_name', 'bank_name',
    ];

    // dropdown / display label
    public function getLabelAttribute(): string
    {
        if ($this->type === 'bank') {
            return trim(($this->bank_name ? $this->bank_name . ' - ' : '') . $this->account_number
                . ($this->account_name ? ' (' . $this->account_name . ')' : ''));
        }

        return ucfirst($this->type) . ' - ' . $this->account_number;
    }
}
