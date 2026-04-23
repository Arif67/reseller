<?php

namespace App\Console\Commands;

use App\Models\AbandonedCartLead;
use App\Models\GeneralSetting;
use App\Models\MarketingToolConfig;
use App\Models\SmsGateway;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ProcessAbandonedCartRecoveries extends Command
{
    protected $signature = 'marketing:recover-abandoned-carts';

    protected $description = 'Send abandoned cart recovery messages via SMS and WhatsApp';

    public function handle(): int
    {
        if (! Schema::hasTable('marketing_tool_configs') || ! Schema::hasTable('abandoned_cart_leads')) {
            $this->warn('Marketing tables are not ready.');
            return self::SUCCESS;
        }

        $config = MarketingToolConfig::where('status', 1)->first();

        if (! $config || ! $config->abandoned_cart_enabled) {
            $this->info('Abandoned cart recovery is disabled.');
            return self::SUCCESS;
        }

        $cutoff = now()->subMinutes(max((int) $config->abandoned_cart_recovery_minutes, 1));

        $leads = AbandonedCartLead::query()
            ->where('status', 'active')
            ->where('cart_count', '>', 0)
            ->whereNotNull('last_activity_at')
            ->where('last_activity_at', '<=', $cutoff)
            ->where(function ($query) use ($config) {
                if ($config->abandoned_cart_sms_enabled) {
                    $query->orWhereNull('sms_sent_at');
                }

                if ($config->abandoned_cart_whatsapp_enabled) {
                    $query->orWhereNull('whatsapp_sent_at');
                }
            })
            ->get();

        $site = GeneralSetting::where('status', 1)->first();
        $smsGateway = SmsGateway::where('status', 1)->first();

        foreach ($leads as $lead) {
            $errorMessages = [];

            if ($config->abandoned_cart_sms_enabled && ! $lead->sms_sent_at && $lead->phone) {
                $smsMessage = $this->renderTemplate(
                    $config->abandoned_cart_sms_template ?: 'প্রিয় {name}, আপনার cart এ কিছু product রয়ে গেছে। অর্ডার complete করুন: {cart_url}',
                    $lead,
                    $site
                );

                $smsSent = $this->sendSms($smsGateway, $lead->phone, $smsMessage);

                if ($smsSent) {
                    $lead->sms_sent_at = now();
                } else {
                    $errorMessages[] = 'SMS send failed';
                }
            }

            if ($config->abandoned_cart_whatsapp_enabled && ! $lead->whatsapp_sent_at && $lead->phone) {
                $whatsAppMessage = $this->renderTemplate(
                    $config->abandoned_cart_whatsapp_template ?: 'প্রিয় {name}, আপনার cart checkout বাকি আছে। Continue করুন: {cart_url}',
                    $lead,
                    $site
                );

                $whatsAppSent = $this->sendWhatsApp($config, $lead->phone, $whatsAppMessage);

                if ($whatsAppSent) {
                    $lead->whatsapp_sent_at = now();
                } else {
                    $errorMessages[] = 'WhatsApp send failed';
                }
            }

            $lead->last_recovery_attempt_at = now();

            if ($errorMessages === []) {
                $lead->status = 'contacted';
                $lead->last_recovery_error = null;
            } else {
                $lead->last_recovery_error = implode(' | ', $errorMessages);
            }

            $lead->save();
        }

        $this->info('Processed ' . $leads->count() . ' abandoned carts.');
        return self::SUCCESS;
    }

    protected function renderTemplate(string $template, AbandonedCartLead $lead, ?GeneralSetting $site): string
    {
        return strtr($template, [
            '{name}' => $lead->name ?: 'Customer',
            '{phone}' => $lead->phone ?: '',
            '{store}' => $site?->name ?: config('app.name'),
            '{cart_url}' => route('marketing.recover_cart', $lead->recovery_token),
            '{total}' => number_format((float) $lead->cart_total, 2, '.', ''),
        ]);
    }

    protected function sendSms(?SmsGateway $gateway, string $phone, string $message): bool
    {
        if (! $gateway || ! $gateway->url || ! $gateway->api_key || ! $gateway->serderid) {
            return false;
        }

        $response = Http::asForm()->post($gateway->url, [
            'api_key' => $gateway->api_key,
            'number' => $phone,
            'type' => 'text',
            'senderid' => $gateway->serderid,
            'message' => $message,
        ]);

        if (! $response->successful()) {
            Log::warning('Abandoned cart SMS failed', ['phone' => $phone, 'response' => $response->body()]);
        }

        return $response->successful();
    }

    protected function sendWhatsApp(MarketingToolConfig $config, string $phone, string $message): bool
    {
        if (! $config->whatsapp_api_url || ! $config->whatsapp_api_key) {
            return false;
        }

        $payload = [
            'api_key' => $config->whatsapp_api_key,
            'number' => $phone,
            'message' => $message,
        ];

        if ($config->whatsapp_sender) {
            $payload['sender'] = $config->whatsapp_sender;
        }

        $response = Http::asForm()->post($config->whatsapp_api_url, $payload);

        if (! $response->successful()) {
            Log::warning('Abandoned cart WhatsApp failed', ['phone' => $phone, 'response' => $response->body()]);
        }

        return $response->successful();
    }
}
