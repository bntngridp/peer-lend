<?php

namespace Database\Seeders;

use App\Models\InterestRate;
use Illuminate\Database\Seeder;

class InterestRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rates = [
            [
                'risk_grade' => 'A',
                'min_rate'   => 8.00,
                'max_rate'   => 10.00,
            ],
            [
                'risk_grade' => 'B',
                'min_rate'   => 11.00,
                'max_rate'   => 14.00,
            ],
            [
                'risk_grade' => 'C',
                'min_rate'   => 15.00,
                'max_rate'   => 18.00,
            ],
            [
                'risk_grade' => 'D',
                'min_rate'   => 19.00,
                'max_rate'   => 24.00,
            ],
        ];

        foreach ($rates as $rate) {
            InterestRate::updateOrCreate(
                ['risk_grade' => $rate['risk_grade']],
                $rate
            );
        }
    }

    /**
     * Run down operation (reverse seed)
     */
    public function down(): void
    {
        InterestRate::truncate();
    }
}
