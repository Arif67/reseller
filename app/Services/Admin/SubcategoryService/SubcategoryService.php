<?php

namespace App\Services\Admin\SubcategoryService;

use App\Models\Category;
use App\Models\Subcategory;
use App\Services\AppService\FileUploadService;
use App\Traits\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubcategoryService
{
    use Response;

    public function __construct(
        private readonly FileUploadService $fileUploadService
    ) {
    }

    public function getListData(): array
    {
        try {
            return $this->response([
                'data' => Subcategory::query()->orderBy('id', 'DESC')->with('category')->get(),
            ])->success();
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function getCreateData(): array
    {
        try {
            return $this->response([
                'categories' => Category::query()->select('id', 'name')->get(),
            ])->success();
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function getEditData(int|string $id): array
    {
        try {
            return $this->response([
                'edit_data' => Subcategory::query()->findOrFail($id),
                'categories' => Category::query()->select('id', 'name')->get(),
            ])->success();
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function storeSubcategory(array $payload): array
    {
        try {
            $subcategory = DB::transaction(fn () => $this->saveSubcategory($payload));

            return $this->response(['subcategory' => $subcategory])->success('Data insert successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function updateSubcategory(array $payload): array
    {
        try {
            $subcategoryId = (int) ($payload['id'] ?? 0);
            $subcategory = DB::transaction(function () use ($payload, $subcategoryId) {
                $subcategory = Subcategory::query()->findOrFail($subcategoryId);

                return $this->saveSubcategory($payload, $subcategory);
            });

            return $this->response(['subcategory' => $subcategory])->success('Data update successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function changeSubcategoryStatus(array $payload): array
    {
        try {
            $subcategory = Subcategory::query()->findOrFail((int) ($payload['hidden_id'] ?? 0));
            $status = (int) ($payload['status'] ?? 0);

            $subcategory->status = $status;
            $subcategory->save();

            return $this->response(['subcategory' => $subcategory])->success(
                $status === 1 ? 'Data active successfully' : 'Data inactive successfully'
            );
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function deleteSubcategory(array $payload): array
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
                return $this->response()->error('Please select subcategory first');
            }

            $subcategories = Subcategory::query()->whereIn('id', $ids)->get();
            $imagePaths = $subcategories->pluck('image')->filter()->all();
            $deletedCount = Subcategory::query()->whereIn('id', $ids)->delete();

            foreach ($imagePaths as $imagePath) {
                $this->fileUploadService->deleteLocalIfNeeded($imagePath);
            }

            return $this->response(['deleted_count' => $deletedCount])->success(
                $deletedCount > 1 ? 'Subcategories deleted successfully' : 'Subcategory deleted successfully'
            );
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    private function prepareData(array $payload): array
    {
        $name = trim((string) ($payload['subcategoryName'] ?? ''));

        return [
            'category_id' => (int) ($payload['category_id'] ?? 0),
            'subcategoryName' => $name,
            'slug' => Str::slug($name),
            'meta_title' => $payload['meta_title'] ?? null,
            'meta_description' => $payload['meta_description'] ?? null,
            'status' => (int) ($payload['status'] ?? 0),
        ];
    }

    private function saveSubcategory(array $payload, ?Subcategory $subcategory = null): Subcategory
    {
        $subcategory ??= new Subcategory();
        $data = $this->prepareData($payload);
        $previousImage = $subcategory->exists ? $subcategory->image : null;

        $image = $payload['image'] ?? null;
        if ($image instanceof UploadedFile) {
            $data['image'] = $this->uploadImage($image);
        } elseif (filled($payload['image'] ?? null)) {
            $data['image'] = $payload['image'];
        } elseif ($subcategory->exists) {
            $data['image'] = $subcategory->image;
        }

        $subcategory->fill($data);
        $subcategory->save();

        if ($image instanceof UploadedFile && filled($previousImage)) {
            $this->fileUploadService->deleteLocalIfNeeded($previousImage);
        }

        return $subcategory;
    }

    private function uploadImage(UploadedFile $image): string
    {
        return $this->fileUploadService->processAndUploadImage(
            $image,
            'uploads/subcategory',
            ['prefix' => 'subcategory']
        );
    }
}
