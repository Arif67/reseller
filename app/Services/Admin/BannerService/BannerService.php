<?php

namespace App\Services\Admin\BannerService;

use App\Models\Banner;
use App\Services\AppService\FileUploadService;
use Illuminate\Http\UploadedFile;

class BannerService
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {
    }

    public function uploadBannerImage(Banner $banner, UploadedFile $image): string
    {
        // Banners usually don't need fixed resize, but we can process to webp
        return $this->fileUploadService->processAndUploadImage(
            $image, 
            'uploads/banner', 
            ['prefix' => 'banner']
        );
    }

    public function deleteLocalIfNeeded(?string $path): void
    {
        $this->fileUploadService->deleteLocalIfNeeded($path);
    }
}
