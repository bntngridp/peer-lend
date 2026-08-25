<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\KYC;
use App\Models\KYCDocument;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Generates 2 dummy accounts per role (10 accounts total across 5 roles).
     */
    public function run(): void
    {
        $idrCurrency = Currency::where('code', 'IDR')->first();
        if (!$idrCurrency) {
            $idrCurrency = Currency::create(['code' => 'IDR', 'name' => 'Indonesian Rupiah', 'symbol' => 'Rp']);
        }

        $dummyAccounts = [
            // 1. ADMIN ROLES
            [
                'email'          => 'admin1@lendflow.com',
                'password'       => 'password123',
                'role'           => 'admin',
                'full_name'      => 'Primary System Admin',
                'phone'          => '081100000001',
                'balance'        => 100000000,
                'monthly_income' => 30000000,
                'kyc'            => 'approved',
            ],
            [
                'email'          => 'admin2@lendflow.com',
                'password'       => 'password123',
                'role'           => 'admin',
                'full_name'      => 'Compliance Officer Admin',
                'phone'          => '081100000002',
                'balance'        => 50000000,
                'monthly_income' => 25000000,
                'kyc'            => 'approved',
            ],

            // 2. BORROWER ROLES
            [
                'email'          => 'borrower1@lendflow.com',
                'password'       => 'password123',
                'role'           => 'borrower',
                'full_name'      => 'Budi Santoso',
                'phone'          => '081200000001',
                'balance'        => 15000000,
                'monthly_income' => 28000000,
                'kyc'            => 'approved',
            ],
            [
                'email'          => 'borrower2@lendflow.com',
                'password'       => 'password123',
                'role'           => 'borrower',
                'full_name'      => 'Siti Aminah',
                'phone'          => '081200000002',
                'balance'        => 8000000,
                'monthly_income' => 35000000,
                'kyc'            => 'approved',
            ],

            // 3. LENDER ROLES
            [
                'email'          => 'lender1@lendflow.com',
                'password'       => 'password123',
                'role'           => 'lender',
                'full_name'      => 'Rizky Pratama',
                'phone'          => '081300000001',
                'balance'        => 250000000,
                'monthly_income' => 75000000,
                'kyc'            => 'approved',
            ],
            [
                'email'          => 'lender2@lendflow.com',
                'password'       => 'password123',
                'role'           => 'lender',
                'full_name'      => 'Dewi Lestari',
                'phone'          => '081300000002',
                'balance'        => 500000000,
                'monthly_income' => 120000000,
                'kyc'            => 'approved',
            ],

            // 4. CUSTOMER SERVICE ROLES
            [
                'email'          => 'cs1@lendflow.com',
                'password'       => 'password123',
                'role'           => 'customer_service',
                'full_name'      => 'Andi CS Support 1',
                'phone'          => '081400000001',
                'balance'        => 0,
                'monthly_income' => 15000000,
                'kyc'            => 'approved',
            ],
            [
                'email'          => 'cs2@lendflow.com',
                'password'       => 'password123',
                'role'           => 'customer_service',
                'full_name'      => 'Maya CS Support 2',
                'phone'          => '081400000002',
                'balance'        => 0,
                'monthly_income' => 15000000,
                'kyc'            => 'approved',
            ],

            // 5. COLLECTION OFFICER ROLES
            [
                'email'          => 'collector1@lendflow.com',
                'password'       => 'password123',
                'role'           => 'collection_officer',
                'full_name'      => 'Eko Collection Officer 1',
                'phone'          => '081500000001',
                'balance'        => 0,
                'monthly_income' => 18000000,
                'kyc'            => 'approved',
            ],
            [
                'email'          => 'collector2@lendflow.com',
                'password'       => 'password123',
                'role'           => 'collection_officer',
                'full_name'      => 'Fajar Collection Officer 2',
                'phone'          => '081500000002',
                'balance'        => 0,
                'monthly_income' => 18000000,
                'kyc'            => 'approved',
            ],
        ];

        foreach ($dummyAccounts as $acc) {
            // Create or update user
            $user = User::updateOrCreate(
                ['email' => $acc['email']],
                [
                    'password'          => Hash::make($acc['password']),
                    'email_verified_at' => now(),
                    'is_active'         => true,
                ]
            );

            // Sync role
            $role = Role::where('name', $acc['role'])->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            }

            // Create or update profile
            Profile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'full_name'      => $acc['full_name'],
                    'phone'          => $acc['phone'],
                    'address'        => 'Jl. Jendral Sudirman No. 45, Jakarta Pusat',
                    'city'           => 'Jakarta',
                    'province'       => 'DKI Jakarta',
                    'occupation'     => ucfirst(str_replace('_', ' ', $acc['role'])),
                    'monthly_income' => $acc['monthly_income'] ?? null,
                ]
            );

            // Create or update wallet
            Wallet::updateOrCreate(
                [
                    'user_id'     => $user->id,
                    'currency_id' => $idrCurrency->id,
                ],
                [
                    'available_balance' => $acc['balance'],
                    'hold_balance'      => 0,
                ]
            );

            // Create or update approved KYC status
            $kyc = KYC::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nik'             => '3171' . rand(1000000000, 9999999999),
                    'status'          => $acc['kyc'],
                    'rejected_reason' => null,
                    'reviewed_at'     => now(),
                ]
            );

            // Attach KYC documents
            KYCDocument::updateOrCreate(
                ['kyc_id' => $kyc->id, 'type' => 'ktp'],
                ['file_path' => 'kyc/dummy_ktp.jpg', 'storage_driver' => 'local', 'verified_at' => now()]
            );
            KYCDocument::updateOrCreate(
                ['kyc_id' => $kyc->id, 'type' => 'selfie'],
                ['file_path' => 'kyc/dummy_selfie.jpg', 'storage_driver' => 'local', 'verified_at' => now()]
            );
        }
    }
}
