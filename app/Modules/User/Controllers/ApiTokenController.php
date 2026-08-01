<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PersonalAccessToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApiTokenController extends Controller
{
    /**
     * Store a newly created API Token.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'permissions' => ['required', 'string', 'in:read,write'],
        ], [
            'name.required'        => 'Nama API Token wajib diisi.',
            'permissions.required' => 'Hak akses Token wajib dipilih.',
        ]);

        $user = Auth::user();

        // Limit user to max 10 active tokens
        if ($user->apiTokens()->count() >= 10) {
            return redirect()->route('profile.edit', ['tab' => 'security'])
                ->with('error', 'Batas maksimal 10 API Token telah tercapai.');
        }

        // Generate plain text token (e.g. lf_live_...)
        $plainToken = 'lf_live_' . Str::random(40);
        $hashedToken = hash('sha256', $plainToken);

        $token = $user->apiTokens()->create([
            'name'        => trim($request->name),
            'token'       => $hashedToken,
            'permissions' => $request->permissions === 'write' ? 'Read / Write' : 'Read Only',
        ]);

        return redirect()->route('profile.edit', ['tab' => 'security'])
            ->with('tab', 'security')
            ->with('success', "API Token '{$token->name}' berhasil dibuat!")
            ->with('generated_api_token', [
                'name'  => $token->name,
                'token' => $plainToken,
            ]);
    }

    /**
     * Revoke (delete) the specified API Token.
     */
    public function destroy(PersonalAccessToken $token): RedirectResponse
    {
        $user = Auth::user();

        if ($token->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $tokenName = $token->name;
        $token->delete();

        return redirect()->route('profile.edit', ['tab' => 'security'])
            ->with('tab', 'security')
            ->with('success', "API Token '{$tokenName}' telah berhasil dicabut.");
    }
}
