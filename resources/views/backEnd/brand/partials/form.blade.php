@php
    $row = $row ?? null;
@endphp

<div class="col-sm-12">
    <div class="form-group mb-3">
        <label for="name" class="form-label">Brand Name *</label>
        <input
            type="text"
            class="form-control @error('name') is-invalid @enderror"
            name="name"
            value="{{ old('name', $row->name ?? '') }}"
            id="name"
            required
        >
        @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

<div class="col-sm-12 mb-3">
    @include('backEnd.category.partials.media-field', [
        'field' => 'image',
        'label' => 'Brand Image',
        'selectedMediaId' => old('image_media_id', $selectedImageMediaId ?? null),
        'currentUrl' => $row?->image_url ?? '',
        'currentPath' => $row?->image ?? '',
    ])
</div>

@include('backEnd.category.partials.media-picker-modal')

<div class="col-sm-12 mb-3">
    <div class="form-group">
        <label for="status" class="d-block">Status</label>
        <input type="hidden" name="status" value="0">
        <label class="switch">
            <input type="checkbox" value="1" name="status" @checked((int) old('status', $row->status ?? 1) === 1)>
            <span class="slider round"></span>
        </label>
        @error('status')
            <span class="invalid-feedback d-block" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
