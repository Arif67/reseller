<?php

namespace App\Services\Admin\CategoryService;

use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use App\Services\Admin\AdminActivityLogService;
use App\Services\Admin\MediaService\MediaService;
use App\Http\Controllers\Frontend\LandingController;
use App\Traits\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CategoryService
{
    use Response;

    public function __construct(
        private readonly MediaService $mediaService,
        private readonly AdminActivityLogService $activityLogService,
    ) {
    }

    public function getListData(array $query): array
    {
        try {
            if (request()->ajax()) {
                $categories = Category::query()
                    ->select('id', 'name', 'serial', 'front_view', 'image', 'icon', 'status')
                    ->orderBy('serial')
                    ->orderBy('id');

                $tableData = DataTables::eloquent($categories)
                    ->addIndexColumn()
                    ->addColumn('checkbox', function (Category $category): string {
                        return '<input type="checkbox" class="form-check-input category-checkbox" value="' . $category->id . '">';
                    })
                    ->editColumn('name', function (Category $category): string {
                        $name = e($category->name);

                        if ((int) $category->front_view === 1) {
                            return '<span class="btn btn-dark">' . $name . '</span>';
                        }

                        return '<span>' . $name . '</span>';
                    })
                    ->editColumn('serial', fn (Category $category): string => (string) ((int) ($category->serial ?? 0)))
                    ->addColumn('image_preview', function (Category $category): string {
                        if ($category->image_url === '') {
                            return '<span class="text-muted">No image</span>';
                        }

                        return '<img src="' . e($category->image_url) . '" class="backend-image" alt="Category image">';
                    })
                    ->addColumn('icon_preview', function (Category $category): string {
                        if ($category->icon_url === '') {
                            return '<span class="text-muted">No image</span>';
                        }

                        return '<img src="' . e($category->icon_url) . '" class="backend-image" alt="Category icon">';
                    })
                    ->addColumn('status_badge', function (Category $category): string {
                        if ((int) $category->status === 1) {
                            return '<span class="badge bg-soft-success text-success">Active</span>';
                        }

                        return '<span class="badge bg-soft-danger text-danger">Inactive</span>';
                    })
                    ->addColumn('action', function (Category $category): string {
                        $toggleRoute = $category->status == 1
                            ? route('categories.inactive')
                            : route('categories.active');
                        $toggleButtonClass = $category->status == 1
                            ? 'btn btn-xs btn-secondary waves-effect waves-light change-confirm'
                            : 'btn btn-xs btn-success waves-effect waves-light change-confirm';
                        $toggleIcon = $category->status == 1 ? 'fe-thumbs-down' : 'fe-thumbs-up';
                        $editUrl = route('categories.edit', $category->id);

                        return '<div class="button-list">'
                            . '<form method="post" action="' . $toggleRoute . '" class="d-inline">'
                            . csrf_field()
                            . '<input type="hidden" value="' . $category->id . '" name="hidden_id">'
                            . '<button type="button" class="' . $toggleButtonClass . '"><i class="fe ' . $toggleIcon . '"></i></button>'
                            . '</form>'
                            . '<a href="' . $editUrl . '" class="btn btn-xs btn-primary waves-effect waves-light"><i class="fe-edit-1"></i></a>'
                            . '</div>';
                    })
                    ->rawColumns(['checkbox', 'name', 'image_preview', 'icon_preview', 'status_badge', 'action'])
                    ->make(true)
                    ->getData(true);

                return $this->response($tableData)->success();
            }

            return $this->response($query)->success();
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function getEditData(int|string $id): array
    {
        try {
            $category = Category::query()->findOrFail($id);
            $selectedImageMediaId = old(
                'image_media_id',
                $category->image ? Media::query()->where('path', $category->image)->value('id') : null
            );
            $selectedIconMediaId = old(
                'icon_media_id',
                $category->icon ? Media::query()->where('path', $category->icon)->value('id') : null
            );
            return $this->response([
                'edit_data' => $category,
                'selectedImageMediaId' => $selectedImageMediaId,
                'selectedIconMediaId' => $selectedIconMediaId,
            ])->success();
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function storeCategory(array $payload): array
    {
        try {
            $category = DB::transaction(fn () => $this->saveCategory($payload));
            $this->activityLogService->log(
                'category',
                'created',
                (int) $category->id,
                (string) $category->name,
                null,
                $this->snapshotCategory($category)
            );
            $this->flushCategoryCaches();
            return $this->response(['category' => $category])->success('Category inserted successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function updateCategory(array $payload): array
    {
        try {
            $categoryId = (int) ($payload['id'] ?? 0);
            $before = null;
            $category = DB::transaction(function () use ($payload, $categoryId, &$before) {
                $category = Category::query()->findOrFail($categoryId);
                $before = $this->snapshotCategory($category);
                return $this->saveCategory($payload, $category);
            });

            $this->activityLogService->log(
                'category',
                'updated',
                (int) $category->id,
                (string) $category->name,
                $before,
                $this->snapshotCategory($category)
            );

            $this->flushCategoryCaches();
            return $this->response(['category' => $category])->success('Category updated successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function changeCategoryStatus(array $payload): array
    {
        try {
            $category = Category::query()->findOrFail((int) ($payload['hidden_id'] ?? 0));
            $status = (int) ($payload['status'] ?? 0);
            $before = $this->snapshotCategory($category);

            $category->status = $status;
            $category->save();

            $this->activityLogService->log(
                'category',
                $status === 1 ? 'activated' : 'inactivated',
                (int) $category->id,
                (string) $category->name,
                $before,
                $this->snapshotCategory($category)
            );

            $this->flushCategoryCaches();
            return $this->response(['category' => $category])->success(
                $status === 1 ? 'Data active successfully' : 'Data inactive successfully'
            );
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function deleteCategory(array $payload): array
    {
        try {
            $ids = collect($payload['hidden_ids'] ?? [])
                ->filter(fn ($value) => filled($value))
                ->map(fn ($value) => (int) $value)
                ->filter(fn ($value) => $value > 0)
                ->values()
                ->all();

            if (empty($ids)) {
                $hiddenId = (int) ($payload['hidden_id'] ?? 0);
                $ids = $hiddenId > 0 ? [$hiddenId] : [];
            }

            if (empty($ids)) {
                return $this->response()->error('Please select category first');
            }

            $linkedCategoryIds = Product::query()
                ->whereIn('category_id', $ids)
                ->distinct()
                ->pluck('category_id')
                ->all();

            $pivotLinkedCategoryIds = DB::table('product_categories')
                ->whereIn('category_id', $ids)
                ->distinct()
                ->pluck('category_id')
                ->all();

            $linkedCategoryIds = collect($linkedCategoryIds)
                ->merge($pivotLinkedCategoryIds)
                ->unique()
                ->values()
                ->all();

            if (! empty($linkedCategoryIds)) {
                $linkedCategoryNames = Category::query()
                    ->whereIn('id', $linkedCategoryIds)
                    ->pluck('name')
                    ->filter()
                    ->implode(', ');

                return $this->response()->error(
                    $linkedCategoryNames !== ''
                        ? 'These categories have products, so they cannot be deleted: ' . $linkedCategoryNames
                        : 'This category has products, so it cannot be deleted'
                );
            }

            $categories = Category::query()
                ->whereIn('id', $ids)
                ->get();

            $deletedCount = Category::query()->whereIn('id', $ids)->delete();

            foreach ($categories as $category) {
                $this->activityLogService->log(
                    'category',
                    'deleted',
                    (int) $category->id,
                    (string) $category->name,
                    $this->snapshotCategory($category),
                    null
                );
            }

            $this->flushCategoryCaches();
            return $this->response(['deleted_count' => $deletedCount])->success(
                $deletedCount > 1 ? 'Categories deleted successfully' : 'Category deleted successfully'
            );
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    /**
     * Refresh caches that embed the category list (shared menu data + the
     * cached static landing pages that show the category grid).
     */
    private function flushCategoryCaches(): void
    {
        Cache::forget('shared_view_data_v2');
        Cache::forget('shared_view_data_v3');
        Cache::forget('shared_view_data_v4');
        LandingController::flushCache();
    }

    private function prepareData(array $payload): array
    {
        $name = trim((string) ($payload['name'] ?? ''));

        return [
            'name' => $name,
            'serial' => (int) ($payload['serial'] ?? 0),
            'slug' => Str::slug($name),
            'meta_title' => $payload['meta_title'] ?? null,
            'meta_description' => $payload['meta_description'] ?? null,
            'front_view' => (int) ($payload['front_view'] ?? 0),
            'banner_image' => (int) ($payload['banner_image'] ?? 0),
            'status' => (int) ($payload['status'] ?? 0),
            'featured' => (int) ($payload['featured'] ?? 0),
        ];
    }

    private function saveCategory(array $payload, ?Category $category = null): Category
    {
        $category ??= new Category();
        $data = $this->prepareData($payload);

        $image = $payload['image'] ?? null;
        if ($image instanceof UploadedFile) {
            $data['image'] = $this->mediaService->createFromUpload($image)->path;
        } elseif (filled($payload['image_media_id'] ?? null)) {
            $imagePath = Media::query()->whereKey($payload['image_media_id'])->value('path');

            if ($imagePath) {
                $data['image'] = $imagePath;
            }
        }

        $icon = $payload['icon'] ?? null;
        if ($icon instanceof UploadedFile) {
            $data['icon'] = $this->mediaService->createFromUpload($icon)->path;
        } elseif (filled($payload['icon_media_id'] ?? null)) {
            $iconPath = Media::query()->whereKey($payload['icon_media_id'])->value('path');

            if ($iconPath) {
                $data['icon'] = $iconPath;
            }
        }

        $category->fill($data);
        $category->save();

        return $category;
    }

    private function snapshotCategory(Category $category): array
    {
        return [
            'id' => (int) $category->id,
            'name' => (string) $category->name,
            'serial' => (int) ($category->serial ?? 0),
            'slug' => (string) $category->slug,
            'status' => (int) ($category->status ?? 0),
            'front_view' => (int) ($category->front_view ?? 0),
            'featured' => (int) ($category->featured ?? 0),
            'image' => (string) ($category->image ?? ''),
            'icon' => (string) ($category->icon ?? ''),
        ];
    }
}
