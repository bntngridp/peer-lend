<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Accepts one or more role names as variadic arguments:
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,borrower')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            abort(401);
        }

        $allRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $trimmed = trim($r);
                if ($trimmed !== '') {
                    $allRoles[] = $trimmed;
                }
            }
        }

        if (! $request->user()->hasAnyRole($allRoles)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
