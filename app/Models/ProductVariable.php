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
        'vendor_status' => 'boolean',
    ];

    public function scopeAvailableForReseller($query)
    {
        return $query->where('stock', '>', 0)->where('vendor_status', 1);
    }


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

    public function getVariantLabelAttribute(): string
    {
        $selectedValues = $this->relationLoaded('selectedValues')
            ? $this->selectedValues
            : $this->selectedValues()->with('attribute')->get();

        if ($selectedValues->isNotEmpty()) {
            return $selectedValues
                ->map(fn ($value) => ($value->attribute?->title ?? 'Option') . ': ' . $value->title)
                ->implode(', ');
        }

        return collect([
            'Size' => $this->size,
            'Color' => $this->color,
            'Model' => $this->model,
            'Weight' => $this->weight,
        ])->filter()->map(fn ($value, $label) => "$label: $value")->implode(', ');
    }

    public function getIsAvailableForResellerAttribute(): bool
    {
        return (bool) $this->vendor_status && (int) $this->stock > 0;
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
