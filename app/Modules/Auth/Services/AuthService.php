<?php

namespace App\Modules\Auth\Services;

use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Currency;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new borrower or lender.
     *
     * Creates the user record, their profile, assigns the requested role,
     * and initialises a default IDR wallet — all inside one atomic DB transaction.
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // 1. Create user account
            $user = User::create([
                'email'             => $data['email'],
                'password'          => Hash::make($data['password']),
                'email_verified_at' => null,
                'is_active'         => true,
            ]);

            // 2. Create profile
            Profile::create([
                'user_id'   => $user->id,
                'full_name' => $data['full_name'],
                'phone'     => $data['phone'],
            ]);

            // 3. Assign role (borrower / lender)
            $role = Role::where('name', $data['role'])->firstOrFail();
            $user->roles()->attach($role->id);

            // 4. Initialise IDR wallet
            $idr = Currency::where('code', 'IDR')->first();
            if ($idr) {
                Wallet::create([
                    'user_id'           => $user->id,
                    'currency_id'       => $idr->id,
                    'available_balance' => 0,
                    'hold_balance'      => 0,
                ]);
            }

            // 5. Fire registered event (sends email verification)
            event(new Registered($user));

            return $user;
        });
    }

    /**
     * Attempt to log the user in.
     *
     * @throws ValidationException when credentials are wrong or account is inactive.
     */
    public function login(array $credentials, bool $remember = false): User
    {
        // 1. Check if user with this email exists
        $user = User::where('email', $credentials['email'])->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['Email yang Anda masukkan tidak terdaftar. Silakan periksa kembali email Anda atau buat akun baru.'],
            ]);
        }

        // 2. Check if password matches
        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            throw ValidationException::withMessages([
                'password' => ['Password yang Anda masukkan salah. Silakan periksa kembali password Anda atau klik Lupa Password.'],
            ]);
        }

        // 3. Check if account is active
        if (! $user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => ['Akun Anda sedang dinonaktifkan. Silakan hubungi dukungan pelanggan LendFlow.'],
            ]);
        }

        return $user;
    }

    /**
     * Log the current user out and invalidate their session.
     */
    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    /**
     * Send the password reset link to the user's email.
     */
    public function sendResetLink(string $email): string
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return $status;
    }

    /**
     * Reset the user's password using the provided token.
     */
    public function resetPassword(array $data): string
    {
        $status = Password::reset(
            [
                'email'                 => $data['email'],
                'password'              => $data['password'],
                'password_confirmation' => $data['password_confirmation'],
                'token'                 => $data['token'],
            ],
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return $status;
    }
}
