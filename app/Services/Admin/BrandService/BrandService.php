<?php

namespace App\Services\Admin\BrandService;

use App\Models\Brand;
use App\Models\Media;
use App\Services\AppService\FileUploadService;
use App\Traits\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BrandService
{
    use Response;

    public function __construct(
        private readonly FileUploadService $fileUploadService
    ) {
    }

    public function getListData(array $query): array
    {
        try {
            if (request()->ajax()) {
                $brands = Brand::query()
                    ->select('id', 'name', 'image', 'status')
                    ->latest('id');

                $tableData = DataTables::eloquent($brands)
                    ->addIndexColumn()
                    ->addColumn('checkbox', function (Brand $brand): string {
                        return '<input type="checkbox" class="form-check-input brand-checkbox" value="' . $brand->id . '">';
                    })
                    ->addColumn('image_preview', function (Brand $brand): string {
                        if ($brand->image_url === '') {
                            return '<span class="text-muted">No image</span>';
                        }

                        return '<img src="' . e($brand->image_url) . '" class="backend-image" alt="Brand image">';
                    })
                    ->addColumn('status_badge', function (Brand $brand): string {
                        if ((int) $brand->status === 1) {
                            return '<span class="badge bg-soft-success text-success">Active</span>';
                        }

                        return '<span class="badge bg-soft-danger text-danger">Inactive</span>';
                    })
                    ->addColumn('action', function (Brand $brand): string {
                        $toggleRoute = $brand->status == 1
                            ? route('brands.inactive')
                            : route('brands.active');
                        $toggleButtonClass = $brand->status == 1
                            ? 'btn btn-xs btn-secondary waves-effect waves-light change-confirm'
                            : 'btn btn-xs btn-success waves-effect waves-light change-confirm';
                        $toggleIcon = $brand->status == 1 ? 'fe-thumbs-down' : 'fe-thumbs-up';
                        $editUrl = route('brands.edit', $brand->id);

                        return '<div class="button-list">'
                            . '<form method="post" action="' . $toggleRoute . '" class="d-inline">'
                            . csrf_field()
                            . '<input type="hidden" value="' . $brand->id . '" name="hidden_id">'
                            . '<button type="button" class="' . $toggleButtonClass . '"><i class="fe ' . $toggleIcon . '"></i></button>'
                            . '</form>'
                            . '<a href="' . $editUrl . '" class="btn btn-xs btn-primary waves-effect waves-light"><i class="fe-edit-1"></i></a>'
                            . '<form method="post" action="' . route('brands.destroy') . '" class="d-inline">'
                            . csrf_field()
                            . '<input type="hidden" value="' . $brand->id . '" name="hidden_id">'
                            . '<button type="submit" class="btn btn-xs btn-danger waves-effect waves-light delete-confirm"><i class="mdi mdi-close"></i></button>'
                            . '</form>'
                            . '</div>';
                    })
                    ->rawColumns(['checkbox', 'image_preview', 'status_badge', 'action'])
                    ->make(true)
                    ->getData(true);

                return $this->response($tableData)->success();
            }

            return $this->response([])->success();
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function getEditData(int|string $id): array
    {
        try {
            $brand = Brand::query()->findOrFail($id);

            return $this->response([
                'edit_data' => $brand,
                'selectedImageMediaId' => $brand->image ? Media::query()->where('path', $brand->image)->value('id') : null,
            ])->success();
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function storeBrand(array $payload): array
    {
        try {
            $brand = DB::transaction(fn () => $this->saveBrand($payload));

            return $this->response(['brand' => $brand])->success('Brand inserted successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function updateBrand(array $payload): array
    {
        try {
            $brandId = (int) ($payload['id'] ?? 0);
            $brand = DB::transaction(function () use ($payload, $brandId) {
                $brand = Brand::query()->findOrFail($brandId);

                return $this->saveBrand($payload, $brand);
            });

            return $this->response(['brand' => $brand])->success('Brand updated successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function changeBrandStatus(array $payload): array
    {
        try {
            $brand = Brand::query()->findOrFail((int) ($payload['hidden_id'] ?? 0));
            $status = (int) ($payload['status'] ?? 0);

            $brand->status = $status;
            $brand->save();

            return $this->response(['brand' => $brand])->success(
                $status === 1 ? 'Data active successfully' : 'Data inactive successfully'
            );
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function deleteBrand(array $payload): array
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
                return $this->response()->error('Please select brand first');
            }

            $brands = Brand::query()->whereIn('id', $ids)->get();
            $imagePaths = $brands->pluck('image')->all();
            $deletedCount = 0;

            DB::transaction(function () use ($brands, &$deletedCount) {
                foreach ($brands as $brand) {
                    $brand->delete();
                    $deletedCount++;
                }
            });

            foreach ($imagePaths as $imagePath) {
                $this->fileUploadService->deleteLocalIfNeeded($imagePath);
            }

            return $this->response(['deleted_count' => $deletedCount])->success(
                $deletedCount > 1 ? 'Brands deleted successfully' : 'Brand deleted successfully'
            );
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    private function prepareData(array $payload): array
    {
        $name = trim((string) ($payload['name'] ?? ''));

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'status' => (int) ($payload['status'] ?? 0),
        ];
    }

    private function saveBrand(array $payload, ?Brand $brand = null): Brand
    {
        $brand ??= new Brand();
        $data = $this->prepareData($payload);
        $previousImage = $brand->exists ? $brand->image : null;

        $image = $payload['image'] ?? null;
        if ($image instanceof UploadedFile) {
            $data['image'] = $this->fileUploadService->processAndUploadImage(
                $image,
                'uploads/brand',
                ['width' => 210, 'height' => 210, 'prefix' => 'brand']
            );
        } elseif (filled($payload['image_media_id'] ?? null)) {
            $imagePath = Media::query()->whereKey($payload['image_media_id'])->value('path');

            if ($imagePath) {
                $data['image'] = $imagePath;
            }
        }

        $brand->fill($data);
        $brand->save();

        if (($image instanceof UploadedFile || filled($payload['image_media_id'] ?? null))
            && filled($previousImage)
            && ($data['image'] ?? null) !== $previousImage) {
            $this->fileUploadService->deleteLocalIfNeeded($previousImage);
        }

        return $brand;
    }
}
