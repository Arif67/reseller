 @extends('backEnd.layouts.master')
 @section('title', 'App Setting')
 @section('css')
     <link href="{{ asset('public/backEnd') }}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
     <link href="{{ asset('public/backEnd') }}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet"
         type="text/css" />
 @endsection
 @section('content')
     <div class="container-fluid">

         <!-- start page title -->
         <div class="row">
             <div class="col-12">
                 <div class="page-title-box">
                     <div class="page-title-right">
                    <a href="{{ route('settings.index') }}" class="btn btn-primary rounded-pill d-none">Manage</a>
                     </div>
                    <h4 class="page-title">App Setting</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-pills flex-wrap gap-2 mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('settings.index') }}">General Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('settings.index') }}">Social Media</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('settings.index') }}">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('settings.index') }}">Create Page</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('settings.index') }}">Shipping Charge</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('settings.index') }}">Order Status</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- end page title -->
         <div class="row">
             <div class="col-lg-12">
                 <div class="card">
                     <div class="card-body">
                         <form action="{{ route('settings.update') }}" method="POST" class=row data-parsley-validate=""
                             enctype="multipart/form-data">
                             @csrf
                             <input type="hidden" name="id" value="{{ $edit_data->id }}">
                             <div class="col-sm-6">
                                 <div class="form-group mb-3">
                                     <label for="name" class="form-label">Name *</label>
                                     <input type="text" class="form-control @error('name') is-invalid @enderror"
                                         name="name" value="{{ $edit_data->name }}" id="name" required="">
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
                                     'selectedMediaId' => old('white_logo_media_id', $selectedWhiteLogoMediaId ?? null),
                                     'currentUrl' => $edit_data->white_logo ? asset(ltrim($edit_data->white_logo, '/')) : '',
                                     'currentPath' => $edit_data->white_logo ?? '',
                                 ])
                             </div>
                             <!-- col end -->
                             <div class="col-sm-6 mb-3">
                                 @include('backEnd.category.partials.media-field', [
                                     'field' => 'dark_logo',
                                     'label' => 'Dark Logo',
                                     'mediaInputName' => 'dark_logo_media_id',
                                     'fileInputId' => 'dark_logo',
                                     'selectedMediaId' => old('dark_logo_media_id', $selectedDarkLogoMediaId ?? null),
                                     'currentUrl' => $edit_data->dark_logo ? asset(ltrim($edit_data->dark_logo, '/')) : '',
                                     'currentPath' => $edit_data->dark_logo ?? '',
                                 ])
                             </div>
                             <!-- col end -->

                             <div class="col-sm-6 mb-3">
                                 @include('backEnd.category.partials.media-field', [
                                     'field' => 'favicon',
                                     'label' => 'Favicon Logo',
                                     'mediaInputName' => 'favicon_media_id',
                                     'fileInputId' => 'favicon',
                                     'selectedMediaId' => old('favicon_media_id', $selectedFaviconMediaId ?? null),
                                     'currentUrl' => $edit_data->favicon ? asset(ltrim($edit_data->favicon, '/')) : '',
                                     'currentPath' => $edit_data->favicon ?? '',
                                 ])
                             </div>
                             <!-- col end -->
                             <div class="col-sm-12">
                                 <div class="form-group mb-3">
                                     <label for="description" class="form-label">Footer About *</label>
                                     <input type="text" class="form-control @error('name') is-invalid @enderror"
                                         name="description" value="{{ $edit_data->description }}" id="description"
                                         required="">
                                     @error('description')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>
                             <div class="col-sm-12">
                                 <div class="form-group mb-3">
                                     <label for="copyright" class="form-label">Copyright *</label>
                                     <input type="text" class="form-control @error('name') is-invalid @enderror"
                                         name="copyright" value="{{ $edit_data->copyright }}" id="copyright"
                                         required="">
                                     @error('copyright')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>

                             <div class="col-sm-12">
                                 <div class="form-group mb-3">
                                     <label for="pickup_title" class="form-label">details page title *</label>
                                     <input type="text" class="form-control @error('name') is-invalid @enderror"
                                         name="pickup_title" value="{{ $edit_data->pickup_title }}" id="copyright">
                                     @error('pickup_title')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                     @enderror
                                 </div>
                             </div>

                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="pickup_description" class="form-label">details page description *</label>
                                    <input type="text"
                                        class="form-control @error('pickup_description') is-invalid @enderror"
                                        name="pickup_description" value="{{ $edit_data->pickup_description }}"
                                        id="pickup_description">
                                    @error('pickup_description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <h5 class="mt-3">Vault Upload Settings</h5>
                            </div>
                            <div class="col-sm-4 mb-3">
                                <div class="form-group">
                                    <label for="vault_upload_enabled" class="d-block">Vault Upload</label>
                                    <label class="switch">
                                        <input type="checkbox" value="1" name="vault_upload_enabled"
                                            @if ($edit_data->vault_upload_enabled) checked @endif>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-8 mb-3">
                                <div class="form-group">
                                    <label for="vault_endpoint" class="form-label">Vault Endpoint</label>
                                    <input type="text" class="form-control" name="vault_endpoint"
                                        value="{{ $edit_data->vault_endpoint }}" id="vault_endpoint"
                                        placeholder="https://iws-vault-service.3bitsmind.com/upload">
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="vault_access_key_id" class="form-label">Vault Access Key ID</label>
                                    <input type="text" class="form-control" name="vault_access_key_id"
                                        value="{{ $edit_data->vault_access_key_id }}" id="vault_access_key_id">
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="vault_secret_key" class="form-label">Vault Secret Key</label>
                                    <input type="text" class="form-control" name="vault_secret_key"
                                        value="{{ $edit_data->vault_secret_key }}" id="vault_secret_key">
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="vault_public_base_url" class="form-label">Vault CDN/Base URL</label>
                                    <input type="text" class="form-control" name="vault_public_base_url"
                                        value="{{ $edit_data->vault_public_base_url }}" id="vault_public_base_url"
                                        placeholder="https://iws-cdn-service.3bitsmind.com/VoAXMhcc3SV">
                                </div>
                            </div>

















                            <div class="col-12">
                                <h5 class="mt-3">App Download Settings <small class="text-muted fs-12">(Used in Hero Slider Layout 3)</small></h5>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="android_app_link" class="form-label">Google Play Store Link</label>
                                    <input type="url" class="form-control" name="android_app_link"
                                        value="{{ $edit_data->android_app_link }}" id="android_app_link"
                                        placeholder="https://play.google.com/store/apps/details?id=...">
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="ios_app_link" class="form-label">Apple App Store Link</label>
                                    <input type="url" class="form-control" name="ios_app_link"
                                        value="{{ $edit_data->ios_app_link }}" id="ios_app_link"
                                        placeholder="https://apps.apple.com/app/...">
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                @include('backEnd.category.partials.media-field', [
                                    'field' => 'app_qr_code',
                                    'label' => 'App QR Code Image',
                                    'mediaInputName' => 'app_qr_code_media_id',
                                    'fileInputId' => 'app_qr_code',
                                    'selectedMediaId' => old('app_qr_code_media_id', null),
                                    'currentUrl' => $edit_data->app_qr_code ? asset(ltrim($edit_data->app_qr_code, '/')) : '',
                                    'currentPath' => $edit_data->app_qr_code ?? '',
                                ])
                            </div>

                             <div class="col-sm-6 mb-3">
                                 <div class="form-group">
                                     <label for="status" class="d-block">Status</label>
                                     <label class="switch">
                                         <input type="checkbox" value="1" name="status"
                                             @if ($edit_data->status == 1) checked @endif>
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
     <script src="{{ asset('public/backEnd/') }}/assets/libs/parsleyjs/parsley.min.js"></script>
     <script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-validation.init.js"></script>
     <script src="{{ asset('public/backEnd/') }}/assets/libs/select2/js/select2.min.js"></script>
     <script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-advanced.init.js"></script>
     <!-- Plugins js -->
     <script src="{{ asset('public/backEnd/') }}/assets/libs//summernote/summernote-lite.min.js"></script>
     <script>
         $(".summernote").summernote({
             placeholder: "Enter Your Text Here",
         });
     </script>
 @endsection
