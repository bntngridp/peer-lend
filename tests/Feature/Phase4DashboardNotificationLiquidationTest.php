<?php

namespace Tests\Feature;

use App\Models\KYC;
use App\Models\LoanFunding;
use App\Models\LoanInstallment;
use App\Models\LoanRequest;
use App\Models\Notification;
use App\Models\User;
use App\Models\Wallet;
use App\Modules\Loan\Services\LiquidationService;
use App\Modules\Shared\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase4DashboardNotificationLiquidationTest extends TestCase
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
            'phone'     => '0812345' . rand(10000, 99999), // unique per user
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
        $idrId = \App\Models\Currency::firstOrCreate(
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

    // ─── Dashboard Tests ──────────────────────────────────────────────────────

    public function test_borrower_can_access_dashboard_and_see_stats(): void
    {
        $user = $this->createVerifiedUser('borrower');
        $this->createWallet($user, '500000');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Welcome back');
    }

    public function test_admin_can_access_dashboard_and_see_platform_stats(): void
    {
        $admin = $this->createVerifiedUser('admin');
        $this->createWallet($admin);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Platform Overview');
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    // ─── Notification Tests ───────────────────────────────────────────────────

    public function test_notification_service_creates_notification_in_database(): void
    {
        $user    = $this->createVerifiedUser();
        $service = app(NotificationService::class);

        $service->notifyKycApproved($user);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type'    => NotificationService::TYPE_KYC_APPROVED,
            'read_at' => null,
        ]);
    }

    public function test_user_can_view_notification_list(): void
    {
        $user = $this->createVerifiedUser();

        Notification::create([
            'user_id' => $user->id,
            'type'    => 'kyc_approved',
            'title'   => 'KYC Approved',
            'body'    => 'Your KYC has been approved.',
        ]);
        Notification::create([
            'user_id' => $user->id,
            'type'    => 'kyc_approved',
            'title'   => 'KYC Approved',
            'body'    => 'Your KYC has been approved.',
        ]);
        Notification::create([
            'user_id' => $user->id,
            'type'    => 'kyc_approved',
            'title'   => 'KYC Approved',
            'body'    => 'Your KYC has been approved.',
        ]);

        $response = $this->actingAs($user)->get('/notifications');

        $response->assertStatus(200);
        $response->assertSee('Notifications');
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $user         = $this->createVerifiedUser();
        $notification = Notification::create([
            'user_id' => $user->id,
            'title'   => 'Test',
            'body'    => 'Test notification',
            'type'    => 'kyc_approved',
            'read_at' => null,
        ]);

        $this->actingAs($user)
            ->post("/notifications/{$notification->id}/read")
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = $this->createVerifiedUser();

        for ($i = 0; $i < 5; $i++) {
            Notification::create([
                'user_id' => $user->id,
                'title'   => 'Notification ' . $i,
                'body'    => 'Body ' . $i,
                'type'    => 'kyc_approved',
                'read_at' => null,
            ]);
        }

        $this->actingAs($user)
            ->post('/notifications/read-all')
            ->assertRedirect();

        $this->assertSame(
            0,
            Notification::where('user_id', $user->id)->whereNull('read_at')->count()
        );
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        $user1 = $this->createVerifiedUser();
        $user2 = $this->createVerifiedUser();

        $notification = Notification::create([
            'user_id' => $user2->id,
            'title'   => 'Test',
            'body'    => 'Body',
            'type'    => 'kyc_approved',
            'read_at' => null,
        ]);

        $this->actingAs($user1)
            ->post("/notifications/{$notification->id}/read")
            ->assertStatus(403);

        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_kyc_approval_triggers_notification_to_user(): void
    {
        $kycUser = $this->createVerifiedUser();
        $admin   = $this->createVerifiedUser('admin');

        // Update the existing approved KYC to pending for this test
        $kyc = KYC::where('user_id', $kycUser->id)->firstOrFail();
        $kyc->update(['status' => 'pending', 'reviewed_by' => null, 'reviewed_at' => null]);

        $service = app(\App\Modules\KYC\Services\KYCService::class);
        $service->approveKYC($kyc, $admin);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $kycUser->id,
            'type'    => NotificationService::TYPE_KYC_APPROVED,
        ]);
    }

    public function test_kyc_rejection_triggers_notification_with_reason(): void
    {
        $kycUser = $this->createVerifiedUser();
        $admin   = $this->createVerifiedUser('admin');

        // Update the existing approved KYC to pending for this test
        $kyc = KYC::where('user_id', $kycUser->id)->firstOrFail();
        $kyc->update(['status' => 'pending', 'reviewed_by' => null, 'reviewed_at' => null]);

        $service = app(\App\Modules\KYC\Services\KYCService::class);
        $service->rejectKYC($kyc, $admin, 'Document unclear');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $kycUser->id,
            'type'    => NotificationService::TYPE_KYC_REJECTED,
        ]);
    }

    // ─── Liquidation Tests ────────────────────────────────────────────────────

    public function test_liquidation_service_returns_mock_oracle_price(): void
    {
        $service = app(LiquidationService::class);

        $btcPrice  = (float) $service->getMockOraclePrice('BTC');
        $ethPrice  = (float) $service->getMockOraclePrice('ETH');
        $usdtPrice = (float) $service->getMockOraclePrice('USDT');

        $this->assertGreaterThan(0, $btcPrice);
        $this->assertGreaterThan(0, $ethPrice);
        $this->assertSame(15500.0, $usdtPrice);
    }

    public function test_loan_with_ltv_below_threshold_is_not_liquidated(): void
    {
        $borrower = $this->createVerifiedUser('borrower');
        $this->createWallet($borrower);

        $btcCurrency = \App\Models\Currency::firstOrCreate(
            ['code' => 'BTC'],
            ['name' => 'Bitcoin', 'symbol' => 'BTC']
        );

        // Loan amount 1_000_000 with massive collateral (very low LTV)
        $idrCurrency = \App\Models\Currency::firstOrCreate(['code' => 'IDR'], ['name' => 'Indonesian Rupiah', 'symbol' => 'Rp']);
        $category    = \App\Models\LoanCategory::firstOrCreate(['name' => 'Personal'], ['description' => 'Personal loan']);
        $loan = LoanRequest::create([
            'borrower_id'            => $borrower->id,
            'category_id'            => $category->id,
            'currency_id'            => $idrCurrency->id,
            'amount'                 => '1000000',
            'purpose'                => 'Test purpose',
            'description'            => 'Test loan for LTV',
            'risk_grade'             => 'A',
            'status'                 => LoanRequest::STATUS_ACTIVE,
            'collateral_currency_id' => $btcCurrency->id,
            'collateral_amount'      => '10.00000000',
            'initial_ltv'            => 5.00,
            'liquidation_ltv'        => 80.00,
            'current_ltv'            => 5.00,
            'liquidation_price'      => '0.00000000',
            'interest_rate'          => '12.00',
            'duration'               => 12,
            'tenor_type'             => 'monthly',
            'funded_percentage'      => 0.00,
        ]);

        $service       = app(LiquidationService::class);
        $wasLiquidated = $service->updateLtv($loan);

        $this->assertFalse($wasLiquidated);
        $this->assertNotEquals('liquidated', $loan->fresh()->status);
    }

    public function test_artisan_update_ltv_command_runs_successfully(): void
    {
        $this->artisan('peer-lend:update-ltv')
             ->assertExitCode(0);
    }

    public function test_update_ltv_dry_run_does_not_liquidate(): void
    {
        $this->artisan('peer-lend:update-ltv --dry-run')
             ->assertExitCode(0);
    }
}
