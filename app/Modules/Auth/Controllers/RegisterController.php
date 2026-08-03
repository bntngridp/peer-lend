<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Requests\VerifyOtpRequest;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Show the registration form.
     */
    public function showForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle the registration request initiation.
     * Generates OTP and redirects to OTP verification page.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        // Generate 6-digit OTP
        $otpCode = (string) random_int(100000, 999999);

        // Store pending registration data & OTP in session (valid for 5 minutes)
        session([
            'registration_pending_data' => $validatedData,
            'registration_otp'          => $otpCode,
            'otp_expires_at'            => now()->addMinutes(5)->timestamp,
        ]);

        return redirect()->route('register.otp')
            ->with('info', 'Kode OTP verifikasi telah dikirimkan ke nomor ' . ($validatedData['phone'] ?? '') . '. Silakan verifikasi untuk menyelesaikan pendaftaran.');
    }

    /**
     * Show the OTP verification form.
     */
    public function showOtpForm(): View|RedirectResponse
    {
        $pendingData = session('registration_pending_data');

        if (! $pendingData) {
            return redirect()->route('register')
                ->with('error', 'Sesi pendaftaran Anda telah berakhir. Silakan isi kembali form pendaftaran.');
        }

        return view('auth.verify-otp', [
            'phone'      => $pendingData['phone'] ?? '',
            'email'      => $pendingData['email'] ?? '',
            'otpCode'    => session('registration_otp'),
            'expiresAt'  => session('otp_expires_at'),
        ]);
    }

    /**
     * Verify the 6-digit OTP and complete account creation.
     */
    public function verifyOtp(VerifyOtpRequest $request): RedirectResponse
    {
        $pendingData = session('registration_pending_data');
        $storedOtp   = session('registration_otp');
        $expiresAt   = session('otp_expires_at');

        if (! $pendingData || ! $storedOtp) {
            return redirect()->route('register')
                ->with('error', 'Sesi pendaftaran Anda telah berakhir. Silakan isi kembali form pendaftaran.');
        }

        // Check expiration
        if (now()->timestamp > $expiresAt) {
            return redirect()->back()
                ->with('error', 'Kode OTP telah kedaluwarsa. Silakan klik "Kirim Ulang Kode" untuk menerima OTP baru.');
        }

        // Check code match
        if ($request->input('otp') !== $storedOtp) {
            return redirect()->back()
                ->withErrors(['otp' => 'Kode OTP yang Anda masukkan tidak sesuai. Silakan periksa kembali.'])
                ->withInput();
        }

        // OTP is valid! Proceed with account registration
        $this->authService->register($pendingData);

        // Clean up registration session
        session()->forget([
            'registration_pending_data',
            'registration_otp',
            'otp_expires_at',
        ]);

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Nomor telepon Anda telah diverifikasi secara sah. Silakan masuk menggunakan email dan password Anda.');
    }

    /**
     * Resend a new OTP code to the pending registration phone.
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $pendingData = session('registration_pending_data');

        if (! $pendingData) {
            return redirect()->route('register')
                ->with('error', 'Sesi pendaftaran Anda telah berakhir. Silakan isi kembali form pendaftaran.');
        }

        // Generate new 6-digit OTP code
        $newOtpCode = (string) random_int(100000, 999999);

        session([
            'registration_otp' => $newOtpCode,
            'otp_expires_at'   => now()->addMinutes(5)->timestamp,
        ]);

        return redirect()->route('register.otp')
            ->with('success', 'Kode OTP baru telah berhasil dikirimkan ke nomor ' . ($pendingData['phone'] ?? '') . '.');
    }
}
