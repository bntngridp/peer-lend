<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\KYC;
use App\Models\LoanCategory;
use App\Models\LoanRequest;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowerMarketplaceViewTest extends TestCase
{
    use RefreshDatabase;

    private User $borrower;
    private User $lender;
    private LoanRequest $loan;

    protected function setUp(): void
    {
        parent::setUp();

        $borrowerRole = Role::firstOrCreate(['name' => 'borrower'], ['display_name' => 'Borrower']);
        $lenderRole = Role::firstOrCreate(['name' => 'lender'], ['display_name' => 'Lender']);

        $idr = Currency::firstOrCreate(['code' => 'IDR'], [
            'name' => 'Indonesian Rupiah',
            'symbol' => 'Rp',
            'type' => 'fiat',
            'decimal_places' => 2,
        ]);

        $category = LoanCategory::firstOrCreate(['name' => 'Business Expansion'], [
            'slug' => 'business-expansion',
            'description' => 'Business loan category',
            'min_amount' => 1000000,
            'max_amount' => 50000000,
            'min_duration' => 3,
            'max_duration' => 24,
            'min_interest_rate' => 8,
            'max_interest_rate' => 20,
        ]);

        $this->borrower = User::factory()->create(['email' => 'testborrower@example.com']);
        $this->borrower->roles()->attach($borrowerRole);
        Profile::create([
            'user_id' => $this->borrower->id,
            'full_name' => 'Test Borrower',
            'phone_number' => '081234567890',
            'address' => 'Jakarta, Indonesia',
        ]);
        KYC::create(['user_id' => $this->borrower->id, 'status' => 'approved']);

        $this->lender = User::factory()->create(['email' => 'testlender@example.com']);
        $this->lender->roles()->attach($lenderRole);
        Profile::create([
            'user_id' => $this->lender->id,
            'full_name' => 'Test Lender',
            'phone_number' => '081234567891',
            'address' => 'Jakarta, Indonesia',
        ]);
        KYC::create(['user_id' => $this->lender->id, 'status' => 'approved']);

        $this->loan = LoanRequest::create([
            'borrower_id' => $this->borrower->id,
            'category_id' => $category->id,
            'currency_id' => $idr->id,
            'amount' => 10000000,
            'duration' => 12,
            'interest_rate' => 12.5,
            'risk_grade' => 'A',
            'purpose' => 'Business Inventory Expansion',
            'status' => 'open_funding',
        ]);
    }

    public function test_borrower_owner_can_view_own_loan_in_marketplace(): void
    {
        $response = $this->actingAs($this->borrower)->get(route('marketplace.show', $this->loan->id));

        $response->assertStatus(200);
        $response->assertSee('Borrower Campaign Hub');
        $response->assertSee('Your Active Loan Campaign');
        $response->assertSee('Listing is Live in Marketplace');
        $response->assertSee('Business Inventory Expansion');
    }

    public function test_lender_can_view_marketplace_loan_with_invest_calculator(): void
    {
        $response = $this->actingAs($this->lender)->get(route('marketplace.show', $this->loan->id));

        $response->assertStatus(200);
        $response->assertSee('Invest in this Loan');
        $response->assertSee('AMOUNT TO INVEST (IDR)');
        $response->assertSee('INVEST NOW');
    }
}
