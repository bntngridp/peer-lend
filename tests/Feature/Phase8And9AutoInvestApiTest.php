<?php

namespace Tests\Feature;

use App\Models\AutoInvestRule;
use App\Models\Currency;
use App\Models\KYC;
use App\Models\LoanCategory;
use App\Models\LoanRequest;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class Phase8And9AutoInvestApiTest extends TestCase
{
    use RefreshDatabase;

    private User $borrower;
    private User $lender;
    private Currency $idr;
    private LoanCategory $education;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles, currencies, setting
        $this->artisan('db:seed');

        $this->idr = Currency::where('code', 'IDR')->firstOrFail();
        $this->education = LoanCategory::create([
            'name' => 'Education',
            'slug' => 'edu',
            'description' => 'Student loan'
        ]);

        // Borrower Setup
        $this->borrower = User::factory()->create();
        $this->borrower->roles()->attach(Role::where('name', 'borrower')->firstOrFail()->id);
        Profile::create(['user_id' => $this->borrower->id, 'full_name' => 'Borrower Satu', 'phone' => '081234567891']);
        KYC::create(['user_id' => $this->borrower->id, 'status' => 'approved']);
        Wallet::create(['user_id' => $this->borrower->id, 'currency_id' => $this->idr->id]);

        // Lender Setup (with Rp 10.000.000 balance)
        $this->lender = User::factory()->create();
        $this->lender->roles()->attach(Role::where('name', 'lender')->firstOrFail()->id);
        Profile::create(['user_id' => $this->lender->id, 'full_name' => 'Lender Automatik', 'phone' => '081234567892']);
        KYC::create(['user_id' => $this->lender->id, 'status' => 'approved']);
        Wallet::create([
            'user_id'           => $this->lender->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 10000000.00,
            'hold_balance'      => 0.00,
        ]);
    }

    // ─── REST API Tests (Phase 8) ─────────────────────────────────────────────

    public function test_api_can_fetch_marketplace_loans_with_pagination(): void
    {
        // Create 2 open loans
        LoanRequest::create([
            'borrower_id'            => $this->borrower->id,
            'category_id'            => $this->education->id,
            'amount'                 => 5000000,
            'interest_rate'          => 12.00,
            'duration'               => 12,
            'tenor_type'             => 'monthly',
            'purpose'                => 'Bayar SPP Sekolah',
            'currency_id'            => $this->idr->id,
            'status'                 => LoanRequest::STATUS_OPEN_FUNDING,
            'funded_percentage'      => 0.00,
        ]);

        $response = $this->actingAs($this->borrower)
            ->withSession(['two_factor_verified' => true])
            ->getJson(route('api.v1.marketplace.index'));

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'status', 'message', 'data', 'meta' => ['current_page', 'per_page', 'total', 'total_pages']
            ]);
    }

    public function test_api_can_fetch_loan_detail(): void
    {
        $loan = LoanRequest::create([
            'borrower_id'            => $this->borrower->id,
            'category_id'            => $this->education->id,
            'amount'                 => 3000000,
            'interest_rate'          => 10.00,
            'duration'               => 6,
            'tenor_type'             => 'monthly',
            'purpose'                => 'Buku Pelajaran',
            'currency_id'            => $this->idr->id,
            'status'                 => LoanRequest::STATUS_OPEN_FUNDING,
            'funded_percentage'      => 0.00,
        ]);

        $response = $this->actingAs($this->borrower)
            ->withSession(['two_factor_verified' => true])
            ->getJson(route('api.v1.marketplace.show', $loan->id));

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.purpose', 'Buku Pelajaran');
    }

    // ─── Auto-Invest Engine Tests (Phase 9) ───────────────────────────────────

    public function test_auto_invest_funds_matching_loan_automatically(): void
    {
        // 1. Lender configures auto-invest: Grade B to A, LTV max 80%, allocation Rp 2.000.000
        AutoInvestRule::create([
            'lender_id'               => $this->lender->id,
            'is_active'               => true,
            'min_grade'               => 'B',
            'max_grade'               => 'A',
            'max_allocation_per_loan' => 2000000.00,
            'max_ltv'                 => 80.00,
        ]);

        // 2. Borrower has a loan of Grade B (score 65)
        // Let's mock a loan with Risk Grade B
        $loan = LoanRequest::create([
            'borrower_id'            => $this->borrower->id,
            'category_id'            => $this->education->id,
            'amount'                 => 5000000,
            'interest_rate'          => 12.50,
            'duration'               => 12,
            'tenor_type'             => 'monthly',
            'purpose'                => 'Renovasi Laboratorium Sekolah',
            'currency_id'            => $this->idr->id,
            'status'                 => LoanRequest::STATUS_OPEN_FUNDING,
            'funded_percentage'      => 0.00,
            'risk_grade'             => 'B', // Grade B matches lender rule
        ]);

        // 3. Trigger Auto-Invest Artisan command
        $exitCode = Artisan::call('peer-lend:run-auto-invest');
        $this->assertEquals(0, $exitCode);

        // 4. Verify loan is partially funded by lender (Rp 2.000.000)
        $loan->refresh();
        $this->assertEquals(40.00, (float) $loan->funded_percentage); // 2jt / 5jt = 40%

        // 5. Verify Lender's wallet available balance reduced by Rp 2.000.000 and hold balance is Rp 2.000.000
        $lenderWallet = $this->lender->walletFor($this->idr->id);
        $this->assertEquals(8000000.00, (float) $lenderWallet->available_balance);
        $this->assertEquals(2000000.00, (float) $lenderWallet->hold_balance);
    }

    public function test_auto_invest_skips_non_matching_loan_grade(): void
    {
        // 1. Lender configures auto-invest for Grade A only
        AutoInvestRule::create([
            'lender_id'               => $this->lender->id,
            'is_active'               => true,
            'min_grade'               => 'A',
            'max_grade'               => 'A',
            'max_allocation_per_loan' => 2000000.00,
            'max_ltv'                 => 80.00,
        ]);

        // 2. Borrower has a loan of Grade C
        $loan = LoanRequest::create([
            'borrower_id'            => $this->borrower->id,
            'category_id'            => $this->education->id,
            'amount'                 => 5000000,
            'interest_rate'          => 16.50,
            'duration'               => 12,
            'tenor_type'             => 'monthly',
            'purpose'                => 'Bayar SPP Kuliah',
            'currency_id'            => $this->idr->id,
            'status'                 => LoanRequest::STATUS_OPEN_FUNDING,
            'funded_percentage'      => 0.00,
            'risk_grade'             => 'C', // Grade C does NOT match A
        ]);

        // 3. Trigger Auto-Invest
        Artisan::call('peer-lend:run-auto-invest');

        // 4. Verify loan remains unfunded
        $loan->refresh();
        $this->assertEquals(0.00, (float) $loan->funded_percentage);
    }

    // ─── POST /api/v1/loans/apply Tests ──────────────────────────────────────

    public function test_api_borrower_can_apply_loan_via_api(): void
    {
        $response = $this->actingAs($this->borrower)
            ->withSession(['two_factor_verified' => true])
            ->postJson(route('api.v1.loans.apply'), [
                'category_id' => $this->education->id,
                'amount'      => 3000000,
                'duration'    => 12,
                'purpose'     => 'Biaya Pendidikan S2',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'status', 'message', 'data' => [
                    'id', 'amount', 'duration', 'interest_rate',
                    'risk_grade', 'status', 'purpose', 'category', 'currency', 'created_at',
                ],
            ]);

        // Verify loan stored in DB as 'pending' (awaiting admin review)
        $this->assertDatabaseHas('loan_requests', [
            'borrower_id' => $this->borrower->id,
            'amount'      => 3000000,
            'status'      => LoanRequest::STATUS_PENDING,
        ]);
    }

    public function test_api_loan_apply_fails_validation_with_422(): void
    {
        $response = $this->actingAs($this->borrower)
            ->withSession(['two_factor_verified' => true])
            ->postJson(route('api.v1.loans.apply'), [
                'amount'   => 100, // Below minimum (1.000.000)
                'duration' => 99,  // Not in allowed set
                // Missing category_id and purpose
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', 'error')
            ->assertJsonStructure([
                'status', 'message', 'errors' => [
                    ['field', 'message'],
                ],
            ]);
    }
}
