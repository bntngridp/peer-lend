<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Wallet>
 */
class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'           => User::factory(),
            'currency_id'       => Currency::inRandomOrder()->first()?->id ?? 1,
            'available_balance' => fake()->randomFloat(8, 1000000, 50000000), // 1 million to 50 million
            'hold_balance'      => 0,
        ];
    }
}
