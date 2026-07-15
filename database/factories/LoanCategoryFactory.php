<?php

namespace Database\Factories;

use App\Models\LoanCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoanCategory>
 */
class LoanCategoryFactory extends Factory
{
    protected $model = LoanCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => fake()->unique()->randomElement([
                'Business Expansion', 'Inventory Restocking', 'Medical Expenses', 'Education Fees', 'Renovation Capital'
            ]),
            'description' => fake()->sentence(),
        ];
    }
}
