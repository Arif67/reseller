<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntryLine extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'debit' => 'float',
        'credit' => 'float',
    ];

    public function accountHead()
    {
        return $this->belongsTo(AccountHead::class, 'account_head_id');
    }
}
