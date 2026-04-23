<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'qty' => 'int',
        'unit_cost' => 'float',
        'line_total' => 'float',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productVariable()
    {
        return $this->belongsTo(ProductVariable::class, 'product_variable_id');
    }
}
