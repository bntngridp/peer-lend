<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ─── 1. Create Admin Account ──────────────────────────────────
        $admin = User::updateOrCreate(
            ['email' => 'admin@peerlend.com'],
            [
                'password'          => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_active'         => true,
            ]
        );

        // Assign Admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $admin->roles()->sync([$adminRole->id]);
        }

        // ─── 2. Create Profile ─────────────────────────────────────────
        Profile::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'full_name'  => 'System Administrator',
                'phone'      => '081234567890',
                'address'    => 'Main Office Building Jakarta',
                'city'       => 'Jakarta',
                'province'   => 'DKI Jakarta',
                'occupation' => 'Fintech Auditor',
            ]
        );

        // ─── 3. Initialize Wallets ─────────────────────────────────────
        $currencies = Currency::all();
        foreach ($currencies as $currency) {
            Wallet::updateOrCreate(
                [
                    'user_id'     => $admin->id,
                    'currency_id' => $currency->id,
                ],
                [
                    'available_balance' => 0,
                    'hold_balance'      => 0,
                ]
            );
        }
    }

    /**
     * Run down operation (reverse seed)
     */
    public function down(): void
    {
        $admin = User::where('email', 'admin@peerlend.com')->first();
        if ($admin) {
            $admin->profile()->delete();
            $admin->wallets()->delete();
            $admin->roles()->detach();
            $admin->delete();
        }
    }
}
