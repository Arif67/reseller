<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'movement_date' => 'date',
        'qty' => 'int',
        'stock_before' => 'int',
        'stock_after' => 'int',
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
