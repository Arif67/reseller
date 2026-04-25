<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $guarded = [];

    public function getImageUrlAttribute(): string
    {
        $image = (string) ($this->image ?? '');

        if ($image === '') {
            return '';
        }

        if (Str::startsWith($image, ['http://', 'https://'])) {
            return $image;
        }

        $setting = Cache::remember('upload_settings_v1', 300, function () {
            return GeneralSetting::where('status', 1)->latest('id')->first();
        });

        $vaultEnabled = (bool) ($setting?->vault_upload_enabled ?? false);
        $vaultBase = trim((string) ($setting?->vault_public_base_url ?? config('vault.public_base_url', '')), '/');

        if ($vaultEnabled && $vaultBase) {
            if (! Str::startsWith($image, ['/','storage/','public/','uploads/'])) {
                return $vaultBase . '/' . ltrim($image, '/');
            }
        }

        return asset($image);
    }

    public function getIconUrlAttribute(): string
    {
        $icon = (string) ($this->icon ?? '');

        if ($icon === '') {
            return '';
        }

        if (Str::startsWith($icon, ['http://', 'https://'])) {
            return $icon;
        }

        $setting = Cache::remember('upload_settings_v1', 300, function () {
            return GeneralSetting::where('status', 1)->latest('id')->first();
        });

        $vaultEnabled = (bool) ($setting?->vault_upload_enabled ?? false);
        $vaultBase = trim((string) ($setting?->vault_public_base_url ?? config('vault.public_base_url', '')), '/');

        if ($vaultEnabled && $vaultBase) {
            if (! Str::startsWith($icon, ['/','storage/','public/','uploads/'])) {
                return $vaultBase . '/' . ltrim($icon, '/');
            }
        }

        return asset($icon);
    }

    public function getRouteKeyName() {
        return 'slug';
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_id')->where('status', 1)->with('products');
    }
    public function subcategories_home()
    {
        return $this->hasMany(Subcategory::class, 'category_id')->where('status', 1)->with('products_home');
    }


    public function menusubcategories() {
        return $this->hasMany(Subcategory::class, 'category_id')->select('id','slug','subcategoryName','category_id')->where('status', 1);
    }

    public function menuchildcategories()
    {
        return $this->hasMany(Childcategory::class, 'subcategory_id')->select('id','slug','subcategory_id','childcategoryName')->where('status',1);
    }


    public function homeproducts()
    {
        return $this->belongsToMany(Product::class, 'product_categories')
            ->withTimestamps();
    }
    public function menuproducts()
    {
        return $this->belongsToMany(Product::class, 'product_categories')
            ->withTimestamps()
            ->limit(8);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories')
            ->withTimestamps()
            ->select('products.id', 'products.name', 'products.slug', 'products.category_id', 'products.new_price', 'products.old_price')
            ->orderBy('products.id', 'DESC');
    }




}
