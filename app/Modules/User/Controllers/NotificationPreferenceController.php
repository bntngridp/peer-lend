<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationPreferenceController extends Controller
{
    /**
     * Update user notification preferences.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->profile ?? $user->profile()->create([
            'full_name' => 'User',
            'phone'     => '',
        ]);

        $settings = [
            'security_email'   => $request->boolean('security_email'),
            'security_push'    => $request->boolean('security_push'),
            'financial_email'  => $request->boolean('financial_email'),
            'financial_push'   => $request->boolean('financial_push'),
            'investment_email' => $request->boolean('investment_email'),
            'investment_push'  => $request->boolean('investment_push'),
        ];

        $profile->update([
            'notification_settings' => $settings,
        ]);

        return redirect()->route('profile.edit', ['tab' => 'notifications'])
            ->with('tab', 'notifications')
            ->with('success', 'Preferensi notifikasi berhasil diperbarui!');
    }
}
