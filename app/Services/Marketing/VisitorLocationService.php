<?php

namespace App\Services\Marketing;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class VisitorLocationService
{
    public function resolve(string $ip, array $headers = []): array
    {
        $headerLocation = $this->fromHeaders($headers);

        if ($this->hasUsefulLocation($headerLocation)) {
            return $headerLocation;
        }

        if (! $this->isLookupEligible($ip)) {
            return $headerLocation;
        }

        return Cache::remember('visitor-location:' . md5($ip), now()->addHours(12), function () use ($ip, $headerLocation) {
            try {
                $response = Http::timeout(3)
                    ->acceptJson()
                    ->get('http://ip-api.com/json/' . $ip, [
                        'fields' => 'status,country,countryCode,regionName,city,query',
                    ]);

                if (! $response->successful()) {
                    return $headerLocation;
                }

                $payload = $response->json();

                if (($payload['status'] ?? null) !== 'success') {
                    return $headerLocation;
                }

                return [
                    'country' => $payload['country'] ?? ($headerLocation['country'] ?? null),
                    'region' => $payload['regionName'] ?? ($headerLocation['region'] ?? null),
                    'district' => $this->normalizeDistrict($payload['city'] ?? ($payload['regionName'] ?? null)),
                    'city' => $payload['city'] ?? ($headerLocation['city'] ?? null),
                ];
            } catch (\Throwable $e) {
                return $headerLocation;
            }
        });
    }

    public function normalizeDistrict(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $value = trim($value);
        $value = preg_replace('/\b(city|district|zila|division)\b/i', '', $value) ?: $value;
        $value = preg_replace('/\s+/', ' ', trim($value)) ?: $value;

        return $value !== '' ? $value : null;
    }

    protected function fromHeaders(array $headers): array
    {
        $countryCode = $headers['cf-ipcountry'] ?? $headers['CF-IPCountry'] ?? null;
        $region = $headers['cf-region'] ?? $headers['CF-Region'] ?? $headers['x-appengine-region'] ?? null;
        $city = $headers['cf-ipcity'] ?? $headers['CF-IPCity'] ?? $headers['x-appengine-city'] ?? null;

        return [
            'country' => $this->mapCountryCode($countryCode),
            'region' => $region,
            'district' => $this->normalizeDistrict($city ?: $region),
            'city' => $city,
        ];
    }

    protected function mapCountryCode(?string $code): ?string
    {
        if (! $code) {
            return null;
        }

        $map = [
            'BD' => 'Bangladesh',
        ];

        return $map[strtoupper($code)] ?? strtoupper($code);
    }

    protected function hasUsefulLocation(array $location): bool
    {
        return filled($location['district'] ?? null) || filled($location['city'] ?? null) || filled($location['country'] ?? null);
    }

    protected function isLookupEligible(string $ip): bool
    {
        if ($ip === '' || $ip === '127.0.0.1' || $ip === '::1') {
            return false;
        }

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }
}
