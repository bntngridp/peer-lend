<?php

namespace App\Modules\Loan\Services;

use App\Models\AutoInvestRule;
use App\Models\LoanRequest;
use App\Modules\Shared\Services\AuditLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoInvestService
{
    public function __construct(
        private readonly LoanFundingService $fundingService,
        private readonly AuditLogService $auditLogService,
    ) {}

    /**
     * Run the automated investment engine to fund active marketplace loans.
     * Returns the list of auto-funded transactions/records.
     */
    public function runAutoInvest(): array
    {
        $fundedRecords = [];

        // 1. Get open funding loans
        $loans = LoanRequest::where('status', LoanRequest::STATUS_OPEN_FUNDING)
            ->where('funded_percentage', '<', 100.00)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($loans->isEmpty()) {
            return $fundedRecords;
        }

        // 2. Get active auto-invest rules
        $rules = AutoInvestRule::where('is_active', true)
            ->with('lender.wallets')
            ->get();

        if ($rules->isEmpty()) {
            return $fundedRecords;
        }

        // 3. Process matching
        foreach ($loans as $loan) {
            foreach ($rules as $rule) {
                // Skip if lender is the borrower of the loan
                if ($loan->borrower_id === $rule->lender_id) {
                    continue;
                }

                // Check if lender already funded this loan
                $alreadyFunded = $loan->fundings()->where('lender_id', $rule->lender_id)->exists();
                if ($alreadyFunded) {
                    continue;
                }

                // Check matches rule criteria
                if (!$rule->matches($loan)) {
                    continue;
                }

                // Calculate required funding amount
                $loan->refresh();
                if ($loan->status !== LoanRequest::STATUS_OPEN_FUNDING) {
                    break; // Loan might be fully funded in this loop
                }

                // Remaining amount needed to hit 100%
                $fundedAmt = $loan->fundings()->sum('amount');
                $remainingNeeded = bcsub((string)$loan->amount, (string)$fundedAmt, 2);

                if (bccomp($remainingNeeded, '0.00', 2) <= 0) {
                    break;
                }

                // Get lender IDR wallet balance
                $lender = $rule->lender;
                $fiatWallet = $lender->walletFor($loan->currency_id);
                if (!$fiatWallet || bccomp((string)$fiatWallet->available_balance, '100000.00', 2) < 0) {
                    continue; // Balance too low to invest (min Rp 100k)
                }

                // Investment amount is min(max_allocation_per_loan, remainingNeeded, lenderAvailableBalance)
                $investAmt = number_format((float) $rule->max_allocation_per_loan, 2, '.', '');
                if (bccomp($investAmt, $remainingNeeded, 2) > 0) {
                    $investAmt = $remainingNeeded;
                }
                if (bccomp($investAmt, number_format((float)$fiatWallet->available_balance, 2, '.', ''), 2) > 0) {
                    $investAmt = number_format((float)$fiatWallet->available_balance, 2, '.', '');
                }

                // Floor/Clamp to make it clean Rupiah (e.g. min Rp 100k)
                if (bccomp($investAmt, '100000.00', 2) < 0) {
                    continue;
                }

                // Execute funding inside transaction safely
                try {
                    DB::transaction(function () use ($loan, $lender, $investAmt) {
                        $this->fundingService->fundLoan($lender, $loan, (string)$investAmt);
                    });

                    $fundedRecords[] = [
                        'lender_id' => $lender->id,
                        'loan_id'   => $loan->id,
                        'amount'    => $investAmt,
                    ];

                    $this->auditLogService->log(
                        'auto_invest_executed',
                        LoanRequest::class,
                        $loan->id,
                        $lender,
                        ['amount' => $investAmt]
                    );

                } catch (\Throwable $e) {
                    Log::error("AutoInvest failed for Loan {$loan->id}, Lender {$lender->id}: " . $e->getMessage());
                }
            }
        }

        return $fundedRecords;
    }
}
