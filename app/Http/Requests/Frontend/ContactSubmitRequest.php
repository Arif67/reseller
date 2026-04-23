<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class ContactSubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required'],
            'email' => ['required'],
            'subject' => ['required'],
            'message' => ['required'],
            'phone' => ['required'],
        ];
    }
}
