<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Category;
use App\Services\Admin\MediaService\MediaService;
use Brian2694\Toastr\Facades\Toastr as FacadesToastr;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function __construct(
        private MediaService $mediaService
    ) {
    }

    public function index(Request $request)
    {
        $query = Media::query()->latest();
        $selectedMediaIds = collect($request->query('selected_media_ids', []))
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value) => (int) $value)
            ->filter(fn ($value) => $value > 0)
            ->unique()
            ->values()
            ->all();

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('alt', 'like', '%' . $search . '%')
                    ->orWhere('path', 'like', '%' . $search . '%');
            });
        }

        $mediaItems = $query->paginate(24);
        $selectedMediaId = $request->query('selected_media_id');

        if ($request->boolean('picker')) {
            $selectedMediaItems = empty($selectedMediaIds)
                ? collect()
                : Media::query()->whereIn('id', $selectedMediaIds)->get();

            $pickerMediaItems = $selectedMediaItems->concat($mediaItems->getCollection())->unique('id')->values();

            return response()->json([
                'html' => view('backEnd.media.partials.picker-grid', [
                    'mediaItems' => $pickerMediaItems,
                    'selectedMediaId' => $selectedMediaId,
                    'selectedMediaIds' => $selectedMediaIds,
                ])->render(),
                'next_page' => $mediaItems->hasMorePages() ? $mediaItems->currentPage() + 1 : null,
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('backEnd.media.partials.grid', compact('mediaItems'))->render(),
                'next_page' => $mediaItems->hasMorePages() ? $mediaItems->currentPage() + 1 : null,
            ]);
        }

        return view('backEnd.media.index', compact('mediaItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|mimes:jpg,jpeg,png,webp,avif,svg',
        ]);

        foreach ($request->file('files', []) as $file) {
            $this->mediaService->createFromUpload($file);
        }

        FacadesToastr::success('Success', 'Media uploaded successfully');
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $media = Media::findOrFail($request->id);
        $usedInCategory = Category::query()
            ->where('image', $media->path)
            ->orWhere('icon', $media->path)
            ->exists();

        if ($media->products()->exists() || $media->productVariables()->exists() || $usedInCategory) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This media is still attached to a product, variation or category.',
                ], 422);
            }

            FacadesToastr::error('Failed', 'This media is still attached to a product, variation or category.');
            return redirect()->back();
        }

        $this->mediaService->deleteFile($media->path);
        $media->delete();

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Media deleted successfully.',
            ]);
        }

        FacadesToastr::success('Success', 'Media deleted successfully');
        return redirect()->back();
    }
}
