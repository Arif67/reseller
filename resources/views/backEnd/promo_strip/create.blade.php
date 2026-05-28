@extends('backEnd.layouts.master')
@section('title', 'Add Promo Strip')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('promo_strip.index') }}" class="btn btn-primary rounded-pill">Manage</a>
                </div>
                <h4 class="page-title">Add Promo Strip</h4>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('promo_strip.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Title <small class="text-muted">(optional label, e.g. "EID SALE 2026")</small></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="EID Sale 2026">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link <span class="text-danger">*</span></label>
                            <input type="text" name="link" class="form-control @error('link') is-invalid @enderror"
                                   value="{{ old('link', '#') }}" required>
                            @error('link')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Countdown End Date &amp; Time <small class="text-muted">(optional — shows timer overlay)</small></label>
                            <input type="datetime-local" name="countdown_end"
                                   class="form-control @error('countdown_end') is-invalid @enderror"
                                   value="{{ old('countdown_end') }}">
                            @error('countdown_end')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Background Color <small class="text-muted">(shown while image loads)</small></label>
                            <input type="color" name="bg_color" class="form-control form-control-color" value="{{ old('bg_color', '#ffffff') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                        </div>

                        <div class="alert alert-info border-0 p-3 mb-3" style="background:#f0f8ff;border-left:4px solid #0d6efd !important;border-radius:8px;">
                            <h6 class="mb-1 fw-bold" style="color:#0d47a1;"><i class="mdi mdi-image-size-select-actual me-1"></i> Recommended Image Size</h6>
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="p-2 rounded text-center" style="background:#e3f2fd;border:1px solid #90caf9;">
                                        <div class="fw-bold text-primary" style="font-size:13px;">Full-width Strip</div>
                                        <div style="font-size:18px;font-weight:800;color:#1565c0;">1188 × 140 px</div>
                                        <small class="text-muted">Single banner across full width</small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-2 rounded text-center" style="background:#e8f5e9;border:1px solid #a5d6a7;">
                                        <div class="fw-bold" style="font-size:13px;color:#2e7d32;">Card Strip (multiple)</div>
                                        <div style="font-size:18px;font-weight:800;color:#1b5e20;">600 × 140 px</div>
                                        <small class="text-muted">Side-by-side cards in strip</small>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2" style="font-size:12px;color:#555;">
                                <i class="mdi mdi-information-outline"></i>
                                Format: <strong>JPG, PNG, WebP, GIF</strong> &nbsp;|&nbsp; Max size: <strong>4 MB</strong>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Banner Image</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                                   accept="image/jpeg,image/png,image/webp,image/gif">
                            @error('image')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label d-block">Status</label>
                            <label class="switch">
                                <input type="checkbox" name="status" value="1" checked>
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-success">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
