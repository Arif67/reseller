@php
    $selectedIds = collect($selectedIds ?? [])
        ->filter(fn ($value) => filled($value))
        ->map(fn ($value) => (string) $value)
        ->unique()
        ->values()
        ->all();
    $mediaLookup = collect($mediaLibrary ?? [])->keyBy(fn ($media) => (string) $media->id);
    $inputName = $inputName ?? 'selected_media_ids';
    $title = $title ?? 'Select Product Media';
@endphp

<div
    class="product-media-card"
    data-product-media-picker
    data-product-media-target="{{ $inputName }}"
    data-product-media-label="{{ $title }}"
>
    <div class="product-media-card-head d-flex justify-content-between align-items-start gap-2 mb-2">
        <div>
            <h5 class="product-media-title mb-1">{{ $title }}</h5>
            <small class="product-media-note">Category gallery er moto library picker use korun.</small>
        </div>

        <button
            type="button"
            class="btn btn-sm btn-outline-primary"
            data-open-product-media-picker
            data-media-target="{{ $inputName }}"
            data-media-label="{{ $title }}"
        >
            Choose From Library
        </button>
    </div>

    <div class="product-media-summary">
        <div class="product-media-summary-text">
            <strong data-product-media-summary-title>
                {{ count($selectedIds) > 0 ? count($selectedIds) . ' media selected' : 'No media selected yet' }}
            </strong>
            <span data-product-media-summary-copy>
                {{ count($selectedIds) > 0 ? 'Selection will be saved with the product.' : 'Choose one or more media items from the library.' }}
            </span>
        </div>
        <button
            type="button"
            class="btn btn-sm btn-light"
            data-product-media-clear
            @disabled(count($selectedIds) === 0)
        >
            Clear
        </button>
    </div>

    <div class="product-media-selected-grid mt-3" data-product-media-selected-grid>
        @forelse($selectedIds as $id)
            @php $mediaItem = $mediaLookup->get((string) $id); @endphp
            @if($mediaItem)
                @php($mediaUrl = \Illuminate\Support\Str::startsWith($mediaItem->path, ['http://', 'https://']) ? $mediaItem->path : asset($mediaItem->path))
                <div
                    class="product-media-selected-item"
                    data-product-media-selected-item
                    data-media-id="{{ $mediaItem->id }}"
                    data-media-path="{{ $mediaUrl }}"
                    data-media-name="{{ $mediaItem->name }}"
                    data-media-alt="{{ $mediaItem->alt ?? $mediaItem->name }}"
                >
                    <img src="{{ $mediaUrl }}" alt="{{ $mediaItem->alt ?? $mediaItem->name }}">
                    <button type="button" class="product-media-remove" data-product-media-remove data-media-id="{{ $mediaItem->id }}" aria-label="Remove media">&times;</button>
                </div>
            @endif
        @empty
            <div class="alert alert-light mb-0" data-product-media-empty>No media selected yet.</div>
        @endforelse
    </div>

    <div data-product-media-hidden-inputs>
        @foreach($selectedIds as $id)
            <input type="hidden" name="{{ $inputName }}[]" value="{{ $id }}" data-product-media-hidden-input>
        @endforeach
    </div>
</div>
