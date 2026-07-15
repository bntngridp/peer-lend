<?php

namespace App\Console\Commands;

use App\Modules\Loan\Services\LateFeeService;
use Illuminate\Console\Command;

class CalculatePenaltiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'peer-lend:calculate-penalties {--dry-run : Only calculate penalties without saving changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan overdue installments and calculate late payment daily penalties.';

    /**
     * Execute the console command.
     */
    public function handle(LateFeeService $lateFeeService): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('--- RUNNING IN DRY-RUN MODE (NO CHANGES WILL BE SAVED) ---');
        }

        $this->info('Scanning installments for late payments...');

        $updated = $lateFeeService->calculatePenalties($dryRun);

        if (empty($updated)) {
            $this->info('No overdue or late installments found.');
            return self::SUCCESS;
        }

        $headers = ['Installment ID', 'Loan ID', 'Installment #', 'Days Late', 'Old Status', 'New Status', 'Old Penalty', 'New Penalty'];
        $rows = [];

        foreach ($updated as $item) {
            $rows[] = [
                $item['id'],
                $item['loan_id'],
                $item['installment_number'],
                $item['days_overdue'],
                $item['old_status'],
                $item['new_status'],
                'Rp ' . number_format((float)$item['old_penalty'], 2, ',', '.'),
                'Rp ' . number_format((float)$item['new_penalty'], 2, ',', '.'),
            ];
        }

        $this->table($headers, $rows);
        $this->info('Successfully processed ' . count($updated) . ' installment(s).');

        return self::SUCCESS;
    }
}
