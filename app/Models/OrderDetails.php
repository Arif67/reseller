<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variable_id',
        'selected_attributes',
    ];

    protected $casts = [
        'selected_attributes' => 'array',
    ];

    public function image()
    {
        return $this->belongsTo(Productimage::class, 'product_id', 'product_id')
            ->select('productimages.id', 'productimages.product_id', 'productimages.image');
    }

    public function shipping()
    {
        return $this->belongsTo(Shipping::class, 'order_id', 'order_id')
            ->select('shippings.id', 'shippings.order_id', 'shippings.name', 'shippings.phone', 'shippings.address');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id')->select('orders.id', 'orders.invoice_id');
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
