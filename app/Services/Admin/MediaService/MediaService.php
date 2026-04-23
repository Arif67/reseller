<?php

namespace App\Services\Admin\MediaService;

use App\Models\Media;
use App\Services\AppService\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class MediaService
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {
    }

    public function createFromUpload(UploadedFile $file, array $options = []): Media
    {
        $originalName = $file->getClientOriginalName();
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $folder = trim((string) ($options['folder'] ?? 'uploads/media'), '/');
        $filename = $this->buildFilename($baseName, $extension);
        $uploadOptions = $options;
        unset($uploadOptions['folder']);

        $path = empty($uploadOptions)
            ? $this->fileUploadService->upload($file, $folder, $filename)
            : $this->fileUploadService->processAndUploadImage($file, $folder, $uploadOptions);

        return Media::create([
            'name' => $baseName,
            'alt' => $baseName,
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    public function deleteFile(?string $path): void
    {
        $this->fileUploadService->deleteLocalIfNeeded($path);
    }

    private function buildFilename(string $baseName, string $extension): string
    {
        $slug = Str::slug($baseName);

        if ($slug === '') {
            $slug = 'media';
        }

        $filename = $slug . '-' . now()->timestamp . '-' . Str::lower(Str::random(6));

        if ($extension !== '') {
            return $filename . '.' . $extension;
        }

        return $filename;
    }
}
