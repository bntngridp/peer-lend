<?php

namespace App\Modules\Loan\Services;

use App\Models\LoanFunding;
use App\Models\LoanRequest;
use App\Models\User;
use App\Modules\Shared\Services\AuditLogService;
use App\Modules\Shared\Services\NotificationService;
use App\Modules\Wallet\Services\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LiquidationService
{
    public function __construct(
        private readonly WalletService      $walletService,
        private readonly AuditLogService    $auditLogService,
        private readonly NotificationService $notificationService,
    ) {}

    // ─── Mock Oracle ──────────────────────────────────────────────────────────

    /**
     * Simulate fetching current crypto price from an oracle.
     * In production, replace with CoinGecko / Chainlink API call.
     *
     * @return string Price in IDR (bcmath string)
     */
    public function getMockOraclePrice(string $currencyCode): string
    {
        return match (strtoupper($currencyCode)) {
            'BTC'  => (string)(rand(950_000_000, 1_100_000_000)),  // ~Rp 1 Milyar
            'ETH'  => (string)(rand(55_000_000,  65_000_000)),    // ~Rp 60 Juta
            'BNB'  => (string)(rand(8_000_000,   11_000_000)),    // ~Rp 9 Juta
            'SOL'  => (string)(rand(2_200_000,   2_800_000)),     // ~Rp 2.5 Juta
            'USDT',
            'USDC' => '15500',                                     // ~Rp 15.500 per 1 USDT
            default => '10000000',
        };
    }

    // ─── LTV Update ───────────────────────────────────────────────────────────

    /**
     * Recalculate the current LTV for a single loan using the latest oracle price.
     * Returns true if the loan was liquidated, false otherwise.
     */
    public function updateLtv(LoanRequest $loan): bool
    {
        // Only active loans with crypto collateral can be liquidated
        if ($loan->status !== LoanRequest::STATUS_ACTIVE || !$loan->collateral_currency_id) {
            return false;
        }

        $currencyCode = $loan->collateralCurrency?->code ?? 'BTC';
        $currentPrice = $this->getMockOraclePrice($currencyCode);

        // current_ltv = loan_amount / (collateral_amount * current_price) * 100
        $collateralValueIdr = bcmul((string)$loan->collateral_amount, $currentPrice, 2);

        if (bccomp($collateralValueIdr, '0', 2) === 0) {
            return false;
        }

        $currentLtv = bcdiv(
            bcmul((string)$loan->amount, '100', 8),
            $collateralValueIdr,
            2
        );

        $loan->update(['current_ltv' => $currentLtv]);

        Log::info("LTV updated for loan {$loan->id}: {$currentLtv}% (price: {$currentPrice})");

        // ── Warn if LTV is between 70%-80% ───────────────────────────────
        if (bccomp($currentLtv, '70', 2) >= 0 && bccomp($currentLtv, (string)$loan->liquidation_ltv, 2) < 0) {
            $this->notificationService->notifyLtvWarning(
                $loan->borrower,
                $loan->id,
                $currentLtv
            );
        }

        // ── Trigger liquidation if LTV ≥ liquidation_ltv (default 80%) ───
        if (bccomp($currentLtv, (string)$loan->liquidation_ltv, 2) >= 0) {
            $this->liquidate($loan, $currentPrice);
            return true;
        }

        return false;
    }

    // ─── Liquidation ──────────────────────────────────────────────────────────

    /**
     * Execute collateral liquidation for a loan that has exceeded its liquidation LTV.
     * Distributes recovered funds proportionally to all lenders, then marks loan as liquidated.
     */
    public function liquidate(LoanRequest $loan, string $currentCryptoPrice): void
    {
        DB::transaction(function () use ($loan, $currentCryptoPrice) {
            // Re-lock the loan row for safety
            $lockedLoan = LoanRequest::lockForUpdate()->findOrFail($loan->id);

            if ($lockedLoan->status !== LoanRequest::STATUS_ACTIVE) {
                return; // Already handled in a concurrent request
            }

            // 1. Calculate total recovered value from collateral
            $totalRecovered = bcmul((string)$lockedLoan->collateral_amount, $currentCryptoPrice, 2);
            $totalFunded    = LoanFunding::where('loan_id', $lockedLoan->id)->sum('amount');

            Log::warning("Liquidating loan {$lockedLoan->id} — recovered: {$totalRecovered} IDR from collateral");

            // 2. Distribute recovered funds proportionally to each lender
            $fundings = LoanFunding::where('loan_id', $lockedLoan->id)
                ->with('lender')
                ->lockForUpdate()
                ->get();

            $totalDistributed = '0';

            foreach ($fundings as $funding) {
                if (bccomp((string)$totalFunded, '0', 2) === 0) {
                    continue;
                }

                // lender_share = (funding_amount / total_funded) * total_recovered
                $lenderShare = bcdiv(
                    bcmul((string)$funding->amount, $totalRecovered, 8),
                    (string)$totalFunded,
                    2
                );

                if (bccomp($lenderShare, '0', 2) > 0) {
                    $this->walletService->credit(
                        $funding->lender,
                        $lenderShare,
                        'liquidation_recovery',
                        "Likuidasi jaminan pinjaman #{$lockedLoan->id} — bagian investor"
                    );

                    $this->notificationService->notifyLenderLiquidated(
                        $funding->lender,
                        $lockedLoan->id,
                        $lenderShare
                    );

                    $totalDistributed = bcadd($totalDistributed, $lenderShare, 2);
                }
            }

            // 3. Mark loan as liquidated
            $lockedLoan->update(['status' => LoanRequest::STATUS_LIQUIDATED]);

            // 4. Notify borrower
            $this->notificationService->notifyBorrowerLiquidated(
                $lockedLoan->borrower,
                $lockedLoan->id
            );

            // 5. Write audit log
            $this->auditLogService->log(
                'loan_liquidated',
                LoanRequest::class,
                $lockedLoan->id,
                null,
                [
                    'crypto_price_idr'  => $currentCryptoPrice,
                    'collateral_amount' => (string)$lockedLoan->collateral_amount,
                    'total_recovered'   => $totalRecovered,
                    'total_distributed' => $totalDistributed,
                    'ltv_at_liquidation' => (string)$lockedLoan->current_ltv,
                ]
            );
        });
    }
}
