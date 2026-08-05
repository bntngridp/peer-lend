<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Requests\UpdateProfileRequest;
use App\Modules\User\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profileService
    ) {}

    /**
     * Show the profile edit form with real active sessions.
     */
    public function edit(): View
    {
        $user = Auth::user();
        $currentSessionId = session()->getId();

        $activeSessions = [];

        try {
            $dbSessions = \Illuminate\Support\Facades\DB::table('sessions')
                ->where('user_id', $user->id)
                ->orderBy('last_activity', 'desc')
                ->get();

            foreach ($dbSessions as $s) {
                $parsed = self::parseUserAgent($s->user_agent);
                $isCurrent = ($s->id === $currentSessionId);
                
                $ip = $s->ip_address ?? request()->ip();
                $location = ($ip === '127.0.0.1' || $ip === '::1') ? 'Localhost' : $ip;

                $activeSessions[] = [
                    'id'            => $s->id,
                    'device'        => $parsed['platform'] . ' • ' . $parsed['browser'],
                    'ip_address'    => $ip,
                    'location_info' => $location . ' • ' . $ip,
                    'is_current'    => $isCurrent,
                    'last_active'   => \Carbon\Carbon::createFromTimestamp($s->last_activity)->diffForHumans(),
                ];
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        if (empty($activeSessions)) {
            $currentParsed = self::parseUserAgent(request()->userAgent());
            $ip = request()->ip();
            $location = ($ip === '127.0.0.1' || $ip === '::1') ? 'Localhost' : $ip;

            $activeSessions[] = [
                'id'            => $currentSessionId,
                'device'        => $currentParsed['platform'] . ' • ' . $currentParsed['browser'],
                'ip_address'    => $ip,
                'location_info' => $location . ' • ' . $ip,
                'is_current'    => true,
                'last_active'   => __('Just now'),
            ];
        }

        return view('profile.edit', compact('user', 'activeSessions'));
    }

    /**
     * Update the profile data.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        
        $this->profileService->updateProfile($user, $request->validated());

        if ($request->hasFile('avatar')) {
            $this->profileService->updateAvatar($user, $request->file('avatar'));
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user password.
     */
    public function updatePassword(\Illuminate\Http\Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.min'              => 'Password baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = Auth::user();

        // 1. Verify current password
        if (! \Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()
                ->withInput()
                ->with('tab', 'security')
                ->withErrors(['current_password' => 'Password saat ini yang Anda masukkan tidak sesuai.']);
        }

        // 2. Prevent reusing same password
        if (\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return back()
                ->withInput()
                ->with('tab', 'security')
                ->withErrors(['password' => 'Password baru tidak boleh sama dengan password lama Anda.']);
        }

        // 3. Update password
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit', ['tab' => 'security'])
            ->with('success', 'Password akun Anda telah berhasil diperbarui.');
    }

    /**
     * Revoke all other active sessions for the authenticated user.
     */
    public function revokeOtherSessions(\Illuminate\Http\Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'Password wajib diisi untuk mengonfirmasi pencabutan sesi.',
        ]);

        $user = Auth::user();

        if (! \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return back()
                ->withInput()
                ->with('tab', 'security')
                ->withErrors(['session_password' => 'Password yang Anda masukkan tidak sesuai.']);
        }

        Auth::logoutOtherDevices($request->password);

        return redirect()->route('profile.edit', ['tab' => 'security'])
            ->with('success', 'Semua sesi perangkat lain berhasil dikeluarkan!');
    }

    /**
     * Revoke a single active session.
     */
    public function revokeSession(\Illuminate\Http\Request $request, string $sessionId): RedirectResponse
    {
        $user = Auth::user();

        if ($sessionId === session()->getId()) {
            return back()
                ->with('tab', 'security')
                ->withErrors(['session' => 'Anda tidak dapat mencabut sesi yang sedang digunakan saat ini.']);
        }

        try {
            \Illuminate\Support\Facades\DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', $sessionId)
                ->delete();
        } catch (\Throwable $e) {
            // Silence
        }

        return redirect()->route('profile.edit', ['tab' => 'security'])
            ->with('success', 'Sesi perangkat berhasil dicabut!');
    }

    /**
     * Parse User-Agent into Platform and Browser name.
     */
    public static function parseUserAgent(?string $userAgent): array
    {
        if (!$userAgent) {
            return [
                'platform' => 'Perangkat Tidak Dikenal',
                'browser'  => 'Browser Tidak Dikenal',
            ];
        }

        $platform = 'Desktop';
        if (preg_match('/Macintosh|Mac OS X/i', $userAgent)) {
            $platform = 'Mac OS';
        } elseif (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            $platform = 'iOS';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $platform = 'Android';
        } elseif (preg_match('/Windows/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $platform = 'Linux';
        }

        $browser = 'Browser';
        if (preg_match('/Chrome/i', $userAgent) && !preg_match('/Edg|OPR/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent) && !preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Edg/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            $browser = 'Opera';
        } elseif (preg_match('/LendFlow/i', $userAgent)) {
            $browser = 'LendFlow App';
        }

        return [
            'platform' => $platform,
            'browser'  => $browser,
        ];
    }
}
