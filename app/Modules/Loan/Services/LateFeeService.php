<?php

namespace App\Modules\Loan\Services;

use App\Models\FeeConfiguration;
use App\Models\LoanInstallment;
use App\Modules\Shared\Services\AuditLogService;
use App\Modules\Shared\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class LateFeeService
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly AuditLogService      $auditLogService,
    ) {}

    /**
     * Scan all pending/overdue installments that are past their due date,
     * update their status to overdue, calculate the daily daily penalty, and notify the borrower.
     *
     * Returns the list of updated installments metadata.
     */
    public function calculatePenalties(bool $dryRun = false): array
    {
        $today = now()->startOfDay();
        
        // Find unpaid installments where due_date is in the past
        $overdueInstallments = LoanInstallment::whereIn('status', [
            LoanInstallment::STATUS_PENDING,
            LoanInstallment::STATUS_OVERDUE,
        ])
        ->where('due_date', '<', $today->toDateString())
        ->get();

        $updated = [];

        foreach ($overdueInstallments as $installment) {
            $result = DB::transaction(function () use ($installment, $today, $dryRun) {
                $lockedInstallment = LoanInstallment::lockForUpdate()->find($installment->id);
                
                if (!$lockedInstallment) {
                    return null;
                }

                $dueDate = $lockedInstallment->due_date->startOfDay();
                
                if ($today->lte($dueDate)) {
                    return null;
                }

                $daysOverdue = abs((int) $today->diffInDays($dueDate));

                if ($daysOverdue <= 0) {
                    return null;
                }

                // Get penalty configuration
                $penaltyConfig = FeeConfiguration::getByType('penalty_rate');
                $penaltyRate = $penaltyConfig ? $penaltyConfig->value : '0.1000'; // fallback 0.1% daily

                // Daily penalty = total_amount * (penalty_rate / 100)
                $dailyRateFactor = bcdiv($penaltyRate, '100', 6);
                $dailyPenalty = bcmul($lockedInstallment->total_amount, $dailyRateFactor, 4);

                // Total penalty = days_overdue * daily_penalty
                $newPenalty = bcmul($dailyPenalty, (string)$daysOverdue, 2);

                $oldStatus = $lockedInstallment->status;
                $oldPenalty = (string)$lockedInstallment->penalty_amount;

                if (! $dryRun) {
                    $lockedInstallment->update([
                        'status'         => LoanInstallment::STATUS_OVERDUE,
                        'penalty_amount' => $newPenalty,
                    ]);

                    // Send notification to borrower
                    $borrower = $lockedInstallment->loan->borrower;
                    if ($borrower) {
                        $this->notificationService->notifyInstallmentOverdue(
                            $borrower,
                            $lockedInstallment->id,
                            $lockedInstallment->fresh()->getTotalDueAttribute(),
                            $daysOverdue
                        );
                    }

                    // Log audit trail on transition to overdue or when penalty increases
                    if ($oldStatus !== LoanInstallment::STATUS_OVERDUE || bccomp($oldPenalty, $newPenalty, 2) !== 0) {
                        $this->auditLogService->log(
                            'installment_penalty_updated',
                            LoanInstallment::class,
                            $lockedInstallment->id,
                            null,
                            [
                                'old_status'  => $oldStatus,
                                'new_status'  => LoanInstallment::STATUS_OVERDUE,
                                'old_penalty' => $oldPenalty,
                                'new_penalty' => $newPenalty,
                                'days_late'   => $daysOverdue,
                            ]
                        );
                    }
                }

                return [
                    'id'                 => $lockedInstallment->id,
                    'installment_number' => $lockedInstallment->installment_number,
                    'loan_id'            => $lockedInstallment->loan_id,
                    'days_overdue'       => $daysOverdue,
                    'old_penalty'        => $oldPenalty,
                    'new_penalty'        => $newPenalty,
                    'old_status'         => $oldStatus,
                    'new_status'         => LoanInstallment::STATUS_OVERDUE,
                ];
            });

            if ($result) {
                $updated[] = $result;
            }
        }

        return $updated;
    }
}
