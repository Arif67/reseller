<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoStrip extends Model
{
    protected $fillable = [
        'title', 'image', 'link', 'countdown_end', 'bg_color', 'sort_order', 'status',
    ];

    protected $casts = [
        'countdown_end' => 'datetime',
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1)->orderBy('sort_order')->orderBy('id');
    }
}
