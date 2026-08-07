<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\LoanCategory;
use App\Models\LoanRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoanRejectTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $borrower;
    private LoanRequest $loan;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin'], ['guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'borrower'], ['guard_name' => 'web']);

        $this->admin = User::factory()->create();
        $this->admin->roles()->attach(Role::where('name', 'admin')->first());

        $this->borrower = User::factory()->create();
        $this->borrower->roles()->attach(Role::where('name', 'borrower')->first());

        $fiat = Currency::firstOrCreate(['code' => 'IDR'], [
            'name' => 'Indonesian Rupiah',
            'symbol' => 'Rp',
            'type' => 'fiat',
            'is_active' => true,
        ]);

        $category = LoanCategory::firstOrCreate(['name' => 'Personal Loan'], [
            'is_active' => true,
        ]);

        $this->loan = LoanRequest::create([
            'borrower_id' => $this->borrower->id,
            'category_id' => $category->id,
            'amount' => 5000000,
            'interest_rate' => 12.00,
            'duration' => 6,
            'tenor_type' => 'monthly',
            'purpose' => 'Working Capital',
            'currency_id' => $fiat->id,
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_reject_pending_loan_request(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.loans.reject', $this->loan->id), [
                'rejection_reason' => 'High debt ratio',
            ]);

        $response->assertRedirect(route('admin.loans.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('loan_requests', [
            'id' => $this->loan->id,
            'status' => 'rejected',
        ]);
    }

    public function test_cannot_reject_already_approved_loan(): void
    {
        $this->loan->update(['status' => 'open_funding']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.loans.reject', $this->loan->id), [
                'rejection_reason' => 'Invalid status',
            ]);

        $response->assertSessionHasErrors(['status']);
        $this->assertEquals('open_funding', $this->loan->fresh()->status);
    }
}
