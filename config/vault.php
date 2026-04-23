<?php

return [
    'enabled' => (bool) env('VAULT_UPLOAD_ENABLED', false),
    'fallback_to_local' => (bool) env('VAULT_UPLOAD_FALLBACK_TO_LOCAL', false),
    'endpoint' => env('VAULT_UPLOAD_ENDPOINT', 'https://iws-vault-service.3bitsmind.com/upload'),
    'access_key_id' => env('VAULT_ACCESS_KEY_ID'),
    'secret_key' => env('VAULT_SECRET_KEY'),
    'public_base_url' => env('VAULT_PUBLIC_BASE_URL', ''),
];
