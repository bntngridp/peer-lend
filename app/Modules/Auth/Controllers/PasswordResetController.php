<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    // ─── Forgot Password ──────────────────────────────────────────────

    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $emailKey = strtolower(trim($request->email));
        $ip = $request->ip();

        $dailyKey = 'reset-password-daily:' . $ip . '-' . $emailKey;
        $cooldownKey = 'reset-password-cooldown:' . $ip . '-' . $emailKey;

        // 1. Check Daily Limit (Max 3x per 24 hours)
        if (RateLimiter::tooManyAttempts($dailyKey, 3)) {
            return back()->withErrors([
                'email' => 'Anda telah mencapai batas maksimal 3x permintaan reset password per hari. Silakan coba lagi besok.',
            ])->withInput();
        }

        // 2. Check 60-second Cooldown Timer Limit
        if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
            $seconds = RateLimiter::availableIn($cooldownKey);
            return back()
                ->with('warning', "Tautan pemulihan baru saja dikirim. Harap tunggu {$seconds} detik untuk mengirim ulang.")
                ->with('cooldown_seconds', $seconds)
                ->withInput();
        }

        try {
            $this->authService->sendResetLink($request->email);

            // Record attempts
            RateLimiter::hit($dailyKey, 86400); // 1 day decay (24 hours)
            RateLimiter::hit($cooldownKey, 60);  // 60 seconds cooldown

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return back()
            ->with('success', 'Tautan pemulihan password telah berhasil dikirimkan ke email Anda! Silakan periksa inbox atau folder spam.')
            ->with('cooldown_seconds', 60);
    }

    // ─── Reset Password ───────────────────────────────────────────────

    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token'                 => ['required'],
            'email'                 => ['required', 'email'],
            'password'              => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                'confirmed'
            ],
        ], [
            'password.min'       => 'Password harus terdiri dari minimal 8 karakter.',
            'password.regex'     => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus (@, $, !, %, *, #, ?, &).',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        try {
            $this->authService->resetPassword($request->only(
                'token', 'email', 'password', 'password_confirmation'
            ));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()->route('login')
            ->with('success', 'Password Anda telah berhasil diperbarui! Silakan sign in dengan password baru.');
    }
}
