@extends('backEnd.layouts.master')
@section('title', 'Edit Promo Strip')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('promo_strip.index') }}" class="btn btn-primary rounded-pill">Manage</a>
                </div>
                <h4 class="page-title">Edit Promo Strip</h4>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('promo_strip.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $item->id }}">

                        <div class="mb-3">
                            <label class="form-label">Title <small class="text-muted">(optional)</small></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $item->title) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link <span class="text-danger">*</span></label>
                            <input type="text" name="link" class="form-control @error('link') is-invalid @enderror"
                                   value="{{ old('link', $item->link) }}" required>
                            @error('link')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Countdown End Date &amp; Time <small class="text-muted">(optional)</small></label>
                            <input type="datetime-local" name="countdown_end"
                                   class="form-control @error('countdown_end') is-invalid @enderror"
                                   value="{{ old('countdown_end', $item->countdown_end ? $item->countdown_end->format('Y-m-d\TH:i') : '') }}">
                            @error('countdown_end')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Background Color</label>
                            <input type="color" name="bg_color" class="form-control form-control-color"
                                   value="{{ old('bg_color', $item->bg_color) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control"
                                   value="{{ old('sort_order', $item->sort_order) }}" min="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Banner Image <small class="text-muted">(leave empty to keep current)</small></label>
                            @if($item->image)
                                <div class="mb-2">
                                    <img src="{{ asset($item->image) }}" alt="" height="60"
                                         style="border-radius:6px;background:{{ $item->bg_color }};object-fit:cover;max-width:200px;">
                                    <small class="d-block text-muted mt-1">Current image</small>
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                                   accept="image/jpeg,image/png,image/webp,image/gif">
                            @error('image')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label d-block">Status</label>
                            <label class="switch">
                                <input type="checkbox" name="status" value="1" @if($item->status) checked @endif>
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-success">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
