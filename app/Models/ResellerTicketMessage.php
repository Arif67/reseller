<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerTicketMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id', 'sender', 'message',
    ];

    public function ticket()
    {
        return $this->belongsTo(ResellerTicket::class, 'ticket_id');
    }
}
