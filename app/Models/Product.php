<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'selected_attribute_ids' => 'array',
        'variation_pricing_mode' => 'string',
    ];
    public function getRouteKeyName() {
        return 'slug';
    }
    public function image()
    {
        return $this->hasOne(Productimage::class, 'product_id')->select('id','image','product_id');
    }
    public function images()
    {
        return $this->hasMany(Productimage::class, 'product_id')->select('id','image','product_id');
    }

    public function media()
    {
        return $this->belongsToMany(Media::class, 'product_media')
            ->withPivot('position')
            ->orderBy('product_media.position');
    }

    public function getPrimaryMediaImageAttribute(): ?string
    {
        $media = $this->relationLoaded('media') ? $this->media : $this->media()->get();

        return $media->first()?->path ?? $this->image?->image;
    }

    public function getHoverMediaImageAttribute(): ?string
    {
        $primary = $this->primary_media_image;
        $media = $this->relationLoaded('media') ? $this->media : $this->media()->get();
        $secondaryMedia = $media->pluck('path')->filter(fn ($path) => $path && $path !== $primary)->first();

        if ($secondaryMedia) {
            return $secondaryMedia;
        }

        $images = $this->relationLoaded('images') ? $this->images : $this->images()->get();

        return $images->pluck('image')->filter(fn ($path) => $path && $path !== $primary)->first();
    }

    public function getDisplayOldPriceAttribute()
    {
        return $this->resolveDisplayPrice('old_price');
    }

    public function getDisplayNewPriceAttribute()
    {
        return $this->resolveDisplayPrice('new_price');
    }

    public function usesSharedVariationPricing(): bool
    {
        return (int) $this->type === 0 && $this->variation_pricing_mode === 'same';
    }

    protected function resolveDisplayPrice(string $column)
    {
        if ((int) $this->type !== 0) {
            return $this->getAttribute($column);
        }

        if ($this->usesSharedVariationPricing()) {
            return $this->getAttribute($column);
        }

        $variant = $this->relationLoaded('variable') ? $this->variable : $this->variable()->first();

        return $variant?->{$column} ?? $this->getAttribute($column);
    }

    public function hasCompletedPurchaseBy(?int $customerId): bool
    {
        if (! $customerId) {
            return false;
        }

        return OrderDetails::query()
            ->where('product_id', $this->id)
            ->whereHas('order', function ($query) use ($customerId) {
                $query->where('customer_id', $customerId)->where('order_status', 6);
            })
            ->exists();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id')->select('id');
    }
    public function category()
    {
        return $this->hasOne(Category::class,'id','category_id')->select('id','name','slug');
    }
    public function subcategory()
    {
        return $this->hasOne(Subcategory::class,'id','subcategory_id')->select('id','subcategoryName','slug');
    }
    public function childcategory()
    {
        return $this->hasOne(Childcategory::class,'id','childcategory_id')->select('id','childcategoryName','slug');
    }

    public function brand()
    {
        return $this->hasOne(Brand::class,'id','brand_id')->select('id','name','slug');
    }

    public function variable()
    {
        return $this->hasOne('App\Models\ProductVariable')->where('stock','>',0);
    }

    public function variables()
    {
        return $this->hasMany('App\Models\ProductVariable')->where('stock','>',0);
    }

    public function allVariables()
    {
        return $this->hasMany(ProductVariable::class, 'product_id');
    }
}
