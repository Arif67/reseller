@extends('backEnd.layouts.master')
@section('title','Pixels Configure')
@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd')}}/assets/css/switchery.min.css" rel="stylesheet" type="text/css" />
<style>
    .pixel-config-card {
        border: 1px solid #e9edf4;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    }

    .pixel-config-head {
        padding: 18px 22px;
        color: #fff;
    }

    .pixel-config-head h5,
    .pixel-config-head p {
        margin: 0;
        color: inherit;
    }

    .pixel-config-head p {
        opacity: 0.9;
        margin-top: 6px;
        font-size: 13px;
    }

    .pixel-config-head.facebook {
        background: linear-gradient(135deg, #1877f2, #0f4fbf);
    }

    .pixel-config-head.tiktok {
        background: linear-gradient(135deg, #111111, #ff0050 70%, #00f2ea);
    }

    .pixel-config-body {
        padding: 24px 22px 22px;
        background: #fff;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Pixels Configure</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="pixel-config-card">
                <div class="pixel-config-head facebook">
                    <h5>Facebook / Meta Pixel</h5>
                    <p>Browser Pixel + Conversions API configuration</p>
                </div>
                <div class="pixel-config-body">
                    <form action="{{route('pixels.store')}}" method="POST" class="row" data-parsley-validate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="provider" value="facebook">
                        <input type="hidden" name="pixel_id" value="{{ $facebookPixel?->id }}">

                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="facebook_name" class="form-label">Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="name"
                                    id="facebook_name"
                                    value="{{ old('provider') === 'facebook' ? old('name') : ($facebookPixel?->name ?? 'Facebook Pixel') }}"
                                    placeholder="Facebook Pixel">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="facebook_code" class="form-label">Pixel ID *</label>
                                <input
                                    type="text"
                                    class="form-control @error('code'){{ old('provider', 'facebook') === 'facebook' ? ' is-invalid' : '' }}@enderror"
                                    name="code"
                                    id="facebook_code"
                                    value="{{ old('provider') === 'facebook' ? old('code') : ($facebookPixel?->code ?? '') }}"
                                    required>
                                @if(old('provider', 'facebook') === 'facebook')
                                    @error('code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="facebook_access_token" class="form-label">Access Token</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="access_token"
                                    id="facebook_access_token"
                                    value="{{ old('provider') === 'facebook' ? old('access_token') : ($facebookPixel?->access_token ?? '') }}"
                                    placeholder="Required for Meta Conversions API">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="facebook_test_event_code" class="form-label">Test Event Code</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="test_event_code"
                                    id="facebook_test_event_code"
                                    value="{{ old('provider') === 'facebook' ? old('test_event_code') : ($facebookPixel?->test_event_code ?? '') }}"
                                    placeholder="Optional Meta test event code">
                            </div>
                        </div>

                        <div class="col-sm-12 mb-3">
                            <div class="form-group">
                                <label for="facebook_status" class="d-block">Status</label>
                                <label class="switch">
                                    <input
                                        type="checkbox"
                                        id="facebook_status"
                                        value="1"
                                        name="status"
                                        @if(old('provider') === 'facebook')
                                            {{ old('status') ? 'checked' : '' }}
                                        @elseif(($facebookPixel?->status ?? 1) == 1)
                                            checked
                                        @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <input type="submit" class="btn btn-primary rounded-pill px-4" value="Save Facebook Config">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="pixel-config-card">
                <div class="pixel-config-head tiktok">
                    <h5>TikTok Pixel</h5>
                    <p>Browser Pixel + Events API configuration</p>
                </div>
                <div class="pixel-config-body">
                    <form action="{{route('pixels.store')}}" method="POST" class="row" data-parsley-validate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="provider" value="tiktok">
                        <input type="hidden" name="pixel_id" value="{{ $tiktokPixel?->id }}">

                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="tiktok_name" class="form-label">Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="name"
                                    id="tiktok_name"
                                    value="{{ old('provider') === 'tiktok' ? old('name') : ($tiktokPixel?->name ?? 'TikTok Pixel') }}"
                                    placeholder="TikTok Pixel">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="tiktok_code" class="form-label">Pixel ID *</label>
                                <input
                                    type="text"
                                    class="form-control @error('code'){{ old('provider') === 'tiktok' ? ' is-invalid' : '' }}@enderror"
                                    name="code"
                                    id="tiktok_code"
                                    value="{{ old('provider') === 'tiktok' ? old('code') : ($tiktokPixel?->code ?? '') }}"
                                    required>
                                @if(old('provider') === 'tiktok')
                                    @error('code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="tiktok_access_token" class="form-label">Events API Token</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="access_token"
                                    id="tiktok_access_token"
                                    value="{{ old('provider') === 'tiktok' ? old('access_token') : ($tiktokPixel?->access_token ?? '') }}"
                                    placeholder="Required for TikTok Events API">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="tiktok_test_event_code" class="form-label">Test Event Code</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="test_event_code"
                                    id="tiktok_test_event_code"
                                    value="{{ old('provider') === 'tiktok' ? old('test_event_code') : ($tiktokPixel?->test_event_code ?? '') }}"
                                    placeholder="Optional TikTok test event code">
                            </div>
                        </div>

                        <div class="col-sm-12 mb-3">
                            <div class="form-group">
                                <label for="tiktok_status" class="d-block">Status</label>
                                <label class="switch">
                                    <input
                                        type="checkbox"
                                        id="tiktok_status"
                                        value="1"
                                        name="status"
                                        @if(old('provider') === 'tiktok')
                                            {{ old('status') ? 'checked' : '' }}
                                        @elseif(($tiktokPixel?->status ?? 1) == 1)
                                            checked
                                        @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <input type="submit" class="btn btn-dark rounded-pill px-4" value="Save TikTok Config">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
@endsection
