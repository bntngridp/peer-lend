<?php

namespace App\Modules\Loan\Services;

use App\Models\FeeConfiguration;
use App\Models\LoanAgreement;
use App\Models\LoanInstallment;
use App\Models\LoanRepayment;
use App\Models\LoanRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Modules\Wallet\Services\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RepaymentService
{
    public function __construct(
        private readonly WalletService $walletService
    ) {}

    /**
     * Disburse a fully funded loan to the borrower.
     *
     * Creates monthly flat-rate amortization schedules and settles held balances.
     */
    public function disburse(LoanRequest $loan): LoanRequest
    {
        return DB::transaction(function () use ($loan) {
            $lockedLoan = LoanRequest::lockForUpdate()->findOrFail($loan->id);

            if ($lockedLoan->status !== LoanRequest::STATUS_FUNDED) {
                throw ValidationException::withMessages([
                    'loan' => ['Only fully funded loans can be disbursed.'],
                ]);
            }

            // Verify borrower has signed the contract
            $agreement = $lockedLoan->agreement;
            if (! $agreement) {
                throw ValidationException::withMessages([
                    'agreement' => ['No signed contract agreement found for this loan.'],
                ]);
            }

            // 1. Calculate and deduct Origination Fee
            $originationConfig = FeeConfiguration::getByType('origination_fee');
            $originationRate = $originationConfig ? $originationConfig->value : '1.5000'; // fallback 1.5%
            
            $originationFee = bcmul($lockedLoan->amount, bcdiv($originationRate, '100', 6), 2);
            $netDisbursement = bcsub($lockedLoan->amount, $originationFee, 2);

            // Deposit net disbursement to Borrower
            $borrower = $lockedLoan->borrower;
            $this->walletService->deposit(
                $borrower,
                $lockedLoan->currency_id,
                $netDisbursement,
                "Disbursement for loan #{$lockedLoan->id} (Net of origination fee)"
            );

            // 2. Generate Flat-Rate Installment Amortization Schedules
            $duration = $lockedLoan->duration;
            $monthlyPrincipal = bcdiv($lockedLoan->amount, $duration, 2);
            
            // Monthly Interest = Total Loan Amount * (Annual Interest Rate / 12)
            $monthlyInterestRate = bcdiv(bcdiv($lockedLoan->interest_rate, '100', 6), '12', 6);
            $monthlyInterest = bcmul($lockedLoan->amount, $monthlyInterestRate, 2);
            $monthlyInstallmentAmount = bcadd($monthlyPrincipal, $monthlyInterest, 2);

            for ($i = 1; $i <= $duration; $i++) {
                LoanInstallment::create([
                    'loan_id'            => $lockedLoan->id,
                    'installment_number' => $i,
                    'due_date'           => now()->addMonths($i)->toDateString(),
                    'principal_amount'   => $monthlyPrincipal,
                    'interest_amount'    => $monthlyInterest,
                    'penalty_amount'     => 0.00,
                    'total_amount'       => $monthlyInstallmentAmount,
                    'status'             => LoanInstallment::STATUS_PENDING,
                ]);
            }

            // 3. Settle Lenders Hold Balances (Convert Hold to Spent)
            foreach ($lockedLoan->fundings as $funding) {
                $lenderWallet = Wallet::lockForUpdate()->where([
                    'user_id'     => $funding->lender_id,
                    'currency_id' => $lockedLoan->currency_id,
                ])->first();

                if ($lenderWallet) {
                    // Reduce from Hold balance
                    $this->walletService->useHold($lenderWallet, $funding->amount);

                    // Create log transaction for lender
                    \App\Models\WalletTransaction::create([
                        'wallet_id'      => $lenderWallet->id,
                        'type'           => 'funding',
                        'amount'         => $funding->amount,
                        'balance_before' => $lenderWallet->available_balance,
                        'balance_after'  => $lenderWallet->available_balance, // hold reduced, available remains same
                        'description'    => "Capital deployed for loan #{$lockedLoan->id}",
                    ]);
                }
            }

            // 4. Update status and dates
            $lockedLoan->update([
                'status'       => LoanRequest::STATUS_ACTIVE,
                'disbursed_at' => now(),
            ]);

            $agreement->update(['status' => 'active', 'signed_at' => now()]);

            return $lockedLoan;
        });
    }

    /**
     * Pay a specific loan installment and distribute funds back to P2P investors.
     */
    public function payInstallment(User $borrower, LoanInstallment $installment): void
    {
        DB::transaction(function () use ($borrower, $installment) {
            $lockedInstallment = LoanInstallment::lockForUpdate()->findOrFail($installment->id);

            if ($lockedInstallment->status === LoanInstallment::STATUS_PAID) {
                throw ValidationException::withMessages([
                    'installment' => ['This installment has already been paid.'],
                ]);
            }

            $loan = $lockedInstallment->loan;
            
            // Total cost borrower pays (includes daily penalty rates if overdue)
            $totalDue = bcadd($lockedInstallment->total_amount, $lockedInstallment->penalty_amount, 2);

            // 1. Lock borrower wallet and withdraw payment amount
            $borrowerWallet = Wallet::lockForUpdate()->where([
                'user_id'     => $borrower->id,
                'currency_id' => $loan->currency_id,
            ])->first();

            if (! $borrowerWallet || bccomp($borrowerWallet->available_balance, $totalDue, 8) < 0) {
                throw ValidationException::withMessages([
                    'balance' => ['Insufficient available balance in your wallet to pay this installment.'],
                ]);
            }

            $before = $borrowerWallet->available_balance;
            $after = bcsub($before, $totalDue, 8);

            $borrowerWallet->update(['available_balance' => $after]);

            // Log borrower transaction
            \App\Models\WalletTransaction::create([
                'wallet_id'      => $borrowerWallet->id,
                'type'           => 'repayment',
                'amount'         => $totalDue,
                'balance_before' => $before,
                'balance_after'  => $after,
                'description'    => "Repayment installment #{$lockedInstallment->installment_number} for loan #{$loan->id}",
            ]);

            // Create repayment record
            LoanRepayment::create([
                'loan_id'        => $loan->id,
                'installment_id' => $lockedInstallment->id,
                'amount_paid'    => $totalDue,
                'payment_date'   => now()->toDateString(),
            ]);

            // 2. Distribute Principal + Interest to Lenders proportionally
            foreach ($loan->fundings as $funding) {
                // Lender Share = Total Due * (Funding Percentage / 100)
                $lenderShareRate = bcdiv($funding->percentage, '100', 6);
                $lenderPayout = bcmul($totalDue, $lenderShareRate, 2);

                $lenderWallet = Wallet::lockForUpdate()->firstOrCreate([
                    'user_id'     => $funding->lender_id,
                    'currency_id' => $loan->currency_id,
                ], [
                    'available_balance' => 0,
                    'hold_balance'      => 0,
                ]);

                $lBefore = $lenderWallet->available_balance;
                $lAfter = bcadd($lBefore, $lenderPayout, 8);

                $lenderWallet->update(['available_balance' => $lAfter]);

                // Log lender payout transaction
                \App\Models\WalletTransaction::create([
                    'wallet_id'      => $lenderWallet->id,
                    'type'           => 'repayment',
                    'amount'         => $lenderPayout,
                    'balance_before' => $lBefore,
                    'balance_after'  => $lAfter,
                    'description'    => "Repayment share received for loan #{$loan->id} (installment #{$lockedInstallment->installment_number})",
                ]);
            }

            // 3. Update installment status
            $lockedInstallment->update([
                'status'  => LoanInstallment::STATUS_PAID,
                'paid_at' => now(),
            ]);

            // 4. Auto-complete Loan if all installments are fully settled
            $hasUnpaid = LoanInstallment::where('loan_id', $loan->id)
                ->where('status', '!=', LoanInstallment::STATUS_PAID)
                ->exists();

            if (! $hasUnpaid) {
                $loan->update(['status' => LoanRequest::STATUS_COMPLETED]);
            }
        });
    }
}
