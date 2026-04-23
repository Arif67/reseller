@extends('backEnd.layouts.master')
@section('title','App Setting')
@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

<link href="{{asset('public/backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />

@endsection
@section('content')
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('settings.index')}}" class="btn btn-primary rounded-pill">Manage</a>
                </div>
                <h4 class="page-title">App Setting</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    @include('backEnd.settings.partials.tab-menu')
   <div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{route('settings.store')}}" method="POST" class=row data-parsley-validate=""  enctype="multipart/form-data">
                    @csrf
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" id="name" required="">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-6 mb-3">
                        @include('backEnd.category.partials.media-field', [
                            'field' => 'white_logo',
                            'label' => 'White Logo',
                            'mediaInputName' => 'white_logo_media_id',
                            'fileInputId' => 'white_logo',
                            'selectedMediaId' => old('white_logo_media_id'),
                            'currentUrl' => '',
                            'currentPath' => '',
                        ])
                    </div>
                    <!-- col end -->
                    <div class="col-sm-6 mb-3">
                        @include('backEnd.category.partials.media-field', [
                            'field' => 'dark_logo',
                            'label' => 'Dark Logo',
                            'mediaInputName' => 'dark_logo_media_id',
                            'fileInputId' => 'dark_logo',
                            'selectedMediaId' => old('dark_logo_media_id'),
                            'currentUrl' => '',
                            'currentPath' => '',
                        ])
                    </div>
                    <!-- col end -->
                    <div class="col-sm-6 mb-3">
                        @include('backEnd.category.partials.media-field', [
                            'field' => 'favicon',
                            'label' => 'Favicon Logo',
                            'mediaInputName' => 'favicon_media_id',
                            'fileInputId' => 'favicon',
                            'selectedMediaId' => old('favicon_media_id'),
                            'currentUrl' => '',
                            'currentPath' => '',
                        ])
                    </div>
                    <!-- col end -->
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="status" class="d-block">Status</label>
                            <label class="switch">
                              <input type="checkbox" value="1" name="status" checked>
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
                    <div class="col-12">
                        <h5 class="mt-3">Vault Upload Settings</h5>
                    </div>
                    <div class="col-sm-4 mb-3">
                        <div class="form-group">
                            <label for="vault_upload_enabled" class="d-block">Vault Upload</label>
                            <label class="switch">
                              <input type="checkbox" value="1" name="vault_upload_enabled">
                              <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-8 mb-3">
                        <div class="form-group">
                            <label for="vault_endpoint" class="form-label">Vault Endpoint</label>
                            <input type="text" class="form-control" name="vault_endpoint" id="vault_endpoint"
                                placeholder="https://iws-vault-service.3bitsmind.com/upload">
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="vault_access_key_id" class="form-label">Vault Access Key ID</label>
                            <input type="text" class="form-control" name="vault_access_key_id" id="vault_access_key_id">
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="vault_secret_key" class="form-label">Vault Secret Key</label>
                            <input type="text" class="form-control" name="vault_secret_key" id="vault_secret_key">
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="vault_public_base_url" class="form-label">Vault CDN/Base URL</label>
                            <input type="text" class="form-control" name="vault_public_base_url" id="vault_public_base_url"
                                placeholder="https://iws-cdn-service.3bitsmind.com/VoAXMhcc3SV">
                        </div>
                    </div>
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

<script src="{{asset('public/backEnd/')}}/assets/libs//summernote/summernote-lite.min.js"></script>
<script>
    $(".summernote").summernote({
        placeholder: "Enter Your Text Here",
    });
</script>
@endsection
