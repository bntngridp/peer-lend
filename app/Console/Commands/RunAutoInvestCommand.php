<?php

namespace App\Console\Commands;

use App\Modules\Loan\Services\AutoInvestService;
use Illuminate\Console\Command;

class RunAutoInvestCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'peer-lend:run-auto-invest';

    /**
     * The console command description.
     */
    protected $description = 'Scan open marketplace loans and automatically match/fund them based on lender auto-invest rules';

    /**
     * Execute the console command.
     */
    public function handle(AutoInvestService $autoInvestService): int
    {
        $this->info('Starting Auto-Invest matching engine...');

        $records = $autoInvestService->runAutoInvest();

        if (empty($records)) {
            $this->info('No matching auto-investments were executed.');
            return self::SUCCESS;
        }

        $this->info(sprintf('Successfully executed %d auto-investments:', count($records)));
        foreach ($records as $rec) {
            $this->line(sprintf(' - Lender ID: %s funded Loan ID: %s with amount: Rp %s', 
                $rec['lender_id'], 
                $rec['loan_id'], 
                number_format((float)$rec['amount'], 0, ',', '.')
            ));
        }

        return self::SUCCESS;
    }
}
