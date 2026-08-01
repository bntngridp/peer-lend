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
     * Show the profile edit form.
     */
    public function edit(): View
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
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
}
