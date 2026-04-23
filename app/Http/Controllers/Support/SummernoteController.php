<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Services\AppService\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SummernoteController extends Controller
{
    public function __construct(
        private readonly FileUploadService $fileUploadService
    ) {
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|image|max:5120',
        ]);

        $path = $this->fileUploadService->processAndUploadImage(
            $request->file('file'),
            'uploads/summernote',
            ['prefix' => 'summernote']
        );
        $url = Str::startsWith($path, ['http://', 'https://']) ? $path : asset(ltrim($path, '/'));

        return response()->json([
            'url' => $url,
            'location' => $url,
        ]);
    }
}
