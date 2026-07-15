<?php

namespace Database\Seeders;

use App\Models\FeeConfiguration;
use Illuminate\Database\Seeder;

class FeeConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fees = [
            [
                'type'       => 'platform_fee',
                'value'      => 1.0000, // 1% from loan repayment
                'value_type' => 'percentage',
                'is_active'  => true,
            ],
            [
                'type'       => 'origination_fee',
                'value'      => 1.5000, // 1.5% admin charge when loan is disbursed
                'value_type' => 'percentage',
                'is_active'  => true,
            ],
            [
                'type'       => 'withdrawal_fee',
                'value'      => 6500.0000, // Flat IDR Rp 6,500 bank withdrawal fee
                'value_type' => 'fixed',
                'is_active'  => true,
            ],
            [
                'type'       => 'penalty_rate',
                'value'      => 0.1000, // 0.1% daily penalty for late payments
                'value_type' => 'percentage',
                'is_active'  => true,
            ],
        ];

        foreach ($fees as $fee) {
            FeeConfiguration::updateOrCreate(
                ['type' => $fee['type']],
                $fee
            );
        }
    }

    /**
     * Run down operation (reverse seed)
     */
    public function down(): void
    {
        FeeConfiguration::truncate();
    }
}
