<?php

namespace App\Services\AppService;

use App\Models\Attribute;
use App\Models\Product;
use App\Models\ProductVariable;
use App\Models\Value;
use Illuminate\Support\Collection;

class ProductAttributeService
{
    private const LEGACY_ATTRIBUTE_COLUMN_MAP = [
        'color' => 'color',
        'size' => 'size',
        'model' => 'model',
        'weight' => 'weight',
    ];

    public function getActiveAttributes(): Collection
    {
        return Attribute::query()
            ->where('status', 1)
            ->with([
                'values' => fn ($query) => $query
                    ->where('status', 1)
                    ->orderBy('title'),
            ])
            ->orderBy('id')
            ->get();
    }

    public function sanitizeSelectedValueIds(array $selectedValueIds): array
    {
        return collect($selectedValueIds)
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value) => (int) $value)
            ->filter(fn ($value) => $value > 0)
            ->unique()
            ->values()
            ->all();
    }

    public function fillLegacyColumns(ProductVariable $variable, array $selectedValueIds, array $legacySelections = []): void
    {
        $resolvedSelections = $this->resolveLegacySelectionsFromValueIds($selectedValueIds, $legacySelections);

        foreach (self::LEGACY_ATTRIBUTE_COLUMN_MAP as $attributeKey => $column) {
            $variable->{$column} = $resolvedSelections[$attributeKey] ?? null;
        }
    }

    public function syncSelectedValues(ProductVariable $variable, array $selectedValueIds): void
    {
        $selectedValueIds = $this->sanitizeSelectedValueIds($selectedValueIds);

        if (empty($selectedValueIds)) {
            $variable->selectedValues()->detach();
            return;
        }

        $values = Value::query()
            ->whereIn('id', $selectedValueIds)
            ->get(['id', 'attribute_id']);

        $syncPayload = $values
            ->mapWithKeys(fn (Value $value) => [
                $value->id => ['attribute_id' => $value->attribute_id],
            ])
            ->all();

        $variable->selectedValues()->sync($syncPayload);
    }

    public function getSelectedValueMap(ProductVariable $variable): array
    {
        $selectedValues = $variable->relationLoaded('selectedValues')
            ? $variable->selectedValues
            : $variable->selectedValues()->with('attribute')->get();

        if ($selectedValues->isNotEmpty()) {
            return $selectedValues
                ->pluck('id', 'attribute_id')
                ->map(fn ($value) => (int) $value)
                ->all();
        }

        $legacyAttributeMap = $this->getLegacyAttributeMap();
        $legacyValueLookup = Value::query()
            ->whereIn('attribute_id', $legacyAttributeMap->keys())
            ->get(['id', 'attribute_id', 'title'])
            ->groupBy('attribute_id')
            ->map(fn (Collection $values) => $values->keyBy(fn (Value $value) => mb_strtolower(trim($value->title))));

        $selectedMap = [];
        foreach ($legacyAttributeMap as $attributeId => $attributeKey) {
            $legacyValue = $variable->{self::LEGACY_ATTRIBUTE_COLUMN_MAP[$attributeKey]} ?? null;
            if (! $legacyValue) {
                continue;
            }

            $match = $legacyValueLookup[$attributeId][mb_strtolower(trim($legacyValue))] ?? null;
            if ($match) {
                $selectedMap[$attributeId] = (int) $match->id;
            }
        }

        return $selectedMap;
    }

    public function buildProductAttributeGroups(Product $product): Collection
    {
        $product->loadMissing('variables.selectedValues.attribute');

        $attributeLookup = $this->getActiveAttributes()->keyBy('id');
        $groups = collect();

        foreach ($product->variables as $variable) {
            foreach ($variable->selectedValues as $selectedValue) {
                $attribute = $selectedValue->attribute;
                if (! $attribute) {
                    continue;
                }

                $group = $groups->get($attribute->id, [
                    'attribute_id' => $attribute->id,
                    'title' => $attribute->title,
                    'options' => collect(),
                ]);

                if (! $group['options']->has($selectedValue->id)) {
                    $group['options']->put($selectedValue->id, [
                        'id' => $selectedValue->id,
                        'title' => $selectedValue->title,
                    ]);
                }

                $groups->put($attribute->id, $group);
            }
        }

        $legacyAttributeMap = $this->getLegacyAttributeMap()->flip();
        $legacyValueLookup = Value::query()
            ->whereIn('attribute_id', $legacyAttributeMap->values())
            ->get(['id', 'attribute_id', 'title'])
            ->groupBy('attribute_id')
            ->map(fn (Collection $values) => $values->keyBy(fn (Value $value) => mb_strtolower(trim($value->title))));

        foreach (self::LEGACY_ATTRIBUTE_COLUMN_MAP as $attributeKey => $column) {
            $attributeId = $legacyAttributeMap[$attributeKey] ?? null;
            if (! $attributeId || ! isset($attributeLookup[$attributeId])) {
                continue;
            }

            $attribute = $attributeLookup[$attributeId];
            $group = $groups->get($attributeId, [
                'attribute_id' => $attribute->id,
                'title' => $attribute->title,
                'options' => collect(),
            ]);

            foreach ($product->variables as $variable) {
                $legacyValue = $variable->{$column};
                if (! $legacyValue) {
                    continue;
                }

                $matchedValue = $legacyValueLookup[$attributeId][mb_strtolower(trim($legacyValue))] ?? null;
                $optionId = $matchedValue?->id;
                $optionKey = $optionId ?: 'legacy-' . md5($attributeId . '-' . $legacyValue);

                if (! $group['options']->has($optionKey)) {
                    $group['options']->put($optionKey, [
                        'id' => $optionId,
                        'title' => $legacyValue,
                    ]);
                }
            }

            if ($group['options']->isNotEmpty()) {
                $groups->put($attributeId, $group);
            }
        }

        return $groups
            ->sortKeys()
            ->map(function (array $group) {
                $group['options'] = $group['options']->values();
                return $group;
            })
            ->values();
    }

    public function findVariantForProduct(int $productId, array $selectedValueIds = [], array $legacySelections = []): ?ProductVariable
    {
        $selectedValueIds = $this->sanitizeSelectedValueIds($selectedValueIds);

        if (! empty($selectedValueIds)) {
            $product = ProductVariable::query()
                ->with('selectedValues.attribute')
                ->where('product_id', $productId)
                ->availableForReseller()
                ->whereHas('selectedValues', function ($query) use ($selectedValueIds) {
                    $query->whereIn('values.id', $selectedValueIds);
                }, '=', count($selectedValueIds))
                ->withCount('selectedValues')
                ->having('selected_values_count', count($selectedValueIds))
                ->first();

            if ($product) {
                return $product;
            }
        }

        $resolvedSelections = $this->resolveLegacySelectionsFromValueIds($selectedValueIds, $legacySelections);

        return ProductVariable::query()
            ->with('selectedValues.attribute')
            ->where('product_id', $productId)
            ->availableForReseller()
            ->when(! empty($resolvedSelections['color']), fn ($query) => $query->where('color', $resolvedSelections['color']))
            ->when(! empty($resolvedSelections['size']), fn ($query) => $query->where('size', $resolvedSelections['size']))
            ->when(! empty($resolvedSelections['model']), fn ($query) => $query->where('model', $resolvedSelections['model']))
            ->when(! empty($resolvedSelections['weight']), fn ($query) => $query->where('weight', $resolvedSelections['weight']))
            ->first();
    }

    public function resolveLegacySelectionsFromValueIds(array $selectedValueIds, array $legacySelections = []): array
    {
        $resolvedSelections = [
            'color' => $legacySelections['color'] ?? null,
            'size' => $legacySelections['size'] ?? null,
            'model' => $legacySelections['model'] ?? null,
            'weight' => $legacySelections['weight'] ?? null,
        ];

        $values = Value::query()
            ->with('attribute:id,title')
            ->whereIn('id', $this->sanitizeSelectedValueIds($selectedValueIds))
            ->get(['id', 'attribute_id', 'title']);

        foreach ($values as $value) {
            $attributeKey = $this->normalizeAttributeKey($value->attribute?->title);
            if ($attributeKey && array_key_exists($attributeKey, $resolvedSelections)) {
                $resolvedSelections[$attributeKey] = $value->title;
            }
        }

        return $resolvedSelections;
    }

    public function summarizeSelections(array $selectedValueIds, array $legacySelections = []): array
    {
        $values = Value::query()
            ->with('attribute:id,title')
            ->whereIn('id', $this->sanitizeSelectedValueIds($selectedValueIds))
            ->get(['id', 'attribute_id', 'title']);

        $summary = $values
            ->filter(fn (Value $value) => filled($value->title) && filled(optional($value->attribute)->title))
            ->map(fn (Value $value) => [
                'attribute' => $value->attribute->title,
                'value' => $value->title,
            ])
            ->values()
            ->all();

        if (! empty($summary)) {
            return $summary;
        }

        $resolvedSelections = $this->resolveLegacySelectionsFromValueIds([], $legacySelections);

        return collect($resolvedSelections)
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value, $attribute) => [
                'attribute' => ucfirst($attribute),
                'value' => $value,
            ])
            ->values()
            ->all();
    }

    private function getLegacyAttributeMap(): Collection
    {
        return Attribute::query()
            ->whereIn('title', ['Color', 'Size', 'Model', 'Weight'])
            ->get(['id', 'title'])
            ->mapWithKeys(function (Attribute $attribute) {
                $key = $this->normalizeAttributeKey($attribute->title);
                return $key ? [$attribute->id => $key] : [];
            });
    }

    private function normalizeAttributeKey(?string $title): ?string
    {
        $normalized = mb_strtolower(trim((string) $title));

        return match ($normalized) {
            'color' => 'color',
            'size' => 'size',
            'model' => 'model',
            'weight' => 'weight',
            default => null,
        };
    }
}
