<?php

namespace shurjopayv2\ShurjopayLaravelPackage8;

use Illuminate\Support\ServiceProvider;

class ShurjopayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $configPath = dirname(__DIR__) . '/config/shurjopay.php';

        $this->mergeConfigFrom($configPath, 'shurjopay');
    }
}
