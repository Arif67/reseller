@extends('backEnd.layouts.master')
@section('title', 'Marketing Suite')
@section('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .marketing-suite {
        font-family: 'Hind Siliguri', sans-serif;
    }
    .marketing-card {
        border: 1px solid #e6edf5;
        border-radius: 18px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.06);
    }
    .marketing-card .card-body {
        padding: 24px;
    }
    .marketing-card h5 {
        font-weight: 700;
        margin-bottom: 6px;
    }
    .marketing-card p {
        color: #475569;
        font-weight: 500;
    }
    .marketing-card .form-label {
        font-weight: 700;
    }
    .marketing-card .form-control,
    .marketing-card .form-select {
        border-radius: 12px;
        font-weight: 500;
    }
</style>
@endsection
@section('content')
<div class="container-fluid marketing-suite">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Marketing Suite</h4>
            </div>
        </div>
    </div>
    <form action="{{ route('marketing.tools.update') }}" method="POST" class="row">
        @csrf
        <div class="col-lg-12 mb-4">
            <div class="card marketing-card">
                <div class="card-body">
                    <h5>Abandoned Cart Recovery</h5>
                    <p>Cart abandon korle lead capture korbe. SMS/WhatsApp recovery er config ekhane rakhun.</p>
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="abandoned_cart_enabled" name="abandoned_cart_enabled" value="1" @if($config->abandoned_cart_enabled) checked @endif>
                                <label class="form-check-label" for="abandoned_cart_enabled">Abandoned cart tracking enable</label>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="mb-3">
                                <label for="abandoned_cart_recovery_minutes" class="form-label">Recovery Delay Minutes</label>
                                <input type="number" class="form-control" id="abandoned_cart_recovery_minutes" name="abandoned_cart_recovery_minutes" value="{{ old('abandoned_cart_recovery_minutes', $config->abandoned_cart_recovery_minutes) }}" min="1">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="abandoned_cart_sms_enabled" name="abandoned_cart_sms_enabled" value="1" @if($config->abandoned_cart_sms_enabled) checked @endif>
                                <label class="form-check-label" for="abandoned_cart_sms_enabled">Recovery SMS enable</label>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="abandoned_cart_whatsapp_enabled" name="abandoned_cart_whatsapp_enabled" value="1" @if($config->abandoned_cart_whatsapp_enabled) checked @endif>
                                <label class="form-check-label" for="abandoned_cart_whatsapp_enabled">Recovery WhatsApp enable</label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
                                <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $config->whatsapp_number) }}" placeholder="8801XXXXXXXXX">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="whatsapp_api_url" class="form-label">WhatsApp API URL</label>
                                <input type="text" class="form-control" id="whatsapp_api_url" name="whatsapp_api_url" value="{{ old('whatsapp_api_url', $config->whatsapp_api_url) }}" placeholder="https://your-whatsapp-api/send">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="whatsapp_api_key" class="form-label">WhatsApp API Key</label>
                                <input type="text" class="form-control" id="whatsapp_api_key" name="whatsapp_api_key" value="{{ old('whatsapp_api_key', $config->whatsapp_api_key) }}" placeholder="api key">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="whatsapp_sender" class="form-label">WhatsApp Sender / Device</label>
                                <input type="text" class="form-control" id="whatsapp_sender" name="whatsapp_sender" value="{{ old('whatsapp_sender', $config->whatsapp_sender) }}" placeholder="optional sender or device id">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-check form-switch mt-lg-4">
                                <input class="form-check-input" type="checkbox" id="status" name="status" value="1" @if($config->status) checked @endif>
                                <label class="form-check-label" for="status">Marketing suite active</label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="abandoned_cart_sms_template" class="form-label">SMS Template</label>
                                <textarea class="form-control" id="abandoned_cart_sms_template" name="abandoned_cart_sms_template" rows="4" placeholder="Hi {name}, apnar cart e product roye geche.">{{ old('abandoned_cart_sms_template', $config->abandoned_cart_sms_template) }}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="abandoned_cart_whatsapp_template" class="form-label">WhatsApp Template</label>
                                <textarea class="form-control" id="abandoned_cart_whatsapp_template" name="abandoned_cart_whatsapp_template" rows="4" placeholder="Hi {name}, apnar cart complete korun.">{{ old('abandoned_cart_whatsapp_template', $config->abandoned_cart_whatsapp_template) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card marketing-card h-100">
                <div class="card-body">
                    <h5>Analytics & Ads</h5>
                    <p>GA4, Google Ads ar Microsoft Clarity ekhane configure korun.</p>
                    <div class="mb-3">
                        <label for="ga4_measurement_id" class="form-label">GA4 Measurement ID</label>
                        <input type="text" class="form-control" id="ga4_measurement_id" name="ga4_measurement_id" value="{{ old('ga4_measurement_id', $config->ga4_measurement_id) }}" placeholder="G-XXXXXXXXXX">
                    </div>
                    <div class="mb-3">
                        <label for="google_ads_id" class="form-label">Google Ads ID</label>
                        <input type="text" class="form-control" id="google_ads_id" name="google_ads_id" value="{{ old('google_ads_id', $config->google_ads_id) }}" placeholder="AW-XXXXXXXXX">
                    </div>
                    <div class="mb-3">
                        <label for="google_ads_conversion_label" class="form-label">Google Ads Conversion Label</label>
                        <input type="text" class="form-control" id="google_ads_conversion_label" name="google_ads_conversion_label" value="{{ old('google_ads_conversion_label', $config->google_ads_conversion_label) }}" placeholder="purchase conversion label">
                    </div>
                    <div class="mb-0">
                        <label for="clarity_project_id" class="form-label">Microsoft Clarity Project ID</label>
                        <input type="text" class="form-control" id="clarity_project_id" name="clarity_project_id" value="{{ old('clarity_project_id', $config->clarity_project_id) }}" placeholder="clarity project id">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card marketing-card h-100">
                <div class="card-body">
                    <h5>Merchant & UTM</h5>
                    <p>Google Merchant Center feed ar UTM tracking settings.</p>
                    <div class="mb-3">
                        <label for="merchant_store_name" class="form-label">Merchant Store Name</label>
                        <input type="text" class="form-control" id="merchant_store_name" name="merchant_store_name" value="{{ old('merchant_store_name', $config->merchant_store_name) }}" placeholder="store name">
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="merchant_feed_enabled" name="merchant_feed_enabled" value="1" @if($config->merchant_feed_enabled) checked @endif>
                        <label class="form-check-label" for="merchant_feed_enabled">Google Merchant feed enable</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Feed URL</label>
                        <input type="text" class="form-control" value="{{ route('merchant.feed') }}" readonly>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="utm_tracking_enabled" name="utm_tracking_enabled" value="1" @if($config->utm_tracking_enabled) checked @endif>
                        <label class="form-check-label" for="utm_tracking_enabled">UTM campaign tracker enable</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-success rounded-pill px-4">Save Marketing Suite</button>
        </div>
    </form>
</div>
@endsection
