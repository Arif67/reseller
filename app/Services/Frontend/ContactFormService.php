<?php

namespace App\Services\Frontend;

use Illuminate\Support\Facades\Log;

class ContactFormService
{
    public function submit(array $data): void
    {
        Log::info('Frontend contact form submitted.', $data);
    }
}
