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

        if ($request->has('redirect')) {
            $redirectUrl = $request->query('redirect');
            if (filter_var($redirectUrl, FILTER_VALIDATE_URL) || str_starts_with($redirectUrl, '/')) {
                return redirect()->to($redirectUrl);
            }
        }

        return redirect()->back();
    }
}
