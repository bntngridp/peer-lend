<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user to Google's OAuth authentication page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google OAuth service.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = DB::transaction(function () use ($googleUser) {
                // 1. Check if user with this google_id already exists
                $existingUser = User::where('google_id', $googleUser->getId())->first();

                if ($existingUser) {
                    // Update avatar if changed
                    if ($googleUser->getAvatar() && $existingUser->avatar !== $googleUser->getAvatar()) {
                        $existingUser->update(['avatar' => $googleUser->getAvatar()]);
                    }
                    return $existingUser;
                }

                // 2. Check if user with this email already exists
                $userByEmail = User::where('email', $googleUser->getEmail())->first();

                if ($userByEmail) {
                    // Link Google account to existing user
                    $userByEmail->update([
                        'google_id' => $googleUser->getId(),
                        'avatar'    => $userByEmail->avatar ?? $googleUser->getAvatar(),
                    ]);
                    return $userByEmail;
                }

                // 3. Create new user account from Google profile
                $newUser = User::create([
                    'email'             => $googleUser->getEmail(),
                    'google_id'          => $googleUser->getId(),
                    'avatar'            => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password'          => null,
                    'is_active'         => true,
                ]);

                // Create user profile
                Profile::create([
                    'user_id'   => $newUser->id,
                    'full_name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'LendFlow Member',
                    'phone'     => null,
                ]);

                // Attach default role (lender)
                $lenderRole = Role::where('name', 'lender')->first() ?? Role::first();
                if ($lenderRole) {
                    $newUser->roles()->attach($lenderRole->id);
                }

                // Create default IDR wallet
                $idr = Currency::where('code', 'IDR')->first();
                if ($idr) {
                    Wallet::create([
                        'user_id'           => $newUser->id,
                        'currency_id'       => $idr->id,
                        'available_balance' => 0,
                        'hold_balance'      => 0,
                    ]);
                }

                return $newUser;
            });

            if (! $user->is_active) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your account has been suspended. Please contact support.']);
            }

            // Log user in and regenerate session
            Auth::login($user, true);
            request()->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Successfully logged in with Google!');

        } catch (Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Failed to authenticate with Google. Please try again. (' . $e->getMessage() . ')']);
        }
    }
}
