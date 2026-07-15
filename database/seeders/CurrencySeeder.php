<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code'           => 'IDR',
                'name'           => 'Indonesian Rupiah',
                'type'           => 'fiat',
                'decimal_places' => 2,
                'is_active'      => true,
            ],
            [
                'code'           => 'USDT',
                'name'           => 'Tether USD',
                'type'           => 'crypto',
                'decimal_places' => 8,
                'is_active'      => true,
            ],
            [
                'code'           => 'BTC',
                'name'           => 'Bitcoin',
                'type'           => 'crypto',
                'decimal_places' => 8,
                'is_active'      => true,
            ],
            [
                'code'           => 'ETH',
                'name'           => 'Ethereum',
                'type'           => 'crypto',
                'decimal_places' => 8,
                'is_active'      => true,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }

    /**
     * Run down operation (reverse seed)
     */
    public function down(): void
    {
        Currency::truncate();
    }
}
