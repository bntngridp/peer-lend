<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key'         => 'min_loan_amount',
                'value'       => '1000000',
                'description' => 'Minimum loan amount allowed Rp 1,000,000',
            ],
            [
                'key'         => 'max_loan_amount',
                'value'       => '500000000',
                'description' => 'Maximum loan amount allowed Rp 500,000,000',
            ],
            [
                'key'         => 'kyc_verification_required',
                'value'       => 'true',
                'description' => 'Force compulsory KYC verification for active operations',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Run down operation (reverse seed)
     */
    public function down(): void
    {
        Setting::truncate();
    }
}
