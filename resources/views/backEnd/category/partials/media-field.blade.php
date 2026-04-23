@php
    $field = $field ?? 'image';
    $label = $label ?? ucfirst($field);
    $mediaInputName = $mediaInputName ?? ($field . '_media_id');
    $fileInputId = $fileInputId ?? ('category-' . $field . '-upload');
    $selectedMediaId = (string) ($selectedMediaId ?? '');
    $currentUrl = $currentUrl ?? '';
    $currentPath = $currentPath ?? '';
    $previewClass = $field === 'icon' ? 'category-media-current-preview is-icon' : 'category-media-current-preview';
    $initialStatus = $selectedMediaId !== ''
        ? 'Selected from media library.'
        : 'Use upload or choose from library.';
@endphp

<div
    class="category-media-card"
    data-category-media-picker
    data-category-media-field="{{ $field }}"
    data-category-current-url="{{ $currentUrl }}"
    data-category-current-path="{{ $currentPath }}"
    data-category-current-label="{{ $label }}"
>
    <div class="category-media-head">
        <div>
            <h5 class="category-media-title">{{ $label }}</h5>
            <small class="category-media-note">Upload new file or pick one from the media library.</small>
        </div>
        <button
            type="button"
            class="btn btn-sm btn-outline-primary category-media-trigger"
            data-open-category-media-picker
            data-media-target="{{ $field }}"
            data-media-label="{{ $label }}"
        >
            Choose From Library
        </button>
    </div>

    <input
        type="hidden"
        name="{{ $mediaInputName }}"
        value="{{ old($mediaInputName, $selectedMediaId) }}"
        data-category-media-selected-id
    >

    <label class="category-media-dropzone" data-category-dropzone>
        <input
            type="file"
            name="{{ $field }}"
            id="{{ $fileInputId }}"
            class="@error($field) is-invalid @enderror"
            accept="image/*"
            data-category-media-upload
        >
        <div class="category-media-dropzone-icon">+</div>
        <strong>Click to upload a new file</strong>
        <span data-category-file-label>No file selected yet</span>
    </label>

    @error($field)
        <span class="invalid-feedback d-block" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror

    @error($mediaInputName)
        <span class="invalid-feedback d-block" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror

    <div class="category-media-current" data-category-media-preview>
        <img
            src="{{ $currentUrl }}"
            alt="{{ $label }}"
            class="{{ $previewClass }}"
            data-category-media-preview-image
            @if($currentUrl === '') style="display:none;" @endif
        >
        <div class="category-media-current-copy">
            <strong class="d-block text-dark" data-category-media-preview-title>
                {{ $currentUrl !== '' ? 'Current ' . $label : 'No media selected yet' }}
            </strong>
            <span data-category-media-preview-path>
                {{ $currentPath !== '' ? \Illuminate\Support\Str::limit($currentPath, 50) : 'Choose a file or select from the media library.' }}
            </span>
        </div>
    </div>

    <div class="category-media-status" data-category-media-status>
        {{ $initialStatus }}
    </div>
</div>
