<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'link' => 'required|string',
            'status' => 'required',
            'category_id' => 'required',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif|max:2048|required_without:image_media_id',
            'image_media_id' => 'nullable|integer|exists:media,id|required_without:image',
        ];
    }
}
