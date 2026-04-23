<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_media')->withPivot('position');
    }

    public function productVariables()
    {
        return $this->belongsToMany(ProductVariable::class, 'product_variable_media')->withPivot('position');
    }
}
