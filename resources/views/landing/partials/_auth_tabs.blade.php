@php
    // $active = 'reseller' | 'vendor', $type = 'login' | 'register'
    $isLogin = ($type ?? 'login') === 'login';
    $resellerUrl = $isLogin
        ? (Route::has('reseller.login') ? route('reseller.login') : '#')
        : (Route::has('reseller.register') ? route('reseller.register') : '#');
    $vendorUrl = $isLogin
        ? (Route::has('vendor.login') ? route('vendor.login') : '#')
        : (Route::has('vendor.register') ? route('vendor.register') : '#');
@endphp
<div class="auth-tabs">
    <a href="{{ $resellerUrl }}" class="auth-tab {{ ($active ?? 'reseller') === 'reseller' ? 'active' : '' }}">রিসেলার</a>
    <a href="{{ $vendorUrl }}" class="auth-tab {{ ($active ?? 'reseller') === 'vendor' ? 'active' : '' }}">ভেন্ডর</a>
</div>
