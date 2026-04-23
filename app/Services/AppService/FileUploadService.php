<?php

namespace App\Services\AppService;

use App\Models\GeneralSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use Intervention\Image\Facades\Image;

class FileUploadService
{
    public function upload(UploadedFile $file, string $folder, ?string $filename = null, bool $cacheInvalidate = true): string
    {
        $filename = $filename ?: $this->generateFilename($file);
        $folder = trim($folder, '/');
        
        if ($this->useVault()) {
            try {
                return $this->uploadToVault($file, $folder, $filename, $cacheInvalidate);
            } catch (\Throwable $exception) {
                if ($this->shouldFallbackToLocal()) {
                    Log::warning('Vault upload failed, falling back to local storage', [
                        'folder' => $folder,
                        'filename' => $filename,
                        'error' => $exception->getMessage(),
                    ]);
                } else {
                    Log::error('Vault upload failed and local fallback is disabled', [
                        'folder' => $folder,
                        'filename' => $filename,
                        'error' => $exception->getMessage(),
                    ]);
                    throw $exception;
                }
            }
        }

        return $this->storeLocally($file, $folder, $filename);
    }

    /**
     * Reusable method for processing and uploading images (resizing, webp conversion, etc.)
     */
    public function processAndUploadImage(UploadedFile $file, string $folder, array $options = []): string
    {
        $width = $options['width'] ?? null;
        $height = $options['height'] ?? null;
        $format = $options['format'] ?? 'webp';
        $quality = $options['quality'] ?? 90;
        $prefix = $options['prefix'] ?? 'img';

        $extension = strtolower((string) $file->getClientOriginalExtension());

        // Skip processing for SVGs and store the original file directly.
        if ($extension === 'svg') {
            return $this->upload($file, $folder);
        }

        $tempDir = storage_path('app/tmp/uploads');
        $this->ensureDirectoryExistsAndWritable($tempDir, 'temporary upload');

        $filename = time() . '-' . $prefix . '.' . $format;
        $tempPath = $tempDir . DIRECTORY_SEPARATOR . $filename;

        try {
            $img = Image::make($file->getRealPath());

            if ($width || $height) {
                $img->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            $img->encode($format, $quality);
            $img->save($tempPath);
        } catch (\Throwable $exception) {
            Log::warning('Image processing failed, falling back to original upload', [
                'folder' => $folder,
                'original_name' => $file->getClientOriginalName(),
                'extension' => $extension,
                'target_format' => $format,
                'error' => $exception->getMessage(),
            ]);

            return $this->upload($file, $folder);
        }

        $processedFile = new UploadedFile($tempPath, $filename, 'image/' . $format, null, true);
        try {
            $url = $this->upload($processedFile, $folder, $filename);
        } finally {
            File::delete($tempPath);
        }

        return $url;
    }

    protected function uploadToVault(UploadedFile $file, string $folder, string $filename, bool $cacheInvalidate): string
    {
        $endpoint = trim((string) $this->vaultEndpoint());

        if ($endpoint === '') {
            throw new \RuntimeException('Vault endpoint is not configured.');
        }

        $requestContext = [
            'endpoint' => $endpoint,
            'folder' => $folder,
            'filename' => $filename,
            'cache' => $cacheInvalidate ? 'invalidate' : 'keep',
            'original_name' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'has_access_key' => filled($this->vaultHeaders()['X-Access-Key-Id'] ?? null),
            'has_secret_key' => filled($this->vaultHeaders()['X-Secret-Key'] ?? null),
            'has_public_base_url' => filled($this->vaultPublicBaseUrl()),
        ];

        try {
            $response = Http::acceptJson()
                ->timeout(60)
                ->withHeaders($this->vaultHeaders())
                ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post($endpoint, [
                    'folder' => $folder,
                    'filename' => $filename,
                    'cache' => $cacheInvalidate ? 'invalidate' : 'keep',
                ]);

            if (! $response->successful()) {
                $this->logVaultFailure('Vault upload returned non-2xx response', $requestContext, $response);
                throw new \RuntimeException('Vault upload failed with HTTP ' . $response->status() . ': ' . trim((string) $response->body()));
            }

            $payload = $response->json();

            $url = null;
            if (is_array($payload)) {
                $url = $payload['url']
                    ?? $payload['public_url']
                    ?? $payload['location']
                    ?? $payload['path']
                    ?? $payload['key']
                    ?? null;
            }

            if ($url) {
                if (Str::startsWith($url, ['http://', 'https://'])) {
                    return $url;
                }

                $base = $this->vaultPublicBaseUrl();
                if ($base) {
                    return rtrim($base, '/') . '/' . ltrim($url, '/');
                }
            }

            $base = $this->vaultPublicBaseUrl();
            if ($base) {
                $relative = $folder ? "{$folder}/{$filename}" : $filename;
                return rtrim($base, '/') . '/' . ltrim($relative, '/');
            }

            return $folder ? "{$folder}/{$filename}" : $filename;
        } catch (\Throwable $exception) {
            $this->logVaultFailure('Vault upload exception', $requestContext, null, $exception);
            throw new \RuntimeException(
                'Vault upload failed: ' . $exception->getMessage(),
                0,
                $exception
            );
        }
    }

    protected function logVaultFailure(string $message, array $requestContext, ?Response $response = null, ?\Throwable $exception = null): void
    {
        $payload = $requestContext;

        if ($response) {
            $payload['response_status'] = $response->status();
            $payload['response_headers'] = $response->headers();
            $payload['response_body'] = Str::limit((string) $response->body(), 2000);
        }

        if ($exception) {
            $payload['exception'] = $exception->getMessage();
        }

        Log::error($message, $payload);
    }

    protected function storeLocally(UploadedFile $file, string $folder, string $filename): string
    {
        $folder = trim($folder, '/');
        $relative = ($folder ? "{$folder}/" : '') . $filename;
        $disk = Storage::disk('public');
        $diskRoot = $disk->path('');

        $this->ensureDirectoryExistsAndWritable($diskRoot, 'public storage');

        if ($folder !== '') {
            $targetDir = $disk->path($folder);
            $this->ensureDirectoryExistsAndWritable($targetDir, 'upload');
        }

        $storedPath = $disk->putFileAs($folder, $file, $filename);
        if ($storedPath === false) {
            $targetPath = $folder !== '' ? $disk->path($folder) : $diskRoot;
            throw new \RuntimeException(
                "Unable to write uploaded file to [{$targetPath}]. Check storage permissions."
            );
        }

        return '/storage/' . ltrim($relative, '/');
    }

    protected function useVault(): bool
    {
        $setting = $this->currentSetting();
        if ($setting) {
            return (bool) ($setting->vault_upload_enabled ?? false);
        }

        return (bool) config('vault.enabled');
    }

    protected function shouldFallbackToLocal(): bool
    {
        return (bool) config('vault.fallback_to_local', false);
    }

    protected function vaultHeaders(): array
    {
        $setting = $this->currentSetting();
        return [
            'X-Secret-Key' => $setting?->vault_secret_key ?? config('vault.secret_key'),
            'X-Access-Key-Id' => $setting?->vault_access_key_id ?? config('vault.access_key_id'),
        ];
    }

    protected function vaultEndpoint(): string
    {
        $setting = $this->currentSetting();

        return $setting?->vault_endpoint ?: config('vault.endpoint');
    }

    protected function vaultPublicBaseUrl(): string
    {
        $setting = $this->currentSetting();

        return $setting?->vault_public_base_url ?: (string) config('vault.public_base_url', '');
    }

    protected function currentSetting(): ?GeneralSetting
    {
        return Cache::remember('upload_settings_v1', 300, function () {
            return GeneralSetting::where('status', 1)->latest('id')->first();
        });
    }

    protected function generateFilename(UploadedFile $file): string
    {
        return Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . now()->timestamp . '.' . $file->getClientOriginalExtension();
    }

    public function deleteLocalIfNeeded(?string $path): void
    {
        $path = trim((string) $path);

        if ($path === '' || Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        $normalizedPath = ltrim($path, '/');

        if (Str::startsWith($normalizedPath, 'storage/uploads/')) {
            Storage::disk('public')->delete(Str::after($normalizedPath, 'storage/'));
            return;
        }

        if (Str::startsWith($normalizedPath, 'uploads/')) {
            File::delete(public_path($normalizedPath));
        }
    }

    protected function ensureDirectoryExistsAndWritable(string $path, string $label): void
    {
        if (! File::isDirectory($path)) {
            if (! File::makeDirectory($path, 0755, true) && ! File::isDirectory($path)) {
                throw new \RuntimeException(
                    "Unable to create {$label} directory [{$path}]. Check server write permissions."
                );
            }
        }

        if (! is_writable($path)) {
            throw new \RuntimeException(
                "Directory [{$path}] is not writable for {$label}. Check server permissions."
            );
        }
    }
}
