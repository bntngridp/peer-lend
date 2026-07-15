<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Services\Google2FAService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function __construct(
        private readonly Google2FAService $google2faService
    ) {}

    /**
     * Show 2FA activation setup screen.
     */
    public function showSetup(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->google2fa_enabled) {
            return redirect()->route('dashboard')
                ->with('warning', 'Google Authenticator 2FA is already enabled.');
        }

        // Generate temporary secret and store in session
        $secret = $this->google2faService->generateSecret();
        session(['google2fa_temp_secret' => $secret]);

        $qrUrl = $this->google2faService->getQRCodeUrl($user->email, $secret);

        return view('auth.2fa-setup', compact('secret', 'qrUrl'));
    }

    /**
     * Complete activation of 2FA.
     */
    public function enable(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $secret = session('google2fa_temp_secret');

        if (! $secret) {
            return redirect()->route('2fa.setup')
                ->with('error', 'Session expired. Please try setting up 2FA again.');
        }

        if (! $this->google2faService->verifyCode($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid Google Authenticator code.']);
        }

        // Enable 2FA on User model
        $user = Auth::user();
        $user->update([
            'google2fa_secret'  => $secret,
            'google2fa_enabled' => true,
        ]);

        // Auto verify session
        session()->forget('google2fa_temp_secret');
        session(['google2fa_verified' => true]);

        return redirect()->route('dashboard')
            ->with('success', 'Google Authenticator 2FA has been successfully enabled.');
    }

    /**
     * Show 2FA login verification challenge.
     */
    public function showVerifyForm(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! Auth::user()->google2fa_enabled) {
            return redirect()->route('dashboard');
        }

        if (session('google2fa_verified')) {
            return redirect()->route('dashboard');
        }

        return view('auth.2fa-verify');
    }

    /**
     * Process 2FA login verification code.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        if (! $this->google2faService->verifyCode($user->google2fa_secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code. Please try again.']);
        }

        // Mark session as 2FA verified
        session(['google2fa_verified' => true]);

        return redirect()->intended(route('dashboard'));
    }
}
