<?php

namespace App\Modules\Loan\Services;

use App\Models\LoanAgreement;
use App\Models\LoanFunding;
use App\Models\LoanRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Modules\Wallet\Services\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoanFundingService
{
    public function __construct(
        private readonly WalletService $walletService
    ) {}

    /**
     * Invest/Fund a portion of an open loan request.
     *
     * Locks both the loan request and the investor's wallet to prevent race conditions.
     */
    public function fundLoan(User $lender, LoanRequest $loan, string $amount): LoanFunding
    {
        return DB::transaction(function () use ($lender, $loan, $amount) {
            // 1. Lock LoanRequest row for update
            $lockedLoan = LoanRequest::lockForUpdate()->findOrFail($loan->id);

            if ($lockedLoan->status !== LoanRequest::STATUS_OPEN_FUNDING) {
                throw ValidationException::withMessages([
                    'loan' => ['This loan is not currently open for funding.'],
                ]);
            }

            // Calculate remaining funding requirements
            $fundedSum = $lockedLoan->fundings()->sum('amount') ?? '0.00';
            $remainingRequired = bcsub($lockedLoan->amount, $fundedSum, 2);

            if (bccomp($remainingRequired, '0.00', 2) <= 0) {
                throw ValidationException::withMessages([
                    'amount' => ['This loan is already fully funded.'],
                ]);
            }

            // Cap the funding amount at the remaining required balance
            if (bccomp($amount, $remainingRequired, 2) > 0) {
                $amount = $remainingRequired;
            }

            // 2. Lock Lender's Wallet for update
            $lenderWallet = Wallet::lockForUpdate()->where([
                'user_id'     => $lender->id,
                'currency_id' => $lockedLoan->currency_id,
            ])->first();

            if (! $lenderWallet || bccomp($lenderWallet->available_balance, $amount, 8) < 0) {
                throw ValidationException::withMessages([
                    'amount' => ['Insufficient available balance in your wallet to invest this amount.'],
                ]);
            }

            // 3. Put balance into hold state
            $this->walletService->holdBalance($lenderWallet, $amount);

            // 4. Create LoanFunding record
            $percentage = bcdiv(bcmul($amount, '100', 4), $lockedLoan->amount, 2);
            $funding = LoanFunding::create([
                'loan_id'    => $lockedLoan->id,
                'lender_id'  => $lender->id,
                'amount'     => $amount,
                'percentage' => $percentage,
                'status'     => 'active',
            ]);

            // 5. Update loan status and percentages
            $newFundedSum = bcadd($fundedSum, $amount, 2);
            $newPercentage = bcdiv(bcmul($newFundedSum, '100', 4), $lockedLoan->amount, 2);
            
            $updateData = [
                'funded_percentage' => $newPercentage,
            ];

            // Auto-transition to funded if target is met
            if (bccomp($newPercentage, '100.00', 2) >= 0) {
                $updateData['status'] = LoanRequest::STATUS_FUNDED;
                $updateData['funded_at'] = now();

                // Automatically generate digital agreement contracts
                LoanAgreement::create([
                    'loan_id'          => $lockedLoan->id,
                    'agreement_number' => 'AGR-' . date('Ymd') . '-' . strtoupper(Str::random(8)),
                    'status'           => 'pending',
                ]);
            }

            $lockedLoan->update($updateData);

            return $funding;
        });
    }
}
