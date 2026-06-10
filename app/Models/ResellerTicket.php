<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_id', 'order_id', 'subject', 'status', 'last_reply_at',
    ];

    protected $casts = [
        'last_reply_at' => 'datetime',
    ];

    public function reseller()
    {
        return $this->belongsTo(Reseller::class, 'reseller_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function messages()
    {
        return $this->hasMany(ResellerTicketMessage::class, 'ticket_id');
    }

    public function statusBadge(): array
    {
        return match ($this->status) {
            'open'     => ['Open', 'warning'],
            'answered' => ['Answered', 'info'],
            'closed'   => ['Closed', 'secondary'],
            default    => ['Unknown', 'dark'],
        };
    }
}
