@php
    $row = $row ?? null;
@endphp

<div class="col-sm-12">
    <div class="form-group mb-3">
        <label for="name" class="form-label">Name *</label>
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

<div class="col-sm-12">
    <div class="form-group mb-3">
        <label for="serial" class="form-label">Serial</label>
        <input
            type="number"
            min="0"
            class="form-control @error('serial') is-invalid @enderror"
            name="serial"
            value="{{ old('serial', $row->serial ?? 0) }}"
            id="serial"
        >
        @error('serial')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

<div class="col-sm-12">
    @include('backEnd.category.partials.media-field', [
        'field' => 'image',
        'label' => 'Category Image',
        'selectedMediaId' => old('image_media_id', $selectedImageMediaId ?? null),
        'currentUrl' => $row?->image_url ?? '',
        'currentPath' => $row?->image ?? '',
    ])
</div>

<div class="col-sm-12">
    @include('backEnd.category.partials.media-field', [
        'field' => 'icon',
        'label' => 'Category Icon',
        'selectedMediaId' => old('icon_media_id', $selectedIconMediaId ?? null),
        'currentUrl' => $row?->icon_url ?? '',
        'currentPath' => $row?->icon ?? '',
    ])
</div>

<div class="col-sm-12">
    <div class="form-group mb-3">
        <label for="meta_title" class="form-label">Meta Title</label>
        <input
            type="text"
            class="form-control @error('meta_title') is-invalid @enderror"
            name="meta_title"
            value="{{ old('meta_title', $row->meta_title ?? '') }}"
            id="meta_title"
        >
        @error('meta_title')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

<div class="col-sm-12">
    <div class="form-group mb-3">
        <label for="meta_description" class="form-label">Meta Description</label>
        <textarea
            class="summernote form-control @error('meta_description') is-invalid @enderror"
            name="meta_description"
            rows="6"
            id="meta_description"
        >{!! old('meta_description', $row->meta_description ?? '') !!}</textarea>
        @error('meta_description')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

<div class="col-12 mb-3">
    <div class="form-group">
        <label>
            <input type="hidden" name="featured" value="0">
            <input type="checkbox" name="featured" value="1" @checked(old('featured', $row->featured ?? 0))>
            Featured Category
        </label>
    </div>
</div>

<div class="col mb-3">
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

<div class="col mb-3">
    <div class="form-group">
        <label for="front_view" class="d-block">Front View</label>
        <input type="hidden" name="front_view" value="0">
        <label class="switch">
            <input type="checkbox" value="1" name="front_view" @checked((int) old('front_view', $row->front_view ?? 0) === 1)>
            <span class="slider round"></span>
        </label>
        @error('front_view')
            <span class="invalid-feedback d-block" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

@include('backEnd.category.partials.media-picker-modal')

<div class="col mb-3">
    <div class="form-group">
        <label for="banner_image" class="d-block">Banner Image</label>
        <input type="hidden" name="banner_image" value="0">
        <label class="switch">
            <input type="checkbox" value="1" name="banner_image" @checked((int) old('banner_image', $row->banner_image ?? 0) === 1)>
            <span class="slider round"></span>
        </label>
        @error('banner_image')
            <span class="invalid-feedback d-block" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
