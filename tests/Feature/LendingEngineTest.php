<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\KYC;
use App\Models\LoanCategory;
use App\Models\LoanInstallment;
use App\Models\LoanRequest;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use App\Modules\Wallet\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LendingEngineTest extends TestCase
{
    use RefreshDatabase;

    private User $borrower;
    private User $lender1;
    private User $lender2;
    private User $admin;
    private Currency $idr;
    private LoanCategory $businessCategory;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Seed Roles, Currencies and Configurations
        $this->artisan('db:seed');

        $this->idr = Currency::where('code', 'IDR')->firstOrFail();
        
        // Create custom category for loans
        $this->businessCategory = LoanCategory::create([
            'name'        => 'Business Expansion',
            'slug'        => 'business-expansion',
            'description' => 'For merchant scaling',
        ]);

        // 2. Setup Borrower (Verified KYC)
        $this->borrower = User::factory()->create();
        $this->borrower->roles()->attach(Role::where('name', 'borrower')->firstOrFail()->id);
        Profile::create([
            'user_id'   => $this->borrower->id,
            'full_name' => 'Bintang Peminjam',
            'phone'     => '081234567899',
        ]);
        KYC::create([
            'user_id' => $this->borrower->id,
            'status'  => 'approved',
        ]);

        // Create borrower's wallet
        Wallet::create([
            'user_id'           => $this->borrower->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 0,
            'hold_balance'      => 0,
        ]);

        // 3. Setup Lender 1 (Verified KYC with Rp 6.000.000 balance)
        $this->lender1 = User::factory()->create();
        $this->lender1->roles()->attach(Role::where('name', 'lender')->firstOrFail()->id);
        Profile::create([
            'user_id'   => $this->lender1->id,
            'full_name' => 'Lender One',
            'phone'     => '081234567891',
        ]);
        KYC::create([
            'user_id' => $this->lender1->id,
            'status'  => 'approved',
        ]);
        
        $wallet1 = Wallet::create([
            'user_id'           => $this->lender1->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 6000000.00,
            'hold_balance'      => 0,
        ]);

        // 4. Setup Lender 2 (Verified KYC with Rp 6.000.000 balance)
        $this->lender2 = User::factory()->create();
        $this->lender2->roles()->attach(Role::where('name', 'lender')->firstOrFail()->id);
        Profile::create([
            'user_id'   => $this->lender2->id,
            'full_name' => 'Lender Two',
            'phone'     => '081234567892',
        ]);
        KYC::create([
            'user_id' => $this->lender2->id,
            'status'  => 'approved',
        ]);
        
        $wallet2 = Wallet::create([
            'user_id'           => $this->lender2->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 6000000.00,
            'hold_balance'      => 0,
        ]);

        // 5. Setup Admin
        $this->admin = User::factory()->create();
        $this->admin->roles()->attach(Role::where('name', 'admin')->firstOrFail()->id);
    }

    /**
     * Test full Lending Engine workflow: create, approve, fund, disburse, pay, interest distribution.
     */
    public function test_complete_lending_and_repayment_lifecycle(): void
    {
        // ─── Step 1: Borrower Submits Loan Request (Rp 10.000.000) ────
        $response = $this->actingAs($this->borrower)->post(route('loans.store'), [
            'category_id'            => $this->businessCategory->id,
            'amount'                 => 10000000,
            'duration'               => 12,
            'interest_rate'          => 12.00, // 12% APR
            'purpose'                => 'Expand grocery store',
            'risk_grade'             => 'B',
            'collateral_currency_id' => null, // Unsecured fiat
        ]);

        $response->assertRedirect(route('loans.index'));
        
        $this->assertDatabaseHas('loan_requests', [
            'borrower_id' => $this->borrower->id,
            'amount'      => 10000000,
            'status'      => 'pending',
            'risk_grade'  => 'B',
        ]);

        $loan = LoanRequest::where('borrower_id', $this->borrower->id)->firstOrFail();

        // ─── Step 2: Admin Approves the Loan to Marketplace ──────────
        $response = $this->actingAs($this->admin)->post(route('admin.loans.approve', $loan->id));
        $response->assertRedirect(route('admin.loans.index'));
        
        $loan->refresh();
        $this->assertEquals('open_funding', $loan->status);

        // ─── Step 3: Lender 1 Funds Rp 4.000.000 ─────────────────────
        $response = $this->actingAs($this->lender1)->post(route('marketplace.fund', $loan->id), [
            'amount' => 4000000,
        ]);
        $response->assertRedirect(route('marketplace.show', $loan->id));

        // Check Lender 1 Wallet (Rp 4jt moved from Available to Hold)
        $this->assertDatabaseHas('wallets', [
            'user_id'           => $this->lender1->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 2000000.00,
            'hold_balance'      => 4000000.00,
        ]);

        $loan->refresh();
        $this->assertEquals(40.00, (float) $loan->funded_percentage);

        // ─── Step 4: Lender 2 Funds remaining Rp 6.000.000 ───────────
        $response = $this->actingAs($this->lender2)->post(route('marketplace.fund', $loan->id), [
            'amount' => 6000000,
        ]);
        $response->assertRedirect(route('marketplace.show', $loan->id));

        // Check Lender 2 Wallet (Rp 6jt moved from Available to Hold)
        $this->assertDatabaseHas('wallets', [
            'user_id'           => $this->lender2->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 0.00,
            'hold_balance'      => 6000000.00,
        ]);

        $loan->refresh();
        $this->assertEquals('funded', $loan->status);
        $this->assertEquals(100.00, (float) $loan->funded_percentage);
        $this->assertNotNull($loan->agreement);

        // ─── Step 5: Admin Disburses Loan (1.5% origination fee) ──────
        // Net Disbursement: Rp 10.000.000 - 1.5% = Rp 9.850.000
        $response = $this->actingAs($this->admin)->post(route('admin.loans.disburse', $loan->id));
        $response->assertRedirect(route('admin.loans.index'));

        $loan->refresh();
        $this->assertEquals('active', $loan->status);

        // Check Borrower Wallet (disbursed net)
        $this->assertDatabaseHas('wallets', [
            'user_id'           => $this->borrower->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 9850000.00,
        ]);

        // Check Lenders hold balances settled (Hold cleared to 0)
        $this->assertDatabaseHas('wallets', [
            'user_id'           => $this->lender1->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 2000000.00,
            'hold_balance'      => 0.00,
        ]);
        $this->assertDatabaseHas('wallets', [
            'user_id'           => $this->lender2->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 0.00,
            'hold_balance'      => 0.00,
        ]);

        // Verify Amortization schedule has been generated
        $this->assertCount(12, $loan->installments);

        // 10,000,000 / 12 months = 833,333.33 Monthly Principal
        // 10,000,000 * 12% APR / 12 months = 100,000.00 Monthly Interest
        // Total monthly payment = Rp 933,333.33
        $firstInstallment = $loan->installments()->first();
        $this->assertEquals(833333.33, (float) $firstInstallment->principal_amount);
        $this->assertEquals(100000.00, (float) $firstInstallment->interest_amount);
        $this->assertEquals(933333.33, (float) $firstInstallment->total_amount);

        // ─── Step 6: Borrower Repays First Installment ────────────────
        // Borrower deposits enough fiat into wallet first
        $this->app->make(WalletService::class)->deposit($this->borrower, $this->idr->id, 1000000.00);
        
        $borrowerWallet = $this->borrower->walletFor($this->idr->id);
        $this->assertEquals(10850000.00, (float) $borrowerWallet->available_balance);

        // Pay the first installment
        $response = $this->actingAs($this->borrower)->post(route('repayments.pay', $firstInstallment->id));
        $response->assertRedirect(); // redirects back

        // Assert first installment is paid
        $firstInstallment->refresh();
        $this->assertEquals('paid', $firstInstallment->status);
        $this->assertNotNull($firstInstallment->paid_at);

        // Assert borrower wallet balance: Rp 10.850.000 - Rp 933.333,33 = Rp 9.916.666,67
        $this->assertDatabaseHas('wallets', [
            'user_id'           => $this->borrower->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 9916666.67,
        ]);

        // Assert Lender 1 (40% share) payout: 933,333.33 * 40% = 373,333.33
        // New balance: 2,000,000 + 373,333.33 = 2,373,333.33
        $this->assertDatabaseHas('wallets', [
            'user_id'           => $this->lender1->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 2373333.33,
        ]);

        // Assert Lender 2 (60% share) payout: 933,333.33 * 60% = 559,999.998 => truncated to 559,999.99
        // New balance: 0 + 559,999.99 = 559,999.99
        $this->assertDatabaseHas('wallets', [
            'user_id'           => $this->lender2->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 559999.99,
        ]);
    }
}
