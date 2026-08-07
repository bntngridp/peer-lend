<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\LoanCategory;
use App\Models\LoanInstallment;
use App\Models\LoanRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowerCollateralAndRepaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $borrower;
    private User $lender;
    private Currency $fiat;
    private Currency $eth;
    private LoanCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'borrower'], ['guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'lender'], ['guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin'], ['guard_name' => 'web']);

        $this->borrower = User::factory()->create(['is_active' => true]);
        $this->borrower->roles()->attach(Role::where('name', 'borrower')->first());
        \App\Models\KYC::create(['user_id' => $this->borrower->id, 'status' => 'approved', 'full_name' => 'Test Borrower', 'id_number' => '1234567890']);

        $this->lender = User::factory()->create(['is_active' => true]);
        $this->lender->roles()->attach(Role::where('name', 'lender')->first());
        \App\Models\KYC::create(['user_id' => $this->lender->id, 'status' => 'approved', 'full_name' => 'Test Lender', 'id_number' => '0987654321']);

        $this->fiat = Currency::firstOrCreate(['code' => 'IDR'], [
            'name' => 'Indonesian Rupiah',
            'symbol' => 'Rp',
            'type' => 'fiat',
            'is_active' => true,
        ]);

        $this->eth = Currency::firstOrCreate(['code' => 'ETH'], [
            'name' => 'Ethereum',
            'symbol' => 'ETH',
            'type' => 'crypto',
            'is_active' => true,
            'decimal_places' => 6,
        ]);

        $this->category = LoanCategory::firstOrCreate(['name' => 'Personal Loan'], [
            'is_active' => true,
        ]);
    }

    public function test_borrower_cannot_apply_crypto_loan_without_sufficient_crypto_balance(): void
    {
        // Borrower has no ETH wallet
        $response = $this->actingAs($this->borrower)
            ->post(route('loans.store'), [
                'category_id' => $this->category->id,
                'amount' => 10000000,
                'duration' => 6,
                'purpose' => 'Business Expansion',
                'collateral_currency_id' => $this->eth->id,
            ]);

        $response->assertSessionHasErrors(['collateral_currency_id']);
    }

    public function test_borrower_locks_crypto_collateral_on_loan_request_submission(): void
    {
        // Give borrower 1.0 ETH
        $borrowerEthWallet = Wallet::create([
            'user_id' => $this->borrower->id,
            'currency_id' => $this->eth->id,
            'available_balance' => 1.000000,
            'hold_balance' => 0.000000,
        ]);

        $response = $this->actingAs($this->borrower)
            ->post(route('loans.store'), [
                'category_id' => $this->category->id,
                'amount' => 10000000,
                'duration' => 6,
                'purpose' => 'Business Expansion',
                'collateral_currency_id' => $this->eth->id,
            ]);

        $response->assertRedirect(route('loans.index'));
        $response->assertSessionHas('success');

        $borrowerEthWallet->refresh();
        $this->assertGreaterThan(0, (float)$borrowerEthWallet->hold_balance);
        $this->assertLessThan(1.000000, (float)$borrowerEthWallet->available_balance);
    }

    public function test_collateral_unlocked_on_loan_rejection(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->roles()->attach(Role::where('name', 'admin')->first());

        // Create borrower ETH wallet with 0.5 ETH held
        $borrowerEthWallet = Wallet::create([
            'user_id' => $this->borrower->id,
            'currency_id' => $this->eth->id,
            'available_balance' => 0.500000,
            'hold_balance' => 0.500000,
        ]);

        $loan = LoanRequest::create([
            'borrower_id' => $this->borrower->id,
            'category_id' => $this->category->id,
            'amount' => 10000000,
            'interest_rate' => 12.00,
            'duration' => 6,
            'tenor_type' => 'monthly',
            'purpose' => 'Working Capital',
            'currency_id' => $this->fiat->id,
            'collateral_currency_id' => $this->eth->id,
            'collateral_amount' => 0.500000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.loans.reject', $loan->id), [
                'rejection_reason' => 'High risk profile',
            ]);

        $response->assertRedirect(route('admin.loans.index'));

        $borrowerEthWallet->refresh();
        $this->assertEquals('0.00000000', number_format((float)$borrowerEthWallet->hold_balance, 8, '.', ''));
        $this->assertEquals('1.00000000', number_format((float)$borrowerEthWallet->available_balance, 8, '.', ''));
    }

    public function test_funding_lender_can_view_loan_installments_schedule(): void
    {
        $loan = LoanRequest::create([
            'borrower_id' => $this->borrower->id,
            'category_id' => $this->category->id,
            'amount' => 5000000,
            'interest_rate' => 12.00,
            'duration' => 6,
            'tenor_type' => 'monthly',
            'purpose' => 'Equipment',
            'currency_id' => $this->fiat->id,
            'status' => 'active',
        ]);

        $loan->fundings()->create([
            'lender_id' => $this->lender->id,
            'amount' => 5000000,
            'percentage' => 100.00,
        ]);

        $response = $this->actingAs($this->lender)
            ->get(route('loans.installments', $loan->id));

        $response->assertOk();
        $response->assertSee('Installments Schedule');
    }
}
