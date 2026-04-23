@extends('backEnd.layouts.master')
@section('title', 'SEO Configuration')
@section('css')
<link href="{{ asset('public/backEnd') }}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .seo-page-shell {
        font-family: 'Hind Siliguri', sans-serif;
    }

    .seo-page-shell .page-title-box {
        margin-bottom: 18px;
    }

    .seo-page-shell .page-title {
        font-family: 'Hind Siliguri', sans-serif;
        font-weight: 700;
        font-size: 30px;
        letter-spacing: 0.01em;
        color: #0f172a;
    }

    .seo-config-card {
        border: 1px solid #e8edf4;
        border-radius: 18px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.06);
    }

    .seo-config-hero {
        background: linear-gradient(135deg, #0f172a, #1d4ed8 55%, #0ea5e9);
        color: #fff;
        border-radius: 18px 18px 0 0;
        padding: 24px 26px;
        font-family: 'Hind Siliguri', sans-serif;
    }

    .seo-config-hero h5,
    .seo-config-hero p {
        margin: 0;
        color: inherit;
    }

    .seo-config-hero h5 {
        font-size: 28px;
        font-weight: 700;
        line-height: 1.25;
    }

    .seo-config-hero p {
        margin-top: 8px;
        opacity: 0.9;
        font-size: 16px;
        font-weight: 500;
    }

    .seo-config-body {
        padding: 24px;
        background: #fff;
        border-radius: 0 0 18px 18px;
        font-family: 'Hind Siliguri', sans-serif;
    }

    .seo-config-body .form-label {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .seo-config-body .form-control,
    .seo-config-body .form-select {
        font-family: 'Hind Siliguri', sans-serif;
        font-size: 15px;
        font-weight: 500;
        border-radius: 12px;
        padding: 12px 14px;
    }

    .seo-config-body .btn {
        font-family: 'Hind Siliguri', sans-serif;
        font-size: 16px;
        font-weight: 700;
    }

    .seo-config-tips {
        background: #f8fbff;
        border: 1px solid #dbeafe;
        border-radius: 14px;
        padding: 16px 18px;
        font-family: 'Hind Siliguri', sans-serif;
    }

    .seo-config-tips h6 {
        margin-bottom: 10px;
        font-size: 19px;
        font-weight: 700;
        color: #0f172a;
    }

    .seo-config-tips ul {
        margin: 0;
        padding-left: 18px;
    }

    .seo-config-tips li {
        font-size: 15px;
        font-weight: 600;
        line-height: 1.7;
        color: #1e293b;
    }
</style>
@endsection

@section('content')
<div class="container-fluid seo-page-shell">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('settings.index') }}" class="btn btn-primary rounded-pill">General Settings</a>
                </div>
                <h4 class="page-title">SEO Configuration</h4>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="seo-config-card">
                <div class="seo-config-hero">
                    <h5>Search Engine Setup</h5>
                    <p>Manage the homepage default SEO, Search Console verification, and robots behavior from here.</p>
                </div>
                <div class="seo-config-body">
                    <form action="{{ route('seo.config.update') }}" method="POST" class="row" data-parsley-validate>
                        @csrf
                        <input type="hidden" name="id" value="{{ $seo_data->id }}">

                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group mb-3">
                                        <label for="meta_title" class="form-label">Default SEO Title</label>
                                        <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                            name="meta_title" id="meta_title" maxlength="255"
                                            value="{{ old('meta_title', $seo_data->meta_title) }}"
                                            placeholder="Homepage and fallback title">
                                        @error('meta_title')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group mb-3">
                                        <label for="meta_tag" class="form-label">Default SEO Keywords / Tags</label>
                                        <input type="text" class="form-control @error('meta_tag') is-invalid @enderror"
                                            name="meta_tag" id="meta_tag"
                                            value="{{ old('meta_tag', $seo_data->meta_tag) }}"
                                            placeholder="ecommerce, bangladesh, accessories">
                                        @error('meta_tag')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group mb-3">
                                        <label for="meta_description" class="form-label">Default SEO Description</label>
                                        <textarea class="form-control @error('meta_description') is-invalid @enderror"
                                            name="meta_description" id="meta_description" rows="4"
                                            placeholder="Default description shown in Google search results">{{ old('meta_description', $seo_data->meta_description) }}</textarea>
                                        @error('meta_description')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group mb-3">
                                        <label for="google_site_verification" class="form-label">Google Site Verification</label>
                                        <input type="text"
                                            class="form-control @error('google_site_verification') is-invalid @enderror"
                                            name="google_site_verification" id="google_site_verification"
                                            value="{{ old('google_site_verification', $seo_data->google_site_verification ?? '') }}"
                                            placeholder="Paste the meta tag content value here">
                                        @error('google_site_verification')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group mb-4">
                                        <label for="default_robots" class="form-label">Default Robots Directive</label>
                                        <select class="form-control @error('default_robots') is-invalid @enderror"
                                            name="default_robots" id="default_robots">
                                            <option value="index, follow" {{ old('default_robots', $seo_data->default_robots ?? 'index, follow') === 'index, follow' ? 'selected' : '' }}>index, follow</option>
                                            <option value="noindex, nofollow" {{ old('default_robots', $seo_data->default_robots ?? '') === 'noindex, nofollow' ? 'selected' : '' }}>noindex, nofollow</option>
                                        </select>
                                        @error('default_robots')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <input type="submit" class="btn btn-success rounded-pill px-4" value="Save SEO Configuration">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mt-4 mt-lg-0">
                            <div class="seo-config-tips mb-3">
                                <h6>Quick SEO Guide</h6>
                                <ul>
                                    <li>Use your brand name and main keyword in the `Default SEO Title`. 50 to 60 characters works well.</li>
                                    <li>Write a clear selling point in the `Default SEO Description`. 150 to 160 characters is a good range.</li>
                                    <li>Add your main category keywords in `Default SEO Keywords`, separated by commas.</li>
                                    <li>Fill in page-specific SEO fields for product, category, and subcategory pages for better results.</li>
                                    <li>Avoid duplicate titles and empty descriptions.</li>
                                </ul>
                            </div>

                            <div class="seo-config-tips">
                                <h6>Google Search Console Quick Guide</h6>
                                <ul>
                                    <li>Open `Google Search Console` and add your website property.</li>
                                    <li>Select the `HTML tag` verification method.</li>
                                    <li>Copy the `content` value from the meta tag Google provides.</li>
                                    <li>Paste that value into the `Google Site Verification` field and save.</li>
                                    <li>Return to Search Console and click `Verify`.</li>
                                    <li>After verification, submit `/sitemap.xml` in the `Sitemaps` section.</li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('public/backEnd/') }}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-validation.init.js"></script>
<script src="{{ asset('public/backEnd/') }}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-advanced.init.js"></script>
@endsection
