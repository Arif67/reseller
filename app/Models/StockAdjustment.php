<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'adjustment_date' => 'date',
        'qty' => 'int',
        'unit_cost' => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productVariable()
    {
        return $this->belongsTo(ProductVariable::class, 'product_variable_id');
    }
}
