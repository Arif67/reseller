<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'nullable|integer|exists:categories,id',
            'name' => 'required|string|max:255',
            'serial' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'status' => 'required|integer|in:0,1',
            'front_view' => 'nullable|integer|in:0,1',
            'banner_image' => 'nullable|integer|in:0,1',
            'featured' => 'nullable|integer|in:0,1',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif|max:2048',
            'icon' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif,svg|max:1024',
            'image_media_id' => 'nullable|integer|exists:media,id',
            'icon_media_id' => 'nullable|integer|exists:media,id',
        ];
    }
}
