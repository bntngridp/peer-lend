<?php

namespace App\Console\Commands;

use App\Models\LoanRequest;
use App\Modules\Loan\Services\LiquidationService;
use Illuminate\Console\Command;

class UpdateCryptoLtvCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'peer-lend:update-ltv {--dry-run : Print LTV values without executing liquidations}';

    /**
     * The console command description.
     */
    protected $description = 'Recalculate current LTV for all active crypto-collateral loans and auto-liquidate if threshold is exceeded.';

    public function __construct(private readonly LiquidationService $liquidationService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('⚠️  DRY RUN mode — no liquidations will be executed.');
        }

        $this->info('🔄 Fetching active loans with crypto collateral...');

        $loans = LoanRequest::where('status', LoanRequest::STATUS_ACTIVE)
            ->whereNotNull('collateral_currency_id')
            ->with(['borrower', 'collateralCurrency', 'fundings.lender'])
            ->get();

        if ($loans->isEmpty()) {
            $this->info('✅ No active crypto-collateral loans found. Nothing to update.');
            return self::SUCCESS;
        }

        $this->info("Found {$loans->count()} active loan(s) to process.");

        $updatedCount   = 0;
        $liquidatedCount = 0;

        $this->withProgressBar($loans, function (LoanRequest $loan) use ($isDryRun, &$updatedCount, &$liquidatedCount) {
            if ($isDryRun) {
                $currency     = $loan->collateralCurrency?->code ?? 'UNKNOWN';
                $currentPrice = $this->liquidationService->getMockOraclePrice($currency);
                $collateralValue = bcmul((string)$loan->collateral_amount, $currentPrice, 2);
                $ltv = bccomp($collateralValue, '0', 2) > 0
                    ? bcdiv(bcmul((string)$loan->amount, '100', 8), $collateralValue, 2)
                    : '0';

                $this->newLine();
                $this->line("  Loan #{$loan->id} | LTV: {$ltv}% | Liquidation at: {$loan->liquidation_ltv}%");
                return;
            }

            $wasLiquidated = $this->liquidationService->updateLtv($loan);
            $updatedCount++;

            if ($wasLiquidated) {
                $liquidatedCount++;
            }
        });

        $this->newLine(2);

        if (!$isDryRun) {
            $this->info("✅ LTV update complete — {$updatedCount} loan(s) processed, {$liquidatedCount} liquidated.");
        }

        return self::SUCCESS;
    }
}
