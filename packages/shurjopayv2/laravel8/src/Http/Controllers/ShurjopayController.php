<?php

namespace shurjopayv2\ShurjopayLaravelPackage8\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ShurjopayController extends Controller
{
    public function checkout(array $info)
    {
        $credentials = $this->credentials();
        $tokenData = $this->getToken($credentials);
        $payload = $this->buildCheckoutPayload($info, $credentials, $tokenData);

        $response = Http::asForm()
            ->withHeaders([
                'Accept' => 'application/json',
                'Authorization' => trim(($tokenData['token_type'] ?? 'Bearer') . ' ' . ($tokenData['token'] ?? '')),
            ])
            ->post($this->endpoint($credentials['base_url'], '/secret-pay'), $payload)
            ->throw()
            ->json();

        $checkoutUrl = $response['checkout_url'] ?? null;

        if (! is_string($checkoutUrl) || $checkoutUrl === '') {
            Log::error('ShurjoPay checkout failed: checkout_url missing.', ['response' => $response]);
            throw new RuntimeException('Unable to initiate ShurjoPay checkout.');
        }

        return redirect()->away($checkoutUrl);
    }

    public function verify(string $orderId): string
    {
        $credentials = $this->credentials();
        $tokenData = $this->getToken($credentials);

        $response = Http::acceptJson()
            ->withHeaders([
                'Authorization' => trim(($tokenData['token_type'] ?? 'Bearer') . ' ' . ($tokenData['token'] ?? '')),
            ])
            ->post($this->endpoint($credentials['base_url'], '/verification'), [
                'order_id' => $orderId,
            ])
            ->throw()
            ->body();

        return $response;
    }

    protected function buildCheckoutPayload(array $info, array $credentials, array $tokenData): array
    {
        return [
            'prefix' => $credentials['prefix'],
            'token' => $tokenData['token'] ?? null,
            'return_url' => $credentials['return_url'],
            'cancel_url' => $credentials['cancel_url'],
            'store_id' => $tokenData['store_id'] ?? null,
            'amount' => $info['amount'] ?? null,
            'order_id' => $info['order_id'] ?? null,
            'currency' => $info['currency'] ?? 'BDT',
            'customer_name' => $info['customer_name'] ?? null,
            'customer_address' => $info['customer_address'] ?? null,
            'customer_phone' => $info['customer_phone'] ?? null,
            'customer_city' => $info['customer_city'] ?? null,
            'customer_email' => $info['customer_email'] ?? ($info['email'] ?? null),
            'customer_state' => $info['customer_state'] ?? null,
            'customer_postcode' => $info['customer_postcode'] ?? ($info['customer_post_code'] ?? null),
            'customer_country' => $info['customer_country'] ?? 'BD',
            'client_ip' => $info['client_ip'] ?? request()->ip(),
            'discount_amount' => $info['discount_amount'] ?? ($info['discsount_amount'] ?? 0),
            'disc_percent' => $info['disc_percent'] ?? 0,
            'shipping_address' => $info['shipping_address'] ?? ($info['customer_address'] ?? null),
            'shipping_city' => $info['shipping_city'] ?? ($info['customer_city'] ?? null),
            'shipping_country' => $info['shipping_country'] ?? ($info['customer_country'] ?? 'BD'),
            'received_person_name' => $info['received_person_name'] ?? ($info['customer_name'] ?? null),
            'shipping_phone_number' => $info['shipping_phone_number'] ?? ($info['customer_phone'] ?? null),
            'value1' => $info['value1'] ?? null,
            'value2' => $info['value2'] ?? null,
            'value3' => $info['value3'] ?? null,
            'value4' => $info['value4'] ?? null,
        ];
    }

    protected function getToken(array $credentials): array
    {
        $response = Http::acceptJson()
            ->post($this->endpoint($credentials['base_url'], '/get_token'), [
                'username' => $credentials['username'],
                'password' => $credentials['password'],
            ])
            ->throw()
            ->json();

        if (! is_array($response) || empty($response['token'])) {
            Log::error('ShurjoPay token request failed.', ['response' => $response]);
            throw new RuntimeException('Unable to authenticate with ShurjoPay.');
        }

        return $response;
    }

    protected function endpoint(string $baseUrl, string $path): string
    {
        $normalizedBase = rtrim($baseUrl, '/');

        if (! str_ends_with($normalizedBase, '/api')) {
            $normalizedBase .= '/api';
        }

        return $normalizedBase . $path;
    }

    protected function credentials(): array
    {
        $credentials = [
            'username' => config('shurjopay.apiCredentials.username'),
            'password' => config('shurjopay.apiCredentials.password'),
            'prefix' => config('shurjopay.apiCredentials.prefix'),
            'return_url' => config('shurjopay.apiCredentials.return_url'),
            'cancel_url' => config('shurjopay.apiCredentials.cancel_url'),
            'base_url' => config('shurjopay.apiCredentials.base_url'),
        ];

        foreach ($credentials as $key => $value) {
            if (! is_string($value) || trim($value) === '') {
                throw new RuntimeException("Missing ShurjoPay config value: {$key}");
            }
        }

        return $credentials;
    }
}
