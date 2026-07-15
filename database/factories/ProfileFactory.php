<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'full_name'      => fake()->name(),
            'phone'          => fake()->unique()->numerify('08##########'), // clean Indonesian phone format
            'avatar_path'    => null,
            'address'        => fake()->streetAddress(),
            'city'           => fake()->city(),
            'province'       => fake()->state(),
            'occupation'     => fake()->jobTitle(),
            'monthly_income' => fake()->randomFloat(2, 3000000, 75000000), // Rp 3jt - 75jt
        ];
    }
}
