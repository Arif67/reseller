<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'nullable|integer|exists:brands,id',
            'name' => 'required|string|max:255',
            'status' => 'required|integer|in:0,1',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif|max:2048|required_without:image_media_id',
            'image_media_id' => 'nullable|integer|exists:media,id|required_without:image',
        ];
    }
}
