@extends('backEnd.layouts.master')
@section('title','Pixels Edit')
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
                    <a href="{{route('pixels.index')}}" class="btn btn-primary rounded-pill">Manage</a>
                </div>
                <h4 class="page-title">Pixels Edit</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{route('pixels.update')}}" method="POST" class=row data-parsley-validate=""  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" value="{{$edit_data->id}}" name="id">
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="provider" class="form-label">Provider *</label>
                            <select class="form-control @error('provider') is-invalid @enderror" name="provider" id="provider" required>
                                <option value="facebook" {{ ($edit_data->provider ?? 'facebook') === 'facebook' ? 'selected' : '' }}>Facebook / Meta</option>
                                <option value="tiktok" {{ ($edit_data->provider ?? '') === 'tiktok' ? 'selected' : '' }}>TikTok</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $edit_data->name ?? '' }}" id="name" placeholder="Default Pixel">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="code" class="form-label">Pixel ID / Code *</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ $edit_data->code}}" id="code" required="">
                            @error('code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="access_token" class="form-label">Access Token / Events API Token</label>
                            <input type="text" class="form-control @error('access_token') is-invalid @enderror" name="access_token" value="{{ $edit_data->access_token ?? '' }}" id="access_token" placeholder="Required for Meta CAPI or TikTok Events API">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="test_event_code" class="form-label">Test Event Code</label>
                            <input type="text" class="form-control @error('test_event_code') is-invalid @enderror" name="test_event_code" value="{{ $edit_data->test_event_code ?? '' }}" id="test_event_code" placeholder="Optional provider test code">
                        </div>
                    </div>
                    <!-- col-end -->
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
