<?php

namespace App\Services\Admin\ChildcategoryService;

use App\Models\Childcategory;
use App\Traits\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChildcategoryService
{
    use Response;

    public function getListData(): array
    {
        try {
            return $this->response([
                'data' => Childcategory::query()->orderBy('id', 'DESC')->with('subcategory')->get(),
            ])->success();
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function getEditData(int|string $id): array
    {
        try {
            return $this->response([
                'edit_data' => Childcategory::query()->findOrFail($id),
            ])->success();
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function storeChildcategory(array $payload): array
    {
        try {
            $childcategory = DB::transaction(fn () => $this->saveChildcategory($payload));

            return $this->response(['childcategory' => $childcategory])->success('Data insert successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function updateChildcategory(array $payload): array
    {
        try {
            $childcategoryId = (int) ($payload['id'] ?? 0);
            $childcategory = DB::transaction(function () use ($payload, $childcategoryId) {
                $childcategory = Childcategory::query()->findOrFail($childcategoryId);

                return $this->saveChildcategory($payload, $childcategory);
            });

            return $this->response(['childcategory' => $childcategory])->success('Data update successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function changeChildcategoryStatus(array $payload): array
    {
        try {
            $childcategory = Childcategory::query()->findOrFail((int) ($payload['hidden_id'] ?? 0));
            $status = (int) ($payload['status'] ?? 0);

            $childcategory->status = $status;
            $childcategory->save();

            return $this->response(['childcategory' => $childcategory])->success(
                $status === 1 ? 'Data active successfully' : 'Data inactive successfully'
            );
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function deleteChildcategory(array $payload): array
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
                return $this->response()->error('Please select childcategory first');
            }

            $deletedCount = Childcategory::query()->whereIn('id', $ids)->delete();

            return $this->response(['deleted_count' => $deletedCount])->success(
                $deletedCount > 1 ? 'Data delete successfully' : 'Data delete successfully'
            );
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    private function prepareData(array $payload): array
    {
        $name = trim((string) ($payload['childcategoryName'] ?? ''));

        return [
            'subcategory_id' => (int) ($payload['subcategory_id'] ?? 0),
            'childcategoryName' => $name,
            'slug' => Str::slug($name),
            'meta_title' => $payload['meta_title'] ?? null,
            'meta_description' => $payload['meta_description'] ?? null,
            'status' => (int) ($payload['status'] ?? 0),
        ];
    }

    private function saveChildcategory(array $payload, ?Childcategory $childcategory = null): Childcategory
    {
        $childcategory ??= new Childcategory();
        $childcategory->fill($this->prepareData($payload));
        $childcategory->save();

        return $childcategory;
    }
}
