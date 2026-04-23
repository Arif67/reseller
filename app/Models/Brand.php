<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;
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
            if (! Str::startsWith($image, ['/','storage/','public/'])) {
                return $vaultBase . '/' . ltrim($image, '/');
            }
        }

        if (Str::startsWith($image, ['storage/', 'uploads/'])) {
            return asset($image);
        }

        if (Str::startsWith($image, ['public/'])) {
            return Storage::url(Str::after($image, 'public/'));
        }

        if (Str::startsWith($image, ['/'])) {
            return asset(ltrim($image, '/'));
        }

        return Storage::url($image);
    }
}
