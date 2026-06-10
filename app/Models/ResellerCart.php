<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_id', 'product_id', 'product_variable_id', 'product_name', 'image',
        'size', 'color', 'selected_attributes', 'selected_value_ids',
        'qty', 'wholesale_price', 'sell_price',
    ];

    protected $casts = [
        'selected_attributes' => 'array',
        'selected_value_ids'  => 'array',
    ];

    // "Size: M, Color: Red" — dynamic attribute label
    public function getVariantLabelAttribute(): string
    {
        $attrs = $this->selected_attributes ?? [];
        if (! empty($attrs)) {
            return collect($attrs)
                ->map(fn ($a) => ($a['attribute'] ?? '') . ': ' . ($a['value'] ?? ''))
                ->implode(', ');
        }

        // legacy fallback
        return collect(['Size' => $this->size, 'Color' => $this->color])
            ->filter()
            ->map(fn ($v, $k) => "$k: $v")
            ->implode(', ');
    }

    // ei item-er reseller margin
    public function getMarginAttribute(): float
    {
        return ($this->sell_price - $this->wholesale_price) * $this->qty;
    }

    // ei item-er total sell (bikroy)
    public function getSubtotalAttribute(): float
    {
        return $this->sell_price * $this->qty;
    }

    public function productVariable()
    {
        return $this->belongsTo(ProductVariable::class, 'product_variable_id');
    }
}
