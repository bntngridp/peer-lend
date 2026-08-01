<?php

namespace App\Modules\Shared\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetLocaleMiddleware;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Switch application language.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $targetLocale = strtolower($locale);

        if (in_array($targetLocale, SetLocaleMiddleware::SUPPORTED_LOCALES, true)) {
            session(['locale' => $targetLocale]);
        }

        return redirect()->back();
    }
}
