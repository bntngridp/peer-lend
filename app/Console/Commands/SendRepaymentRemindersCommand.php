<?php

namespace App\Console\Commands;

use App\Models\LoanInstallment;
use App\Modules\Shared\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendRepaymentRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'peer-lend:send-repayment-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Scan active loan installments due in exactly 3 days and send automated queue reminders';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $this->info('Scanning installments due in 3 days...');

        // 3 days from now
        $targetDate = Carbon::now()->addDays(3)->toDateString();

        $installments = LoanInstallment::where('status', LoanInstallment::STATUS_PENDING)
            ->whereDate('due_date', $targetDate)
            ->with('loan.borrower')
            ->get();

        if ($installments->isEmpty()) {
            $this->info('No installments are due in 3 days. No reminders sent.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($installments as $inst) {
            $loan = $inst->loan;
            if (!$loan || !$loan->borrower) {
                continue;
            }

            $dueDateFormatted = Carbon::parse($inst->due_date)->format('d M Y');

            $notificationService->notifyInstallmentDue(
                $loan->borrower,
                $inst->id,
                $dueDateFormatted,
                (string)$inst->total_amount
            );

            $this->line(sprintf(' - Queued reminder for Borrower %s (Loan ID: %s, Due: %s)', 
                $loan->borrower->email,
                $loan->id,
                $dueDateFormatted
            ));
            $count++;
        }

        $this->info(sprintf('Successfully queued %d repayment reminders.', $count));

        return self::SUCCESS;
    }
}
