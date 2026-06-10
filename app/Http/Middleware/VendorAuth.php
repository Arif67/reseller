<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class VendorAuth
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('vendor')->check()) {
            // suspended/pending vendor login korte parbe na
            if (Auth::guard('vendor')->user()->status !== 'active') {
                Auth::guard('vendor')->logout();
                return redirect()->route('vendor.login')
                    ->with('error', 'Your account is not active yet.');
            }
            return $next($request);
        }
        return redirect()->route('vendor.login');
    }
}
