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
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
    public function image()
    {
        return $this->hasOne(Productimage::class, 'product_id')
            ->select('productimages.id', 'productimages.image', 'productimages.product_id')
            ->oldestOfMany();
    }
    public function images()
    {
        return $this->hasMany(Productimage::class, 'product_id')
            ->select('productimages.id', 'productimages.image', 'productimages.product_id')
            ->orderBy('productimages.id');
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
        $orderedImages = $this->relationLoaded('images')
            ? $this->images->sortBy('id')->values()
            : $this->images()->get();

        return $media->pluck('path')->filter()->first()
            ?? $orderedImages->pluck('image')->filter()->first()
            ?? $this->image?->image;
    }

    public function getHoverMediaImageAttribute(): ?string
    {
        $primary = $this->primary_media_image;
        $media = $this->relationLoaded('media') ? $this->media : $this->media()->get();

        // Only use explicitly ordered product media for hover state.
        // Legacy productimages can contain stale rows from older edits and
        // should not trigger an alternate hover image on catalog cards.
        return $media->pluck('path')
            ->filter()
            ->unique()
            ->first(fn ($path) => $path !== $primary);
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

    public function activeReviews()
    {
        return $this->hasMany(Review::class, 'product_id')
            ->where('status', 'active')
            ->select('id', 'product_id', 'ratting');
    }
    public function category()
    {
        return $this->hasOne(Category::class,'id','category_id')->select('id','name','slug');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories')
            ->withTimestamps()
            ->select('categories.id', 'categories.name', 'categories.slug');
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
        return $this->hasOne(ProductVariable::class)->availableForReseller();
    }

    public function variables()
    {
        return $this->hasMany(ProductVariable::class)->availableForReseller();
    }

    public function allVariables()
    {
        return $this->hasMany(ProductVariable::class, 'product_id');
    }

    public function scopeForCategory($query, int|string|null $categoryId)
    {
        $categoryId = (int) $categoryId;

        if ($categoryId <= 0) {
            return $query;
        }

        return $query->whereHas('categories', function ($relationQuery) use ($categoryId) {
            $relationQuery->where('categories.id', $categoryId);
        });
    }
}
