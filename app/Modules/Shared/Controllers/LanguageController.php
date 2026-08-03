<?php

namespace App\Modules\Shared\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetLocaleMiddleware;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Switch application language and stay on the exact page & tab.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $targetLocale = strtolower($locale);

        if (in_array($targetLocale, SetLocaleMiddleware::SUPPORTED_LOCALES, true)) {
            session(['locale' => $targetLocale]);
        }

        $redirectUrl = $request->query('redirect')
            ?: $request->headers->get('referer')
            ?: url()->previous();

        return redirect()->to($redirectUrl);
    }
}
