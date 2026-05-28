@extends('backEnd.layouts.master')
@section('title','Banner Edit')
@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('banners.index')}}" class="btn btn-primary rounded-pill">Manage</a>
                </div>
                <h4 class="page-title">Banner Edit</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{route('banners.update')}}" method="POST" class=row data-parsley-validate=""  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" value="{{$edit_data->id}}" name="id">

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="link" class="form-label">link *</label>
                            <input type="text" class="form-control @error('link') is-invalid @enderror" name="link" value="{{$edit_data->link}}" id="link" required="">
                            @error('link')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="category_id" class="form-label">Banner Category</label>
                             <select class="form-control select2-multiple @error('link') is-invalid @enderror" name="category_id" data-toggle="select2"  data-placeholder="Choose ...">
                                <optgroup>
                                    <option value="">Select..</option>
                                    @foreach($categories as $value)
                                    <option  value="{{$value->id}}" @if($edit_data->category_id==$value->id)selected @endif>{{$value->name}}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('category_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                    <div class="col-sm-12 mb-2">
                        <div class="alert alert-info border-0 p-3 mb-0" style="background:#f0f8ff;border-left:4px solid #0d6efd !important;border-radius:8px;">
                            <h6 class="mb-2 fw-bold" style="color:#0d47a1;">
                                <i class="mdi mdi-image-size-select-actual me-1"></i> Recommended Image Size
                            </h6>
                            <div class="row g-2">
                                <div class="col-sm-4">
                                    <div class="p-2 rounded text-center" style="background:#e3f2fd;border:1px solid #90caf9;">
                                        <div class="fw-bold text-primary" style="font-size:13px;">Layout 1</div>
                                        <div style="font-size:18px;font-weight:800;color:#1565c0;">1920 × 680</div>
                                        <small class="text-muted">Full width slider</small>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="p-2 rounded text-center" style="background:#e8f5e9;border:1px solid #a5d6a7;">
                                        <div class="fw-bold" style="font-size:13px;color:#2e7d32;">Layout 2</div>
                                        <div style="font-size:18px;font-weight:800;color:#1b5e20;">1280 × 500</div>
                                        <small class="text-muted">Left + 2 side banners</small>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="p-2 rounded text-center" style="background:#fff3e0;border:1px solid #ffcc80;">
                                        <div class="fw-bold" style="font-size:13px;color:#e65100;">Layout 3 (Daraz)</div>
                                        <div style="font-size:18px;font-weight:800;color:#bf360c;">1976 × 688</div>
                                        <small class="text-muted">10 col slider + app panel</small>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2" style="font-size:12px;color:#555;">
                                <i class="mdi mdi-information-outline"></i>
                                Minimum: <strong>1280×450 px</strong> &nbsp;|&nbsp;
                                Format: <strong>JPG, PNG, WebP, AVIF</strong> &nbsp;|&nbsp;
                                Max size: <strong>4 MB</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-3">
                        @include('backEnd.category.partials.media-field', [
                            'field' => 'image',
                            'label' => 'Banner Image',
                            'selectedMediaId' => old('image_media_id', $selectedImageMediaId ?? null),
                            'currentUrl' => $edit_data->image ? asset(ltrim($edit_data->image, '/')) : '',
                            'currentPath' => $edit_data->image ?? '',
                        ])
                    </div>
                    <!-- col end -->
                    <div class="col-sm-12 mb-3">
                        <div class="form-group">
                            <label for="status" class="d-block">Status</label>
                            <label class="switch">
                              <input type="checkbox" value="1" name="status" @if($edit_data->status==1)checked @endif>
                              <span class="slider round"></span>
                            </label>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                    <div>
                        <input type="submit" class="btn btn-success" value="Submit">
                    </div>

                </form>

                @include('backEnd.category.partials.media-picker-modal')

            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
   </div>
</div>
@endsection


@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
@endsection
