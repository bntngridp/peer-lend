<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckKYCApproved
{
    /**
     * Handle an incoming request.
     *
     * Block access to sensitive financial actions if the user's KYC
     * has not been reviewed and approved by an administrator.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Admins are exempt from KYC checks
        if ($user->isAdmin()) {
            return $next($request);
        }

        $kyc = $user->kyc;

        if (! $kyc || ! $kyc->isApproved()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'KYC verification required before proceeding.',
                ], 403);
            }

            return redirect()->route('kyc.index')
                ->with('warning', 'Please complete and get your KYC verified before performing this action.');
        }

        return $next($request);
    }
}
