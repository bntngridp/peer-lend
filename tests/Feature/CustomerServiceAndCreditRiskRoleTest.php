<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerServiceAndCreditRiskRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_customer_service_user_is_directed_to_staff_dashboard_and_can_access_kyc_and_user_directory()
    {
        $csUser = User::where('email', 'cs1@lendflow.com')->firstOrFail();

        // 1. Dashboard access -> Directed to staff admin dashboard view
        $response = $this->actingAs($csUser)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('role', 'admin');
        $response->assertSee('System Administration');

        // 2. Access KYC Verification page -> 200 OK
        $kycResponse = $this->actingAs($csUser)->get(route('admin.kyc.index'));
        $kycResponse->assertStatus(200);
        $kycResponse->assertSee('KYC Review Queue');

        // 3. Access User Directory -> 200 OK
        $userResponse = $this->actingAs($csUser)->get(route('admin.users.index'));
        $userResponse->assertStatus(200);
        $userResponse->assertSee('User Management');

        // 4. Access Admin Financial Config -> 403 Forbidden (RBAC enforced!)
        $financialResponse = $this->actingAs($csUser)->get(route('admin.financials.index'));
        $financialResponse->assertStatus(403);
    }

    public function test_collection_officer_user_is_directed_to_staff_dashboard_and_can_access_loans_and_transactions()
    {
        $crUser = User::where('email', 'collector1@lendflow.com')->firstOrFail();

        // 1. Dashboard access -> Directed to staff admin dashboard view
        $response = $this->actingAs($crUser)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('role', 'admin');
        $response->assertSee('System Administration');

        // 2. Access Loans Review page -> 200 OK
        $loansResponse = $this->actingAs($crUser)->get(route('admin.loans.index'));
        $loansResponse->assertStatus(200);
        $loansResponse->assertSee('Loan Applications');

        // 3. Access Transactions Audit page -> 200 OK
        $txResponse = $this->actingAs($crUser)->get(route('admin.transactions.index'));
        $txResponse->assertStatus(200);
        $txResponse->assertSee('Transactions Audit');

        // 4. Access KYC Verification -> 403 Forbidden (RBAC enforced!)
        $kycResponse = $this->actingAs($crUser)->get(route('admin.kyc.index'));
        $kycResponse->assertStatus(403);
    }
}
