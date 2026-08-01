<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Supported locales.
     */
    public const SUPPORTED_LOCALES = ['id', 'en', 'es', 'ar'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale');

        if ($locale && in_array($locale, self::SUPPORTED_LOCALES, true)) {
            App::setLocale($locale);
        } else {
            // Default locale
            App::setLocale(config('app.locale', 'id'));
        }

        return $next($request);
    }
}
