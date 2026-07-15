<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Currency;
use App\Models\FeeConfiguration;
use App\Models\KYC;
use App\Models\LoanCategory;
use App\Models\LoanInstallment;
use App\Models\LoanRequest;
use App\Models\Notification;
use App\Models\User;
use App\Models\Wallet;
use App\Modules\Loan\Services\LateFeeService;
use App\Modules\Loan\Services\LiquidationService;
use App\Modules\Shared\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class Phase5LateFeeOracleTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function createVerifiedUser(string $role = 'borrower'): User
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $roleModel = \App\Models\Role::firstOrCreate(['name' => $role]);
        $user->roles()->attach($roleModel);
        $user->profile()->create([
            'full_name' => 'Test User',
            'phone'     => '0812345' . rand(10000, 99999),
        ]);
        KYC::create([
            'user_id' => $user->id,
            'nik'     => '320' . rand(1000000000000, 9999999999999),
            'status'  => 'approved',
        ]);
        return $user;
    }

    private function createWallet(User $user, string $balance = '1000000'): Wallet
    {
        $idrId = Currency::firstOrCreate(
            ['code' => 'IDR'],
            ['name' => 'Indonesian Rupiah', 'symbol' => 'Rp']
        )->id;
        return Wallet::create([
            'user_id'           => $user->id,
            'currency_id'       => $idrId,
            'available_balance' => $balance,
            'hold_balance'      => '0',
        ]);
    }

    // ─── Late Fee & Penalty Engine Tests ──────────────────────────────────────

    public function test_late_installments_status_and_penalty_calculation(): void
    {
        $borrower = $this->createVerifiedUser();
        $idrCurrency = Currency::firstOrCreate(['code' => 'IDR'], ['name' => 'Indonesian Rupiah', 'symbol' => 'Rp']);
        $category = LoanCategory::firstOrCreate(['name' => 'Personal'], ['description' => 'Personal loan']);

        // Create penalty configuration: 0.1% daily
        FeeConfiguration::updateOrCreate(
            ['type' => 'penalty_rate'],
            [
                'value'      => 0.1000,
                'value_type' => 'percentage',
                'is_active'  => true,
            ]
        );

        $loan = LoanRequest::create([
            'borrower_id'       => $borrower->id,
            'category_id'       => $category->id,
            'currency_id'       => $idrCurrency->id,
            'amount'            => '1200000',
            'purpose'           => 'Personal purpose',
            'description'       => 'Test loan',
            'risk_grade'        => 'A',
            'status'            => LoanRequest::STATUS_ACTIVE,
            'interest_rate'     => '12.00',
            'duration'          => 12,
            'tenor_type'        => 'monthly',
            'funded_percentage' => 0.00,
        ]);

        // Installment due 5 days ago, total amount: 100,000
        $installment = LoanInstallment::create([
            'loan_id'            => $loan->id,
            'installment_number' => 1,
            'due_date'           => now()->subDays(5)->toDateString(),
            'principal_amount'   => '90000.00',
            'interest_amount'    => '10000.00',
            'penalty_amount'     => '0.00',
            'total_amount'       => '100000.00',
            'status'             => LoanInstallment::STATUS_PENDING,
        ]);

        $service = app(LateFeeService::class);
        $updated = $service->calculatePenalties();

        $this->assertCount(1, $updated);
        $this->assertSame($installment->id, $updated[0]['id']);
        $this->assertSame(5, $updated[0]['days_overdue']);

        // Expected penalty = 5 days * (0.1 / 100) * 100,000 = 500
        $freshInstallment = $installment->fresh();
        $this->assertSame(LoanInstallment::STATUS_OVERDUE, $freshInstallment->status);
        $this->assertSame('500.00', (string)$freshInstallment->penalty_amount);

        // Verify notification was sent
        $this->assertDatabaseHas('notifications', [
            'user_id' => $borrower->id,
            'type'    => NotificationService::TYPE_INSTALLMENT_OVERDUE,
        ]);

        // Verify audit log was created
        $this->assertDatabaseHas('audit_logs', [
            'model_type' => LoanInstallment::class,
            'model_id'   => $installment->id,
            'action'     => 'installment_penalty_updated',
        ]);
    }

    public function test_dry_run_option_does_not_persist_penalty(): void
    {
        $borrower = $this->createVerifiedUser();
        $idrCurrency = Currency::firstOrCreate(['code' => 'IDR'], ['name' => 'Indonesian Rupiah', 'symbol' => 'Rp']);
        $category = LoanCategory::firstOrCreate(['name' => 'Personal'], ['description' => 'Personal loan']);

        $loan = LoanRequest::create([
            'borrower_id'       => $borrower->id,
            'category_id'       => $category->id,
            'currency_id'       => $idrCurrency->id,
            'amount'            => '1200000',
            'purpose'           => 'Personal purpose',
            'description'       => 'Test loan',
            'risk_grade'        => 'A',
            'status'            => LoanRequest::STATUS_ACTIVE,
            'interest_rate'     => '12.00',
            'duration'          => 12,
            'tenor_type'        => 'monthly',
            'funded_percentage' => 0.00,
        ]);

        // Installment due 3 days ago
        $installment = LoanInstallment::create([
            'loan_id'            => $loan->id,
            'installment_number' => 1,
            'due_date'           => now()->subDays(3)->toDateString(),
            'principal_amount'   => '90000.00',
            'interest_amount'    => '10000.00',
            'penalty_amount'     => '0.00',
            'total_amount'       => '100000.00',
            'status'             => LoanInstallment::STATUS_PENDING,
        ]);

        $service = app(LateFeeService::class);
        $updated = $service->calculatePenalties(true); // dryRun = true

        $this->assertCount(1, $updated);
        
        $freshInstallment = $installment->fresh();
        $this->assertSame(LoanInstallment::STATUS_PENDING, $freshInstallment->status);
        $this->assertSame('0.00', (string)$freshInstallment->penalty_amount);
    }

    public function test_artisan_calculate_penalties_command_execution(): void
    {
        $this->artisan('peer-lend:calculate-penalties')
             ->assertExitCode(0);
    }

    // ─── Live Price Oracle Tests ──────────────────────────────────────────────

    public function test_coingecko_live_oracle_price_success(): void
    {
        Http::fake([
            'https://api.coingecko.com/api/v3/simple/price*' => Http::response([
                'bitcoin' => ['idr' => 950000000],
                'ethereum' => ['idr' => 55000000],
                'tether' => ['idr' => 15550]
            ], 200)
        ]);

        $service = app(LiquidationService::class);
        $price = $service->getCryptoPrice('BTC');

        $this->assertSame('950000000', $price);
    }

    public function test_coingecko_live_oracle_fallback_on_failure(): void
    {
        // Force server failure
        Http::fake([
            'https://api.coingecko.com/api/v3/simple/price*' => Http::response(null, 500)
        ]);

        $service = app(LiquidationService::class);
        
        // Clear cache to ensure it requests and hits fallback
        \Illuminate\Support\Facades\Cache::forget('coingecko_prices');

        $price = $service->getCryptoPrice('BTC');

        // Should fall back to mock oracle price (which is random in a specific range)
        $this->assertGreaterThan(0, (float)$price);
    }
}
