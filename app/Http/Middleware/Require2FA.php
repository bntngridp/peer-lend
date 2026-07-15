<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Require2FA
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // If 2FA is enabled but not verified in this session
            if ($user->google2fa_enabled && ! session('google2fa_verified')) {
                // Allow requests to 2FA verification routes and logout
                if (! $request->routeIs('2fa.verify') && 
                    ! $request->routeIs('2fa.verify.post') && 
                    ! $request->routeIs('logout')) {
                    
                    return redirect()->route('2fa.verify')
                        ->with('warning', 'Please enter your 2-Factor Authentication (OTP) code to proceed.');
                }
            }
        }

        return $next($request);
    }
}
