<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\KYC;
use App\Models\LoanCategory;
use App\Models\LoanRequest;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase7ChatAndAgreementTest extends TestCase
{
    use RefreshDatabase;

    private User $borrower;
    private User $lender;
    private User $intruder;
    private User $admin;
    private LoanRequest $loan;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic settings, roles, currencies
        $this->artisan('db:seed');

        $idr = Currency::where('code', 'IDR')->firstOrFail();
        $category = LoanCategory::create([
            'name' => 'Education Development',
            'slug' => 'education',
            'description' => 'Collateral based school funding'
        ]);

        // Users Setup
        $this->borrower = User::factory()->create();
        $this->borrower->roles()->attach(Role::where('name', 'borrower')->firstOrFail()->id);
        Profile::create([
            'user_id'   => $this->borrower->id,
            'full_name' => 'Peminjam Aktif',
            'phone'     => '081234567891'
        ]);
        KYC::create(['user_id' => $this->borrower->id, 'status' => 'approved']);
        Wallet::create(['user_id' => $this->borrower->id, 'currency_id' => $idr->id]);

        $this->lender = User::factory()->create();
        $this->lender->roles()->attach(Role::where('name', 'lender')->firstOrFail()->id);
        Profile::create([
            'user_id'   => $this->lender->id,
            'full_name' => 'Pendana Setia',
            'phone'     => '081234567892'
        ]);
        KYC::create(['user_id' => $this->lender->id, 'status' => 'approved']);
        Wallet::create(['user_id' => $this->lender->id, 'currency_id' => $idr->id, 'available_balance' => 15000000]);

        $this->intruder = User::factory()->create();
        $this->intruder->roles()->attach(Role::where('name', 'lender')->firstOrFail()->id);
        Profile::create([
            'user_id'   => $this->intruder->id,
            'full_name' => 'Orang Asing',
            'phone'     => '081234567893'
        ]);
        KYC::create(['user_id' => $this->intruder->id, 'status' => 'approved']);
        Wallet::create(['user_id' => $this->intruder->id, 'currency_id' => $idr->id]);

        $this->admin = User::factory()->create();
        $this->admin->roles()->attach(Role::where('name', 'admin')->firstOrFail()->id);

        // Create active funded loan request
        $this->loan = LoanRequest::create([
            'borrower_id'            => $this->borrower->id,
            'category_id'            => $category->id,
            'amount'                 => 10000000,
            'interest_rate'          => 12.00,
            'duration'               => 12,
            'tenor_type'             => 'monthly',
            'purpose'                => 'Bayar kuliah semester 5',
            'currency_id'            => $idr->id,
            'status'                 => LoanRequest::STATUS_OPEN_FUNDING,
            'funded_percentage'      => 0.00,
        ]);
    }

    // ─── Security Chat Authorization Tests ────────────────────────────────────

    public function test_unauthorized_user_cannot_access_chat_api(): void
    {
        // Intruder is not borrower, has not funded, and is not admin
        $response = $this->actingAs($this->intruder)
            ->withSession(['google2fa_verified' => true])
            ->getJson(route('loans.messages.fetch', $this->loan->id));
        $response->assertStatus(403);
    }

    public function test_borrower_can_send_and_fetch_messages(): void
    {
        // Borrower sends a message
        $response = $this->actingAs($this->borrower)
            ->withSession(['google2fa_verified' => true])
            ->postJson(route('loans.messages.send', $this->loan->id), [
                'message' => 'Halo pendana, terima kasih banyak.'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message.message', 'Halo pendana, terima kasih banyak.');

        // Borrower fetches messages
        $response = $this->actingAs($this->borrower)
            ->withSession(['google2fa_verified' => true])
            ->getJson(route('loans.messages.fetch', $this->loan->id));
        $response->assertStatus(200)
            ->assertJsonCount(1, 'messages');
    }

    public function test_lender_can_chat_after_funding_the_loan(): void
    {
        // Lender funds Rp 5.000.000 (becomes participant)
        $this->actingAs($this->lender)
            ->withSession(['google2fa_verified' => true])
            ->post(route('marketplace.fund', $this->loan->id), [
                'amount' => 5000000
            ]);

        // Now lender can fetch and post messages
        $response = $this->actingAs($this->lender)
            ->withSession(['google2fa_verified' => true])
            ->getJson(route('loans.messages.fetch', $this->loan->id));
        $response->assertStatus(200);

        $response = $this->actingAs($this->lender)
            ->withSession(['google2fa_verified' => true])
            ->postJson(route('loans.messages.send', $this->loan->id), [
                'message' => 'Sama-sama Kak! Semoga usahanya lancar.'
            ]);
        $response->assertStatus(200);
    }

    // ─── Legal Agreement Page Tests ───────────────────────────────────────────

    public function test_agreement_page_shows_correct_loan_details(): void
    {
        // Access agreement as borrower (active status)
        $this->loan->update(['status' => LoanRequest::STATUS_ACTIVE]);

        $response = $this->actingAs($this->borrower)
            ->withSession(['google2fa_verified' => true])
            ->get(route('loans.agreement', $this->loan->id));
        
        $response->assertStatus(200)
            ->assertSee('Perjanjian Pinjam Meminjam Uang')
            ->assertSee('Bayar kuliah semester 5')
            ->assertSee('Pihak Pertama');
    }

    public function test_intruder_cannot_view_agreement(): void
    {
        $this->loan->update(['status' => LoanRequest::STATUS_ACTIVE]);

        $response = $this->actingAs($this->intruder)
            ->withSession(['google2fa_verified' => true])
            ->get(route('loans.agreement', $this->loan->id));
        $response->assertStatus(403);
    }
}
