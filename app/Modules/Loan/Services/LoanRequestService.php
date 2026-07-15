<?php

namespace App\Modules\Loan\Services;

use App\Models\Currency;
use App\Models\LoanRequest;
use App\Models\User;
use App\Modules\Shared\Services\AuditLogService;
use App\Modules\Shared\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanRequestService
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Create a new loan request application.
     */
    public function createLoanRequest(User $borrower, array $data): LoanRequest
    {
        return DB::transaction(function () use ($borrower, $data) {
            $fiat = Currency::where('code', 'IDR')->firstOrFail();
            $collateralCurrencyId = $data['collateral_currency_id'] ?? null;
            
            $collateralAmount = 0;
            $initialLtv = 0;
            $liquidationLtv = 0;
            $liquidationPrice = 0;

            if ($collateralCurrencyId) {
                $collateralCurrency = Currency::findOrFail($collateralCurrencyId);
                
                // Get mock oracle price in IDR
                $priceInIdr = $this->getMockCryptoPrice($collateralCurrency->code);
                
                // Formula: LTV = (Loan Amount / Collateral Value) * 100
                // For a 50% initial LTV target:
                // Collateral Value = Loan Amount / 0.50 = Loan Amount * 2
                // Collateral Amount = Collateral Value / Crypto Price
                $loanAmount = $data['amount'];
                $requiredCollateralValue = bcmul($loanAmount, '2', 2);
                $collateralAmount = bcdiv($requiredCollateralValue, $priceInIdr, 8);

                $initialLtv = 50.00;
                $liquidationLtv = 80.00; // Liquidate if current LTV reaches 80%
                
                // Liquidation Price = (Loan Amount / 0.80) / Collateral Qty
                $maxLoanValueForLiquidation = bcdiv($loanAmount, '0.80', 2);
                $liquidationPrice = bcdiv($maxLoanValueForLiquidation, $collateralAmount, 8);
            }

            $loan = LoanRequest::create([
                'borrower_id'            => $borrower->id,
                'category_id'            => $data['category_id'],
                'amount'                 => $data['amount'],
                'interest_rate'          => $data['interest_rate'],
                'duration'               => $data['duration'],
                'tenor_type'             => 'monthly',
                'purpose'                => $data['purpose'],
                'currency_id'            => $fiat->id,
                'collateral_currency_id' => $collateralCurrencyId,
                'collateral_amount'      => $collateralAmount,
                'initial_ltv'            => $initialLtv,
                'current_ltv'            => $initialLtv,
                'liquidation_ltv'        => $liquidationLtv,
                'liquidation_price'      => $liquidationPrice,
                'description'            => $data['description'] ?? '',
                'risk_grade'             => $data['risk_grade'],
                'status'                 => LoanRequest::STATUS_PENDING,
                'funded_percentage'      => 0.00,
            ]);

            app(\App\Modules\Shared\Services\AuditLogService::class)->log(
                'loan_apply',
                LoanRequest::class,
                $loan->id,
                $borrower,
                ['amount' => $loan->amount]
            );

            return $loan;
        });
    }

    /**
     * Approve a pending loan request by an admin, pushing it into the marketplace.
     */
    public function approveLoanRequest(LoanRequest $loan, User $admin): LoanRequest
    {
        if ($loan->status !== LoanRequest::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'status' => ['Only pending loan requests can be approved.'],
            ]);
        }

        $loan->update([
            'status'      => LoanRequest::STATUS_OPEN_FUNDING,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        app(AuditLogService::class)->log(
            'loan_approve',
            LoanRequest::class,
            $loan->id,
            $admin,
            ['status' => $loan->status]
        );

        // Notify borrower that their loan is now open for funding in the marketplace
        $this->notificationService->notifyLoanOpenFunding(
            $loan->borrower,
            $loan->id,
            (string)$loan->amount
        );

        return $loan;
    }

    /**
     * Mock oracle price feed for collateral crypto assets.
     */
    private function getMockCryptoPrice(string $code): string
    {
        return match ($code) {
            'BTC'   => '900000000', // Rp 900 Million
            'ETH'   => '45000000',  // Rp 45 Million
            'USDT'  => '16000',     // Rp 16,000
            default => '1000000',
        };
    }
}
