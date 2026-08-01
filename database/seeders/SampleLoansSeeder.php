<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\LoanCategory;
use App\Models\LoanFunding;
use App\Models\LoanInstallment;
use App\Models\LoanRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleLoansSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('testing')) {
            return;
        }
        // 1. Ensure Loan Categories
        $categories = [
            'Working Capital'           => 'Short-term liquidity for business operations',
            'Business Expansion'         => 'Capital for opening new branches or expanding product lines',
            'Supply Chain Financing'    => 'Vendor financing for purchasing inventory and raw materials',
            'Equipment Procurement'    => 'Capital expenditure for machinery and hardware',
            'Crypto Collateralized Loan'=> 'Institutional loans backed by digital asset collateral (BTC/ETH/USDT)',
        ];

        $categoryModels = [];
        foreach ($categories as $name => $desc) {
            $categoryModels[$name] = LoanCategory::firstOrCreate(
                ['name' => $name],
                ['description' => $desc]
            );
        }

        // 2. Fetch required reference models
        $idrCurrency = Currency::where('code', 'IDR')->first();
        $btcCurrency = Currency::where('code', 'BTC')->first();
        $ethCurrency = Currency::where('code', 'ETH')->first();
        $usdtCurrency = Currency::where('code', 'USDT')->first();

        $borrower1 = User::where('email', 'borrower1@lendflow.com')->first();
        $borrower2 = User::where('email', 'borrower2@lendflow.com')->first();
        $lender1   = User::where('email', 'lender1@lendflow.com')->first();
        $lender2   = User::where('email', 'lender2@lendflow.com')->first();

        if (!$borrower1 || !$borrower2 || !$lender1 || !$lender2) {
            return;
        }

        // 3. Seed Open Funding Loans (Marketplace Opportunities)
        $marketplaceListings = [
            [
                'borrower_id'       => $borrower1->id,
                'category_id'       => $categoryModels['Business Expansion']->id,
                'purpose'           => 'Acme Corp Regional Logistics Center Expansion',
                'amount'            => 250000000,
                'interest_rate'     => 12.50,
                'duration'          => 24,
                'tenor_type'        => 'month',
                'currency_id'       => $idrCurrency->id,
                'risk_grade'        => 'A',
                'funded_percentage' => 75.00,
                'status'            => LoanRequest::STATUS_OPEN_FUNDING,
                'description'       => 'Funding regional distribution warehouse in Surabaya to expand operations.',
                'funded_amount'     => 187500000,
            ],
            [
                'borrower_id'       => $borrower2->id,
                'category_id'       => $categoryModels['Supply Chain Financing']->id,
                'purpose'           => 'Global Logistics Ltd Fleet Modernization',
                'amount'            => 1200000000,
                'interest_rate'     => 9.80,
                'duration'          => 36,
                'tenor_type'        => 'month',
                'currency_id'       => $idrCurrency->id,
                'risk_grade'        => 'B',
                'funded_percentage' => 40.00,
                'status'            => LoanRequest::STATUS_OPEN_FUNDING,
                'description'       => 'Procuring 10 eco-friendly electric delivery trucks.',
                'funded_amount'     => 480000000,
            ],
            [
                'borrower_id'       => $borrower1->id,
                'category_id'       => $categoryModels['Working Capital']->id,
                'purpose'           => 'Solaris Energy Solar Farm Grid Expansion',
                'amount'            => 500000000,
                'interest_rate'     => 11.20,
                'duration'          => 12,
                'tenor_type'        => 'month',
                'currency_id'       => $idrCurrency->id,
                'risk_grade'        => 'B',
                'funded_percentage' => 90.00,
                'status'            => LoanRequest::STATUS_OPEN_FUNDING,
                'description'       => 'Working capital for commercial solar panel installation contracts.',
                'funded_amount'     => 450000000,
            ],
            [
                'borrower_id'       => $borrower2->id,
                'category_id'       => $categoryModels['Equipment Procurement']->id,
                'purpose'           => 'TechNova Solutions AI Cluster Upgrades',
                'amount'            => 150000000,
                'interest_rate'     => 14.50,
                'duration'          => 18,
                'tenor_type'        => 'month',
                'currency_id'       => $idrCurrency->id,
                'risk_grade'        => 'C',
                'funded_percentage' => 15.00,
                'status'            => LoanRequest::STATUS_OPEN_FUNDING,
                'description'       => 'Purchasing high-performance GPU servers for enterprise AI workloads.',
                'funded_amount'     => 22500000,
            ],
        ];

        foreach ($marketplaceListings as $item) {
            $fundedAmount = $item['funded_amount'];
            unset($item['funded_amount']);

            $loan = LoanRequest::updateOrCreate(
                ['purpose' => $item['purpose']],
                $item
            );

            // Seed investor fundings
            if ($fundedAmount > 0) {
                $amount1 = $fundedAmount * 0.6;
                $amount2 = $fundedAmount * 0.4;
                LoanFunding::updateOrCreate(
                    ['loan_id' => $loan->id, 'lender_id' => $lender1->id],
                    [
                        'amount'     => $amount1,
                        'percentage' => ($amount1 / $loan->amount) * 100,
                        'status'     => 'confirmed',
                    ]
                );
                LoanFunding::updateOrCreate(
                    ['loan_id' => $loan->id, 'lender_id' => $lender2->id],
                    [
                        'amount'     => $amount2,
                        'percentage' => ($amount2 / $loan->amount) * 100,
                        'status'     => 'confirmed',
                    ]
                );
            }
        }

        // 4. Seed Crypto Collateral Loans (Active Loans)
        $cryptoLoans = [
            [
                'borrower_id'            => $borrower1->id,
                'category_id'            => $categoryModels['Crypto Collateralized Loan']->id,
                'purpose'                => 'Genesis Block Partners ETH Secured Yield',
                'amount'                 => 350000000,
                'interest_rate'          => 10.50,
                'duration'               => 12,
                'tenor_type'             => 'month',
                'currency_id'            => $idrCurrency->id,
                'collateral_currency_id' => $ethCurrency?->id,
                'collateral_amount'      => 150.00000000,
                'initial_ltv'            => 50.00,
                'current_ltv'            => 82.40,
                'liquidation_ltv'        => 80.00,
                'liquidation_price'      => 42000000.00,
                'risk_grade'             => 'A',
                'funded_percentage'      => 100.00,
                'status'                 => LoanRequest::STATUS_ACTIVE,
                'description'            => 'ETH collateralized institutional loan with live LTV tracking.',
            ],
            [
                'borrower_id'            => $borrower2->id,
                'category_id'            => $categoryModels['Crypto Collateralized Loan']->id,
                'purpose'                => 'Apex Capital Ltd BTC Collateral Facility',
                'amount'                 => 750000000,
                'interest_rate'          => 8.90,
                'duration'               => 24,
                'tenor_type'             => 'month',
                'currency_id'            => $idrCurrency->id,
                'collateral_currency_id' => $btcCurrency?->id,
                'collateral_amount'      => 12.50000000,
                'initial_ltv'            => 45.00,
                'current_ltv'            => 76.10,
                'liquidation_ltv'        => 75.00,
                'liquidation_price'      => 880000000.00,
                'risk_grade'             => 'B',
                'funded_percentage'      => 100.00,
                'status'                 => LoanRequest::STATUS_ACTIVE,
                'description'            => 'Bitcoin backed revolving credit line.',
            ],
            [
                'borrower_id'            => $borrower1->id,
                'category_id'            => $categoryModels['Crypto Collateralized Loan']->id,
                'purpose'                => 'Meridian Yield Fund BTC Credit Line',
                'amount'                 => 600000000,
                'interest_rate'          => 9.20,
                'duration'               => 18,
                'tenor_type'             => 'month',
                'currency_id'            => $idrCurrency->id,
                'collateral_currency_id' => $btcCurrency?->id,
                'collateral_amount'      => 45.00000000,
                'initial_ltv'            => 40.00,
                'current_ltv'            => 75.50,
                'liquidation_ltv'        => 75.00,
                'liquidation_price'      => 860000000.00,
                'risk_grade'             => 'B',
                'funded_percentage'      => 100.00,
                'status'                 => LoanRequest::STATUS_ACTIVE,
                'description'            => 'BTC backed credit line with margin call alerts.',
            ],
        ];

        foreach ($cryptoLoans as $item) {
            $loan = LoanRequest::updateOrCreate(
                ['purpose' => $item['purpose']],
                $item
            );

            // Generate active installments
            $monthlyPrincipal = $loan->amount / $loan->duration;
            $monthlyInterest  = ($loan->amount * ($loan->interest_rate / 100)) / 12;

            for ($i = 1; $i <= min(6, $loan->duration); $i++) {
                LoanInstallment::updateOrCreate(
                    [
                        'loan_id'            => $loan->id,
                        'installment_number' => $i,
                    ],
                    [
                        'due_date'          => now()->addMonths($i - 2),
                        'principal_amount'  => $monthlyPrincipal,
                        'interest_amount'   => $monthlyInterest,
                        'total_amount'      => $monthlyPrincipal + $monthlyInterest,
                        'status'            => $i === 1 ? 'paid' : ($i === 2 ? 'pending' : 'upcoming'),
                        'paid_at'           => $i === 1 ? now()->subMonth() : null,
                    ]
                );
            }
        }
    }
}
