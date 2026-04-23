<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingToolConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Toastr;

class MarketingToolsController extends Controller
{
    protected function clearCaches(): void
    {
        Cache::forget('shared_view_data_v3');
    }

    public function index()
    {
        $config = MarketingToolConfig::firstOrCreate([], [
            'utm_tracking_enabled' => 1,
            'abandoned_cart_recovery_minutes' => 60,
            'status' => 1,
        ]);

        return view('backEnd.marketing_tools.index', compact('config'));
    }

    public function update(Request $request)
    {
        $config = MarketingToolConfig::firstOrCreate([]);

        $config->update([
            'ga4_measurement_id' => $request->ga4_measurement_id,
            'google_ads_id' => $request->google_ads_id,
            'google_ads_conversion_label' => $request->google_ads_conversion_label,
            'clarity_project_id' => $request->clarity_project_id,
            'merchant_store_name' => $request->merchant_store_name,
            'merchant_feed_enabled' => $request->boolean('merchant_feed_enabled') ? 1 : 0,
            'utm_tracking_enabled' => $request->boolean('utm_tracking_enabled') ? 1 : 0,
            'abandoned_cart_enabled' => $request->boolean('abandoned_cart_enabled') ? 1 : 0,
            'abandoned_cart_recovery_minutes' => max((int) $request->input('abandoned_cart_recovery_minutes', 60), 1),
            'abandoned_cart_sms_enabled' => $request->boolean('abandoned_cart_sms_enabled') ? 1 : 0,
            'abandoned_cart_whatsapp_enabled' => $request->boolean('abandoned_cart_whatsapp_enabled') ? 1 : 0,
            'whatsapp_number' => $request->whatsapp_number,
            'whatsapp_api_url' => $request->whatsapp_api_url,
            'whatsapp_api_key' => $request->whatsapp_api_key,
            'whatsapp_sender' => $request->whatsapp_sender,
            'abandoned_cart_sms_template' => $request->abandoned_cart_sms_template,
            'abandoned_cart_whatsapp_template' => $request->abandoned_cart_whatsapp_template,
            'status' => $request->boolean('status') ? 1 : 0,
        ]);

        $this->clearCaches();

        Toastr::success('Success', 'Marketing tools configuration updated successfully');
        return redirect()->route('marketing.tools.index');
    }
}
