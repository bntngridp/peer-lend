<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemPreferenceController extends Controller
{
    /**
     * Quick theme toggle via AJAX — only updates color_theme.
     * Called by the navbar ☀️/🌙 button and profile page theme cards.
     */
    public function toggleTheme(Request $request): JsonResponse
    {
        $request->validate([
            'theme' => ['required', 'string', 'in:light,dark'],
        ]);

        $user    = Auth::user();
        $profile = $user->profile ?? $user->profile()->create([
            'full_name' => $user->name ?? 'User',
            'phone'     => '',
        ]);

        // Merge theme into existing preferences (don't overwrite other fields)
        $current = $profile->system_preferences ?? [];
        $current['color_theme'] = $request->theme;

        $profile->update([
            'system_preferences' => $current,
        ]);

        return response()->json([
            'status'  => 'success',
            'theme'   => $request->theme,
            'message' => 'Theme updated successfully',
        ]);
    }

    /**
     * Full system preferences update (form PUT from profile settings tab).
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'color_theme'  => ['required', 'string', 'in:light,dark'],
            'data_density' => ['required', 'string', 'in:comfortable,compact'],
        ]);

        $user    = Auth::user();
        $profile = $user->profile ?? $user->profile()->create([
            'full_name' => $user->name ?? 'User',
            'phone'     => '',
        ]);

        $settings = [
            'color_theme'               => $request->color_theme,
            'data_density'              => $request->data_density,
            'public_profile'            => $request->boolean('public_profile'),
            'data_sharing'              => $request->boolean('data_sharing'),
            'third_party_integrations'  => $request->boolean('third_party_integrations'),
        ];

        $profile->update([
            'system_preferences' => $settings,
        ]);

        return redirect()->route('profile.edit', ['tab' => 'system'])
            ->with('tab', 'system')
            ->with('success', 'Pengaturan sistem & preferensi tampilan berhasil disimpan!');
    }
}
