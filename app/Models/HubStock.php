<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HubStock extends Model
{
    protected $fillable = [
        'product_id',
        'product_variable_id',
        'qty',
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
