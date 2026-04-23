<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariable extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'images' => 'array',
    ];


    public function getGalleryImagesAttribute(): array
    {
        $images = $this->images;

        if (is_array($images) && count($images) > 0) {
            return array_values(array_filter($images));
        }

        return $this->image ? [$this->image] : [];
    }

    public function getPrimaryImageAttribute(): ?string
    {
        return $this->primary_media_image ?? $this->gallery_images[0] ?? $this->image;
    }

    public function getPrimaryMediaImageAttribute(): ?string
    {
        $media = $this->relationLoaded('media') ? $this->media : $this->media()->get();

        return $media->first()?->path ?? $this->gallery_images[0] ?? $this->image;
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function media()
    {
        return $this->belongsToMany(Media::class, 'product_variable_media')
            ->withPivot('position')
            ->orderBy('product_variable_media.position');
    }

    public function selectedValues()
    {
        return $this->belongsToMany(Value::class, 'product_variable_values')
            ->withPivot('attribute_id')
            ->withTimestamps();
    }
}
