<?php

namespace App\Services\Admin\ProductService;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Childcategory;
use App\Models\Media;
use App\Models\Product;
use App\Models\ProductVariable;
use App\Models\Productimage;
use App\Models\Subcategory;
use App\Services\Admin\AdminActivityLogService;
use App\Services\Admin\MediaService\MediaService;
use App\Services\AppService\ProductAttributeService;
use App\Traits\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class ProductService
{
    use Response;

    public function __construct(
        private readonly ProductAttributeService $productAttributeService,
        private readonly MediaService $mediaService,
        private readonly AdminActivityLogService $activityLogService,
    ) {}

    public function getIndexData(Request $request): array|JsonResponse
    {
        try {
            $query = Product::query()->orderBy('id', 'DESC')->with('image', 'media', 'category', 'categories', 'brand', 'variables');

            if ($request->keyword) {
                $query->where('name', 'LIKE', '%' . $request->keyword . '%');
            }

            if ($request->filled('category_id')) {
                $query->forCategory($request->category_id);
            }

            if ($request->filled('brand_id')) {
                $query->where('brand_id', $request->brand_id);
            }

            if ($request->filled('subcategory_id')) {
                $query->where('subcategory_id', $request->subcategory_id);
            }

            if ($request->filled('childcategory_id')) {
                $query->where('childcategory_id', $request->childcategory_id);
            }

            if ($request->ajax()) {
                return DataTables::eloquent($query)
                    ->addIndexColumn()
                    ->addColumn('checkbox', function (Product $product): string {
                        return '<input type="checkbox" class="checkbox" value="' . $product->id . '">';
                    })
                    ->addColumn('action', function (Product $product): string {
                        $toggleRoute = $product->status == 1
                            ? route('products.inactive')
                            : route('products.active');
                        $toggleButtonClass = $product->status == 1
                            ? 'change-confirm btn btn-xs btn-secondary waves-effect waves-light'
                            : 'change-confirm btn btn-xs btn-success waves-effect waves-light';
                        $toggleIcon = $product->status == 1 ? 'fe-thumbs-down' : 'fe-thumbs-up';

                        return '<div class="button-list custom-btn-list">'
                            . '<form method="post" action="' . $toggleRoute . '" class="d-inline">'
                            . csrf_field()
                            . '<input type="hidden" value="' . $product->id . '" name="hidden_id">'
                            . '<button type="button" class="' . $toggleButtonClass . '" title="Toggle status"><i class="fe ' . $toggleIcon . '"></i></button>'
                            . '</form>'
                            . '<a href="' . route('products.edit', $product->id) . '" title="Edit"><i class="fe-edit"></i></a>'
                            . '<a href="' . route('products.barcode_labels', $product->id) . '" title="Print Barcode" target="_blank"><i class="fe-printer"></i></a>'
                            . '<a href="' . route('products.copy', $product->id) . '" title="Copy" onclick="return confirm(\'Create a copy of this product?\')"><i class="fe-copy"></i></a>'
                            . '<form method="post" action="' . route('products.destroy') . '" class="d-inline">'
                            . csrf_field()
                            . '<input type="hidden" value="' . $product->id . '" name="hidden_id">'
                            . '<button type="submit" class="delete-confirm" title="Delete"><i class="fe-trash-2"></i></button>'
                            . '</form>'
                            . '</div>';
                    })
                    ->addColumn('category_name', function (Product $product): string {
                        return e($product->categories->pluck('name')->filter()->implode(', '));
                    })
                    ->addColumn('image_preview', function (Product $product): string {
                        $primaryImage = $product->primary_media_image ?? optional($product->image)->image ?? 'uploads/logo.png';

                        return '<img src="' . e(asset($primaryImage)) . '" class="backend-image" alt="">';
                    })
                    ->addColumn('price', function (Product $product): string {
                        return e((string) $product->display_new_price);
                    })
                    ->addColumn('stock_value', function (Product $product): string {
                        return (string) ($product->variables->count() > 0 ? $product->variables->sum('stock') : $product->stock);
                    })
                    ->addColumn('deal_feature', function (Product $product): string {
                        return '<p class="m-0">Hot Deals : ' . ($product->topsale == 1 ? 'Yes' : 'No') . '</p>'
                            . '<p class="m-0">Top Feature : ' . ($product->feature_product == 1 ? 'Yes' : 'No') . '</p>';
                    })
                    ->addColumn('status_badge', function (Product $product): string {
                        return $product->status == 1
                            ? '<span class="badge bg-soft-success text-success">Active</span>'
                            : '<span class="badge bg-soft-danger text-danger">Inactive</span>';
                    })
                    ->rawColumns(['checkbox', 'action', 'image_preview', 'deal_feature', 'status_badge'])
                    ->make(true);
            }

            $selectedCategoryId = $request->input('category_id');
            $selectedSubcategoryId = $request->input('subcategory_id');

            return $this->response([
                'data' => $query->get(),
                'categories' => Category::where('status', 1)->orderBy('name')->select('id', 'name')->get(),
                'brands' => Brand::where('status', 1)->orderBy('name')->select('id', 'name')->get(),
                'subcategories' => Subcategory::query()
                    ->when($selectedCategoryId, fn($subQuery) => $subQuery->where('category_id', $selectedCategoryId))
                    ->where('status', 1)
                    ->orderBy('subcategoryName')
                    ->select('id', 'subcategoryName', 'category_id')
                    ->get(),
                'childcategories' => Childcategory::query()
                    ->when($selectedSubcategoryId, fn($childQuery) => $childQuery->where('subcategory_id', $selectedSubcategoryId))
                    ->where('status', 1)
                    ->orderBy('childcategoryName')
                    ->select('id', 'childcategoryName', 'subcategory_id')
                    ->get(),
            ])->success();
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function getCreateData(Request $request): array
    {
        try {
            $selectedAttributeIds = old('selected_attribute_ids', []);
            $selectedMediaIds = old('selected_media_ids', []);
            $selectedVariableMediaIds = old('selected_variable_media_ids', []);
            $selectedCategoryIds = old('category_ids', []);
            $primaryCategoryId = old('primary_category_id');
            $mediaLibrary = $this->loadMediaLibrary(array_merge(
                $selectedMediaIds,
                ...array_values($selectedVariableMediaIds ?: [])
            ));

            return $this->response([
                'categories' => Category::where('status', 1)->select('id', 'name', 'status')->get(),
                'brands' => Brand::where('status', 1)->select('id', 'name', 'status')->get(),
                'attributes' => $this->productAttributeService->getActiveAttributes(),
                'primaryCategoryId' => $primaryCategoryId,
                'selectedCategoryIds' => $selectedCategoryIds,
                'selectedAttributeIds' => $selectedAttributeIds,
                'mediaLibrary' => $mediaLibrary,
                'selectedMediaIds' => $selectedMediaIds,
                'selectedVariableMediaIds' => $selectedVariableMediaIds,
            ])->success();
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function storeProduct(Request $request): array
    {
        try {
            $this->validateProductRequest($request);

            $nextProductId = (Product::max('id') ?? 0) + 1;
            $input = $this->buildProductInput($request, $nextProductId);
            $product = Product::create($input);
            $this->syncProductCategories($product, $this->resolveCategoryIds($request));

            $uploadedProductMediaIds = $this->saveGalleryImages($product, $request->file('image'));
            $this->syncProductMedia($product, array_merge(
                $this->sanitizeMediaIds($request->input('selected_media_ids', [])),
                $uploadedProductMediaIds
            ));
            $this->createVariableRows($product, $request);

            $this->activityLogService->log(
                'product',
                'created',
                (int) $product->id,
                (string) $product->name,
                null,
                $this->snapshotProduct($product->fresh(['media', 'allVariables']))
            );

            return $this->response(['product' => $product])->success('Data insert successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function getEditData(int|string $id, Request $request): array
    {
        try {
            $editData = Product::with('images', 'media', 'categories')->findOrFail($id);
            $categoryId = $editData->category_id;
            $subcategoryId = $editData->subcategory_id;
            $variables = ProductVariable::with('selectedValues.attribute', 'media')->where('product_id', $id)->get();
            $variableSelections = [];
            $existingVariableMediaIds = [];

            foreach ($variables as $loopIndex => $variable) {
                $variableSelections[$variable->id] = $this->productAttributeService->getSelectedValueMap($variable);
                $existingVariableMediaIds[$loopIndex] = old('up_selected_variable_media_ids.' . $loopIndex, $variable->media->pluck('id')->all());
            }

            $selectedAttributeIds = old('selected_attribute_ids', $editData->selected_attribute_ids ?? []);
            $selectedMediaIds = old('selected_media_ids', $editData->media->pluck('id')->all());
            $selectedVariableMediaIds = old('selected_variable_media_ids', []);
            $selectedCategoryIds = old('category_ids', $editData->categories->pluck('id')->all());
            $primaryCategoryId = old('primary_category_id', $editData->category_id);

            if (empty($selectedAttributeIds)) {
                $selectedAttributeIds = collect($variableSelections)
                    ->flatMap(fn($selection) => array_keys($selection))
                    ->map(fn($attributeId) => (int) $attributeId)
                    ->unique()
                    ->values()
                    ->all();
            }

            $mediaLibrary = $this->loadMediaLibrary(array_merge(
                $selectedMediaIds,
                ...array_values($existingVariableMediaIds ?: []),
                ...array_values($selectedVariableMediaIds ?: [])
            ));

            return $this->response([
                'edit_data' => $editData,
                'categories' => Category::where('status', 1)->select('id', 'name', 'status')->get(),
                'subcategory' => Subcategory::where('category_id', $categoryId)->select('id', 'subcategoryName', 'status')->get(),
                'childcategory' => Childcategory::where('subcategory_id', $subcategoryId)->select('id', 'childcategoryName', 'status')->get(),
                'brands' => Brand::where('status', 1)->select('id', 'name', 'status')->get(),
                'variables' => $variables,
                'attributes' => $this->productAttributeService->getActiveAttributes(),
                'primaryCategoryId' => $primaryCategoryId,
                'selectedCategoryIds' => $selectedCategoryIds,
                'selectedAttributeIds' => $selectedAttributeIds,
                'variableSelections' => $variableSelections,
                'mediaLibrary' => $mediaLibrary,
                'selectedMediaIds' => $selectedMediaIds,
                'existingVariableMediaIds' => $existingVariableMediaIds,
                'selectedVariableMediaIds' => $selectedVariableMediaIds,
            ])->success();
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function updateProduct(Request $request): array
    {
        try {
            $this->validateProductRequest($request);

            $product = Product::findOrFail($request->id);
            $before = $this->snapshotProduct($product->loadMissing(['media', 'allVariables', 'categories']));
            $product->update($this->buildProductInput($request, $product->id));
            $this->syncProductCategories($product, $this->resolveCategoryIds($request));

            $uploadedProductMediaIds = $this->saveGalleryImages($product, $request->file('image'));
            $this->syncProductMedia($product, array_merge(
                $this->sanitizeMediaIds($request->input('selected_media_ids', [])),
                $uploadedProductMediaIds
            ));
            $this->updateVariableRows($product, $request);
            $this->createVariableRows($product, $request);

            $this->activityLogService->log(
                'product',
                'updated',
                (int) $product->id,
                (string) $product->name,
                $before,
                $this->snapshotProduct($product->fresh(['media', 'allVariables', 'categories']))
            );

            return $this->response(['product' => $product])->success('Data update successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function priceEditData(): array
    {
        try {
            return $this->response([
                'products' => DB::table('products')
                    ->select('id', 'name', 'status', 'old_price', 'new_price', 'stock')
                    ->where('status', 1)
                    ->get(),
            ])->success();
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function updatePrices(Request $request): array
    {
        try {
            $ids = $request->input('ids', []);
            $oldPrices = $request->input('old_price', []);
            $newPrices = $request->input('new_price', []);
            $stocks = $request->input('stock', []);

            foreach ($ids as $index => $id) {
                $product = Product::find($id);

                if (! $product) {
                    continue;
                }

                $product->old_price = $oldPrices[$index] ?? $product->old_price;
                $product->new_price = $newPrices[$index] ?? $product->new_price;
                $product->stock = $stocks[$index] ?? $product->stock;
                $product->save();
            }

            return $this->response(['updated_count' => count($ids)])->success('Product prices updated successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function barcodeLabelsData(int|string $id): array
    {
        try {
            return $this->response([
                'product' => Product::with(['variables.selectedValues.attribute'])->findOrFail($id),
            ])->success();
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function copyProduct(int|string $id): array
    {
        try {
            $sourceProduct = Product::with(['images', 'media', 'categories', 'variables.selectedValues', 'variables.media'])->findOrFail($id);

            $copiedProduct = DB::transaction(function () use ($sourceProduct) {
                $nextProductId = (Product::max('id') ?? 0) + 1;

                $newProduct = $sourceProduct->replicate([
                    'slug',
                    'product_code',
                    'pro_barcode',
                    'created_at',
                    'updated_at',
                ]);

                $newProduct->name = $this->buildCopiedProductName($sourceProduct->name);
                $newProduct->slug = strtolower(preg_replace('/[\/\s]+/', '-', $newProduct->name . '-' . $nextProductId));
                $newProduct->product_code = 'P' . str_pad($nextProductId, 4, '0', STR_PAD_LEFT);
                $newProduct->pro_barcode = null;
                $newProduct->status = 0;
                $newProduct->topsale = 0;
                $newProduct->save();

                foreach ($sourceProduct->images as $image) {
                    Productimage::create([
                        'product_id' => $newProduct->id,
                        'image' => $image->image,
                    ]);
                }

                $this->syncProductMedia($newProduct, $sourceProduct->media->pluck('id')->all());
                $this->syncProductCategories($newProduct, $sourceProduct->categories->pluck('id')->all());

                foreach ($sourceProduct->variables as $variable) {
                    $newVariable = $variable->replicate([
                        'product_id',
                        'barcode',
                        'created_at',
                        'updated_at',
                    ]);
                    $newVariable->product_id = $newProduct->id;
                    $newVariable->barcode = null;
                    $newVariable->save();

                    $this->productAttributeService->syncSelectedValues($newVariable, $variable->selectedValues->pluck('id')->all());
                    $this->syncVariableMedia($newVariable, $variable->media->pluck('id')->all());
                }

                return $newProduct;
            });

            return $this->response(['product' => $copiedProduct])->success('Product copied successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function changeStatus(int|string $id, int $status, string $message): array
    {
        try {
            $product = Product::query()->findOrFail((int) $id);
            $before = $this->snapshotProduct($product);
            $product->status = $status;
            $product->save();

            $this->activityLogService->log(
                'product',
                $status === 1 ? 'activated' : 'inactivated',
                (int) $product->id,
                (string) $product->name,
                $before,
                $this->snapshotProduct($product)
            );

            return $this->response(['product' => $product])->success($message);
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function updateDeals(array $productIds, int $status): array
    {
        try {
            if (empty($productIds)) {
                return $this->response()->error('No product selected');
            }

            Product::whereIn('id', $productIds)->update(['topsale' => $status]);

            return $this->response(['updated_count' => count($productIds)])->success('Hot deals updated successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function updateFeature(array $productIds, int $status): array
    {
        try {
            if (empty($productIds)) {
                return $this->response()->error('No product selected');
            }

            Product::whereIn('id', $productIds)->update(['feature_product' => $status]);

            return $this->response(['updated_count' => count($productIds)])->success('Featured status updated successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function updateStatus(array $productIds, int $status): array
    {
        try {
            if (empty($productIds)) {
                return $this->response()->error('No product selected');
            }

            Product::whereIn('id', $productIds)->update(['status' => $status]);

            return $this->response(['updated_count' => count($productIds)])->success('Product status updated successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function deleteProduct(int|string $id): array
    {
        try {
            $deleteData = Product::with('variables.media', 'media', 'images', 'categories')->findOrFail($id);
            $before = $this->snapshotProduct($deleteData->loadMissing('allVariables'));

            foreach ($deleteData->variables as $variable) {
                $variable->media()->detach();
                $variable->selectedValues()->detach();
                $variable->delete();
            }

            $deleteData->media()->detach();
            $deleteData->categories()->detach();

            foreach ($deleteData->images as $pimage) {
                $pimage->delete();
            }

            $deleteData->delete();

            $this->activityLogService->log(
                'product',
                'deleted',
                (int) $id,
                (string) $deleteData->name,
                $before,
                null
            );

            return $this->response(['product' => $deleteData])->success('Data delete successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function deleteImage(int|string $id): array
    {
        try {
            $deleteData = Productimage::find($id);
            $deleteData?->delete();

            return $this->response(['image_id' => $id])->success('Data delete successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function deleteVariable(int|string $id): array
    {
        try {
            $deleteData = ProductVariable::findOrFail($id);
            $deleteData->media()->detach();
            $deleteData->selectedValues()->detach();
            $deleteData->delete();

            return $this->response(['variable_id' => $id])->success('Data delete successfully');
        } catch (\Throwable $exception) {
            return $this->response()->error($exception->getMessage());
        }
    }

    public function getSubcategoryOptions(int|string $categoryId): array
    {
        return DB::table('subcategories')
            ->where('category_id', $categoryId)
            ->pluck('subcategoryName', 'id')
            ->all();
    }

    public function getChildcategoryOptions(int|string $subcategoryId): array
    {
        return DB::table('childcategories')
            ->where('subcategory_id', $subcategoryId)
            ->pluck('childcategoryName', 'id')
            ->all();
    }

    private function validateProductRequest(Request $request): void
    {
        $rules = [
            'name' => 'required',
            'primary_category_id' => 'required|integer|exists:categories,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'required|integer|exists:categories,id',
            'description' => 'required',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_tag' => 'nullable|string',
            'variation_pricing_mode' => 'nullable|in:same,different',
            'image' => 'nullable|array',
            'image.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,avif',
            'selected_media_ids' => 'nullable|array',
            'selected_media_ids.*' => 'nullable|integer|exists:media,id',
            'images' => 'nullable|array',
            'images.*' => 'nullable|array|max:2',
            'images.*.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,avif',
            'selected_variable_media_ids' => 'nullable|array',
            'up_selected_variable_media_ids' => 'nullable|array',
            'up_images' => 'nullable|array',
            'up_images.*' => 'nullable|array|max:2',
            'up_images.*.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,avif',
        ];

        if ((int) $request->input('type', 1) === 0) {
            $rules['selected_attribute_ids'] = 'required|array|min:1';
        }

        if ((int) $request->input('type', 1) !== 0 || $this->resolveVariationPricingMode($request) === 'same') {
            $rules['purchase_price'] = 'required';
            $rules['new_price'] = 'required';
        }

        if ((int) $request->input('type', 1) !== 0) {
            $rules['stock'] = 'required';
        }

        $rules['pro_barcode'] = 'nullable|string|max:255';
        $rules['barcodes'] = 'nullable|array';
        $rules['barcodes.*'] = 'nullable|string|max:255';
        $rules['up_barcodes'] = 'nullable|array';
        $rules['up_barcodes.*'] = 'nullable|string|max:255';

        $request->validate($rules, [
            'primary_category_id.required' => 'Select a primary category.',
            'category_ids.required' => 'Select at least one category.',
            'category_ids.min' => 'Select at least one category.',
            'selected_attribute_ids.required' => 'Select at least one attribute for variable products.',
            'selected_attribute_ids.min' => 'Select at least one attribute for variable products.',
        ]);

        $this->validateUniqueBarcodes($request);
    }

    private function snapshotProduct(Product $product): array
    {
        $variableCount = $product->relationLoaded('allVariables')
            ? $product->allVariables->count()
            : $product->allVariables()->count();

        $mediaIds = $product->relationLoaded('media')
            ? $product->media->pluck('id')->values()->all()
            : $product->media()->pluck('media.id')->values()->all();

        $categoryIds = $product->relationLoaded('categories')
            ? $product->categories->pluck('id')->values()->all()
            : $product->categories()->pluck('categories.id')->values()->all();

        return [
            'id' => (int) $product->id,
            'name' => (string) $product->name,
            'slug' => (string) $product->slug,
            'status' => (int) ($product->status ?? 0),
            'category_id' => (int) ($product->category_id ?? 0),
            'category_ids' => $categoryIds,
            'subcategory_id' => (int) ($product->subcategory_id ?? 0),
            'childcategory_id' => (int) ($product->childcategory_id ?? 0),
            'brand_id' => (int) ($product->brand_id ?? 0),
            'type' => (int) ($product->type ?? 0),
            'stock' => (int) ($product->stock ?? 0),
            'new_price' => (float) ($product->new_price ?? 0),
            'old_price' => (float) ($product->old_price ?? 0),
            'purchase_price' => (float) ($product->purchase_price ?? 0),
            'variable_count' => $variableCount,
            'media_ids' => $mediaIds,
        ];
    }

    private function buildProductInput(Request $request, int $productId): array
    {
        $input = $request->except([
            'image',
            'primary_category_id',
            'category_ids',
            'product_type',
            'files',
            'selected_media_ids',
            'selected_variable_media_ids',
            'up_selected_variable_media_ids',
            'purchase_prices',
            'old_prices',
            'new_prices',
            'stocks',
            'images',
            'up_id',
            'up_purchase_prices',
            'up_old_prices',
            'up_new_prices',
            'up_stocks',
            'up_images',
            'attribute_values',
            'up_attribute_values',
            'barcodes',
            'up_barcodes',
        ]);

        $categoryIds = $this->resolveCategoryIds($request);

        $input['category_id'] = (int) $request->input('primary_category_id');
        $input['slug'] = strtolower(preg_replace('/[\/\s]+/', '-', $request->name . '-' . $productId));
        $input['meta_title'] = $request->filled('meta_title') ? $request->meta_title : $request->name;
        $input['meta_description'] = $this->resolveMetaDescription($request);
        $input['meta_tag'] = $request->meta_tag;
        $input['variation_pricing_mode'] = $this->resolveVariationPricingMode($request);
        $input['pro_barcode'] = $this->normalizeBarcode($request->input('pro_barcode'));
        $input['selected_attribute_ids'] = $this->sanitizeAttributeIds($request->input('selected_attribute_ids', []));
        $input['status'] = $request->status ? 1 : 0;
        $input['topsale'] = $request->topsale ? 1 : 0;
        $input['feature_product'] = $request->feature_product ? 1 : 0;
        $input['free_shipping'] = $request->free_shipping ? 1 : 0;

        if (! isset($input['product_code'])) {
            $input['product_code'] = 'P' . str_pad($productId, 4, '0', STR_PAD_LEFT);
        }

        return $input;
    }

    private function resolveCategoryIds(Request $request): array
    {
        return collect($request->input('category_ids', []))
            ->prepend($request->input('primary_category_id'))
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value) => (int) $value)
            ->filter(fn ($value) => $value > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function syncProductCategories(Product $product, array $categoryIds): void
    {
        $payload = [];

        foreach (array_values(array_unique(array_filter($categoryIds))) as $categoryId) {
            $payload[(int) $categoryId] = [];
        }

        $product->categories()->sync($payload);
    }

    private function resolveMetaDescription(Request $request): string
    {
        if ($request->filled('meta_description')) {
            return trim((string) $request->meta_description);
        }

        $plainDescription = trim(preg_replace('/\s+/', ' ', strip_tags((string) $request->description)));

        if ($plainDescription !== '') {
            return Str::limit($plainDescription, 160, '');
        }

        return Str::limit(trim((string) $request->name), 160, '');
    }

    private function resolveVariationPricingMode(Request $request): string
    {
        if ((int) $request->input('type', 1) !== 0) {
            return 'same';
        }

        $mode = (string) $request->input('variation_pricing_mode', 'same');

        return in_array($mode, ['same', 'different'], true) ? $mode : 'same';
    }

    private function resolveVariantPricingPayload(Request $request, Product $product, int $index, string $mode, bool $isUpdate = false): array
    {
        if ($mode === 'same') {
            return [
                'purchase_price' => $product->purchase_price,
                'old_price' => $product->old_price,
                'new_price' => $product->new_price,
            ];
        }

        $prefix = $isUpdate ? 'up_' : '';

        return [
            'purchase_price' => $request->input($prefix . 'purchase_prices.' . $index),
            'old_price' => $request->input($prefix . 'old_prices.' . $index),
            'new_price' => $request->input($prefix . 'new_prices.' . $index),
        ];
    }

    private function normalizeBarcode(mixed $value): ?string
    {
        $barcode = trim((string) $value);

        return $barcode !== '' ? $barcode : null;
    }

    private function validateUniqueBarcodes(Request $request): void
    {
        $messages = [];
        $productBarcode = $this->normalizeBarcode($request->input('pro_barcode'));
        $productId = (int) $request->input('id', 0);

        if ($productBarcode) {
            $productBarcodeExists = Product::query()
                ->where('pro_barcode', $productBarcode)
                ->when($productId > 0, fn($query) => $query->where('id', '!=', $productId))
                ->exists();

            $variantBarcodeExists = ProductVariable::query()->where('barcode', $productBarcode)->exists();

            if ($productBarcodeExists || $variantBarcodeExists) {
                $messages['pro_barcode'] = 'This barcode is already in use.';
            }
        }

        $seen = [];
        $checkVariantBarcode = function (?string $barcode, ?int $ignoreId = null, string $key = 'barcodes') use (&$messages, &$seen) {
            if (! $barcode) {
                return;
            }

            $normalized = mb_strtolower($barcode);
            if (isset($seen[$normalized])) {
                $messages[$key] = 'Duplicate barcode found in variation rows.';
                return;
            }
            $seen[$normalized] = true;

            $productExists = Product::query()->where('pro_barcode', $barcode)->exists();
            $variantExists = ProductVariable::query()
                ->where('barcode', $barcode)
                ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
                ->exists();

            if ($productExists || $variantExists) {
                $messages[$key] = 'This variation barcode is already in use.';
            }
        };

        foreach ((array) $request->input('barcodes', []) as $index => $barcode) {
            $checkVariantBarcode($this->normalizeBarcode($barcode), null, 'barcodes.' . $index);
        }

        $updateIds = array_values((array) $request->input('up_id', []));
        foreach ((array) $request->input('up_barcodes', []) as $index => $barcode) {
            $checkVariantBarcode($this->normalizeBarcode($barcode), isset($updateIds[$index]) ? (int) $updateIds[$index] : null, 'up_barcodes.' . $index);
        }

        if (! empty($messages)) {
            throw ValidationException::withMessages($messages);
        }
    }

    private function saveGalleryImages(Product $product, ?array $images): array
    {
        if (! $images) {
            return [];
        }

        $mediaIds = [];

        foreach ($images as $image) {
            $media = $this->createMediaFromUpload($image);
            $mediaIds[] = $media->id;

            $productImage = new Productimage();
            $productImage->product_id = $product->id;
            $productImage->image = $media->path;
            $productImage->save();
        }

        return $mediaIds;
    }

    private function updateVariableRows(Product $product, Request $request): void
    {
        $updateIds = array_values(array_filter($request->input('up_id', [])));
        $pricingMode = $this->resolveVariationPricingMode($request);

        foreach ($updateIds as $index => $variableId) {
            $variable = ProductVariable::find($variableId);
            if (! $variable) {
                continue;
            }

            $selectedValueIds = $this->productAttributeService->sanitizeSelectedValueIds(
                $request->input("up_attribute_values.$index", [])
            );

            $uploadedMediaIds = $this->uploadMediaFiles(
                $this->normalizeImageFiles(data_get($request->file('up_images', []), $index, [])),
                2
            );

            $mediaIds = array_values(array_unique(array_merge(
                $this->sanitizeMediaIds($request->input("up_selected_variable_media_ids.$index", [])),
                $uploadedMediaIds
            )));
            $mediaPaths = $this->getMediaPaths($mediaIds);
            $pricing = $this->resolveVariantPricingPayload($request, $product, $index, $pricingMode, true);

            $variable->fill([
                'product_id' => $product->id,
                'purchase_price' => $pricing['purchase_price'],
                'old_price' => $pricing['old_price'],
                'new_price' => $pricing['new_price'],
                'stock' => $request->input('up_stocks.' . $index),
                'barcode' => $this->normalizeBarcode($request->input('up_barcodes.' . $index)),
                'image' => $mediaPaths[0] ?? null,
                'images' => $mediaPaths,
            ]);

            $this->productAttributeService->fillLegacyColumns($variable, $selectedValueIds);
            $variable->save();
            $this->productAttributeService->syncSelectedValues($variable, $selectedValueIds);
            $this->syncVariableMedia($variable, $mediaIds);
        }
    }

    private function createVariableRows(Product $product, Request $request): void
    {
        $stocks = $request->input('stocks', []);
        $pricingMode = $this->resolveVariationPricingMode($request);

        foreach ($stocks as $index => $stock) {
            if (! filled($stock)) {
                continue;
            }

            if ($pricingMode === 'different' && (! filled($request->input('purchase_prices.' . $index)) || ! filled($request->input('new_prices.' . $index)))) {
                continue;
            }

            $selectedValueIds = $this->productAttributeService->sanitizeSelectedValueIds(
                $request->input("attribute_values.$index", [])
            );

            $uploadedMediaIds = $this->uploadMediaFiles(
                $this->normalizeImageFiles(data_get($request->file('images', []), $index, [])),
                2
            );

            $mediaIds = array_values(array_unique(array_merge(
                $this->sanitizeMediaIds($request->input("selected_variable_media_ids.$index", [])),
                $uploadedMediaIds
            )));
            $mediaPaths = $this->getMediaPaths($mediaIds);
            $pricing = $this->resolveVariantPricingPayload($request, $product, $index, $pricingMode);

            $variable = new ProductVariable([
                'product_id' => $product->id,
                'purchase_price' => $pricing['purchase_price'],
                'old_price' => $pricing['old_price'],
                'new_price' => $pricing['new_price'],
                'stock' => $stock,
                'barcode' => $this->normalizeBarcode($request->input('barcodes.' . $index)),
                'image' => $mediaPaths[0] ?? null,
                'images' => $mediaPaths,
            ]);

            $this->productAttributeService->fillLegacyColumns($variable, $selectedValueIds);
            $variable->save();
            $this->productAttributeService->syncSelectedValues($variable, $selectedValueIds);
            $this->syncVariableMedia($variable, $mediaIds);
        }
    }

    private function syncProductMedia(Product $product, array $mediaIds): void
    {
        $payload = [];

        foreach (array_values(array_unique($this->sanitizeMediaIds($mediaIds))) as $position => $mediaId) {
            $payload[$mediaId] = ['position' => $position];
        }

        $product->media()->sync($payload);
    }

    private function syncVariableMedia(ProductVariable $variable, array $mediaIds): void
    {
        $payload = [];

        foreach (array_values(array_unique($this->sanitizeMediaIds($mediaIds))) as $position => $mediaId) {
            $payload[$mediaId] = ['position' => $position];
        }

        $variable->media()->sync($payload);
    }

    private function loadMediaLibrary(array $selectedIds = [])
    {
        $selectedIds = $this->sanitizeMediaIds($selectedIds);
        $selectedMedia = empty($selectedIds) ? collect() : Media::whereIn('id', $selectedIds)->get();
        $latestMedia = Media::latest()->take(24)->get();

        return $selectedMedia->concat($latestMedia)->unique('id')->values();
    }

    private function sanitizeAttributeIds(array $attributeIds): array
    {
        return collect($attributeIds)
            ->filter(fn($value) => filled($value))
            ->map(fn($value) => (int) $value)
            ->filter(fn($value) => $value > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function sanitizeMediaIds(array $mediaIds): array
    {
        return collect($mediaIds)
            ->filter(fn($value) => filled($value))
            ->map(fn($value) => (int) $value)
            ->filter(fn($value) => $value > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeImageFiles($images): array
    {
        if (! $images) {
            return [];
        }

        if (! is_array($images)) {
            return [$images];
        }

        return array_values(array_filter($images));
    }

    private function uploadMediaFiles(array $images, int $limit = 2): array
    {
        $mediaIds = [];

        foreach (array_slice($images, 0, $limit) as $image) {
            if ($image) {
                $mediaIds[] = $this->createMediaFromUpload($image)->id;
            }
        }

        return $mediaIds;
    }

    private function getMediaPaths(array $mediaIds): array
    {
        if (empty($mediaIds)) {
            return [];
        }

        $pathsById = Media::whereIn('id', $mediaIds)->pluck('path', 'id');

        return collect($mediaIds)
            ->map(fn($mediaId) => $pathsById[$mediaId] ?? null)
            ->filter()
            ->values()
            ->all();
    }

    private function createMediaFromUpload($image): Media
    {
        return $this->mediaService->createFromUpload($image);
    }

    private function buildCopiedProductName(string $name): string
    {
        $baseName = trim($name);

        if ($baseName === '') {
            return 'Copied Product';
        }

        return Str::limit($baseName . ' Copy', 255, '');
    }
}
