<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\LoanCategory;
use App\Models\LoanRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoanRequest>
 */
class LoanRequestFactory extends Factory
{
    protected $model = LoanRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $idr = Currency::where('code', 'IDR')->first();

        return [
            'borrower_id'            => User::factory(),
            'category_id'            => LoanCategory::factory(),
            'amount'                 => fake()->randomFloat(2, 5000000, 100000000), // Rp 5jt - 100jt
            'interest_rate'          => fake()->randomFloat(2, 8.00, 24.00),
            'duration'               => fake()->randomElement([3, 6, 12, 24]),
            'tenor_type'             => 'monthly',
            'purpose'                => fake()->randomElement([
                'Business Expansion', 'Inventory Restocking', 'Medical Expenses', 'Education Fees', 'Renovation Capital'
            ]),
            'currency_id'            => $idr ? $idr->id : 1,
            'collateral_currency_id' => null,
            'collateral_amount'      => 0,
            'initial_ltv'            => 0,
            'current_ltv'            => 0,
            'liquidation_ltv'        => 80.00,
            'liquidation_price'      => 0,
            'description'            => fake()->text(1000),
            'risk_grade'             => fake()->randomElement(['A', 'B', 'C', 'D']),
            'status'                 => 'draft',
            'funded_percentage'      => 0.00,
            'approved_by'            => null,
            'approved_at'            => null,
            'funded_at'              => null,
            'disbursed_at'           => null,
        ];
    }

    /**
     * State for Crypto collateralized loans (DeFi-style)
     */
    public function cryptoBacked(string $cryptoCode = 'ETH'): static
    {
        return $this->state(function (array $attributes) use ($cryptoCode) {
            $crypto = Currency::where('code', $cryptoCode)->first();
            $amount = $attributes['amount'];
            
            // Assume 1 ETH = $3000 / Rp 45,000,000 for mock valuation
            // LTV initial 50%
            $ethPriceIdr = 45000000;
            $requiredCollateralValue = $amount * 2.0; // 50% LTV -> Collateral is 200% of Loan Amount
            $collateralQty = $requiredCollateralValue / $ethPriceIdr;

            return [
                'collateral_currency_id' => $crypto ? $crypto->id : null,
                'collateral_amount'      => $collateralQty,
                'initial_ltv'            => 50.00,
                'current_ltv'            => 50.00,
                'liquidation_ltv'        => 80.00,
                'liquidation_price'      => ($amount / 0.8) / $collateralQty,
            ];
        });
    }

    /**
     * State for Open Funding Loan applications
     */
    public function openFunding(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open_funding',
        ]);
    }
}
