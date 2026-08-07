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
        private readonly NotificationService  $notificationService,
        private readonly CreditScoringService $creditScoringService,
        private readonly LiquidationService   $liquidationService,
    ) {}

    /**
     * Create a new loan request application.
     * Risk grade and interest rate are auto-assigned by the Credit Scoring Engine.
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
                
                // Get live oracle price from CoinGecko (with fallback to mock feed)
                $priceInIdr = $this->liquidationService->getCryptoPrice($collateralCurrency->code);
                
                // Formula: LTV = (Loan Amount / Collateral Value) * 100
                // For a 50% initial LTV target:
                // Collateral Value = Loan Amount / 0.50 = Loan Amount * 2
                // Collateral Amount = Collateral Value / Crypto Price
                $loanAmount = $data['amount'];
                $requiredCollateralValue = bcmul($loanAmount, '2', 2);
                $collateralAmount = bcdiv($requiredCollateralValue, $priceInIdr, 8);

                // Lock collateral on Borrower's Wallet
                $borrowerWallet = \App\Models\Wallet::lockForUpdate()->where([
                    'user_id'     => $borrower->id,
                    'currency_id' => $collateralCurrencyId,
                ])->first();

                if (!$borrowerWallet || bccomp($borrowerWallet->available_balance, $collateralAmount, 8) < 0) {
                    throw ValidationException::withMessages([
                        'collateral_currency_id' => [__('Insufficient :code balance in your wallet to lock required collateral (:req :code).', [
                            'code' => $collateralCurrency->code,
                            'req'  => number_format($collateralAmount, $collateralCurrency->decimal_places),
                        ])],
                    ]);
                }

                // Move from available_balance to hold_balance
                $borrowerWallet->update([
                    'available_balance' => bcsub($borrowerWallet->available_balance, $collateralAmount, 8),
                    'hold_balance'      => bcadd($borrowerWallet->hold_balance, $collateralAmount, 8),
                ]);

                \App\Models\WalletTransaction::create([
                    'wallet_id'      => $borrowerWallet->id,
                    'type'           => 'hold',
                    'amount'         => $collateralAmount,
                    'balance_before' => bcadd($borrowerWallet->available_balance, $collateralAmount, 8),
                    'balance_after'  => $borrowerWallet->available_balance,
                    'description'    => "Locked collateral for loan request",
                ]);

                $initialLtv = 50.00;
                $liquidationLtv = 80.00; // Liquidate if current LTV reaches 80%
                
                // Liquidation Price = (Loan Amount / 0.80) / Collateral Qty
                $maxLoanValueForLiquidation = bcdiv($loanAmount, '0.80', 2);
                $liquidationPrice = bcdiv($maxLoanValueForLiquidation, $collateralAmount, 8);
            }

            // ── Auto Credit Scoring ──────────────────────────────────────────
            $scoring = $this->creditScoringService->calculateScore($borrower);
            $riskGrade    = $scoring['grade'];
            $interestRate = $scoring['interest_rate'];

            $loan = LoanRequest::create([
                'borrower_id'            => $borrower->id,
                'category_id'            => $data['category_id'],
                'amount'                 => $data['amount'],
                'interest_rate'          => $interestRate,
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
                'risk_grade'             => $riskGrade,
                'status'                 => LoanRequest::STATUS_PENDING,
                'funded_percentage'      => 0.00,
            ]);

            app(\App\Modules\Shared\Services\AuditLogService::class)->log(
                'loan_apply',
                LoanRequest::class,
                $loan->id,
                $borrower,
                [
                    'amount'        => $loan->amount,
                    'credit_score'  => $scoring['score'],
                    'risk_grade'    => $riskGrade,
                    'interest_rate' => $interestRate,
                ]
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
     * Reject a pending loan request application by an admin.
     */
    public function rejectLoanRequest(LoanRequest $loan, User $admin, ?string $reason = null): LoanRequest
    {
        if ($loan->status !== LoanRequest::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'status' => ['Only pending loan requests can be rejected.'],
            ]);
        }

        // Release held collateral back to borrower's available balance if crypto collateral was locked
        if ($loan->collateral_currency_id && bccomp((string)$loan->collateral_amount, '0', 8) > 0) {
            $borrowerWallet = \App\Models\Wallet::lockForUpdate()->where([
                'user_id'     => $loan->borrower_id,
                'currency_id' => $loan->collateral_currency_id,
            ])->first();

            if ($borrowerWallet && bccomp($borrowerWallet->hold_balance, (string)$loan->collateral_amount, 8) >= 0) {
                $borrowerWallet->update([
                    'hold_balance'      => bcsub($borrowerWallet->hold_balance, (string)$loan->collateral_amount, 8),
                    'available_balance' => bcadd($borrowerWallet->available_balance, (string)$loan->collateral_amount, 8),
                ]);

                \App\Models\WalletTransaction::create([
                    'wallet_id'      => $borrowerWallet->id,
                    'type'           => 'release_hold',
                    'amount'         => $loan->collateral_amount,
                    'balance_before' => bcsub($borrowerWallet->available_balance, (string)$loan->collateral_amount, 8),
                    'balance_after'  => $borrowerWallet->available_balance,
                    'description'    => "Unlocked collateral for rejected loan request #{$loan->id}",
                ]);
            }
        }

        $loan->update([
            'status' => 'rejected',
        ]);

        app(AuditLogService::class)->log(
            'loan_reject',
            LoanRequest::class,
            $loan->id,
            $admin,
            ['status' => $loan->status, 'reason' => $reason]
        );

        $this->notificationService->send(
            $loan->borrower,
            'loan_rejected',
            __('Loan Application Declined'),
            __('Your loan application #:id for Rp :amount has been declined.', [
                'id'     => strtoupper(substr($loan->id, 0, 8)),
                'amount' => number_format($loan->amount, 0, ',', '.'),
            ]) . ($reason ? ' ' . __('Reason: :reason', ['reason' => $reason]) : '')
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
