<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class ResellerAuth
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('reseller')->check()) {
            if (Auth::guard('reseller')->user()->status !== 'active') {
                Auth::guard('reseller')->logout();
                return redirect()->route('reseller.login')
                    ->with('error', 'Your account is not active yet.');
            }
            return $next($request);
        }
        return redirect()->route('reseller.login');
    }
}
