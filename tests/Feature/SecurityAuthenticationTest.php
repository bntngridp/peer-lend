<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Currency;
use App\Models\KYC;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use App\Modules\Auth\Services\Google2FAService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Currency $idr;
    private Google2FAService $google2faService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->idr = Currency::where('code', 'IDR')->firstOrFail();
        $this->google2faService = new Google2FAService();

        // Setup verified profile for testing
        $this->user = User::factory()->create();
        $this->user->roles()->attach(Role::where('name', 'borrower')->firstOrFail()->id);
        
        Profile::create([
            'user_id'   => $this->user->id,
            'full_name' => 'Bintang Ridwan',
            'phone'     => '081234567899',
        ]);

        Wallet::create([
            'user_id'           => $this->user->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 0,
            'hold_balance'      => 0,
        ]);
    }

    /**
     * Test Google2FAService logic.
     */
    public function test_google_2fa_service_generates_and_verifies_correctly(): void
    {
        $secret = $this->google2faService->generateSecret();
        $this->assertEquals(16, strlen($secret));

        // Generate the code manually for current time
        $timeSlice = floor(time() / 30);
        
        // Calculate dynamic code
        $reflection = new \ReflectionClass(Google2FAService::class);
        $method = $reflection->getMethod('calculateCode');
        $method->setAccessible(true);
        $validCode = $method->invoke($this->google2faService, $secret, $timeSlice);

        $this->assertTrue($this->google2faService->verifyCode($secret, $validCode));
        $this->assertFalse($this->google2faService->verifyCode($secret, '999999')); // invalid code
    }

    /**
     * Test setup and activation of 2FA.
     */
    public function test_user_can_setup_and_enable_2fa(): void
    {
        // 1. Visit setup page to verify it renders correctly
        $response = $this->actingAs($this->user)->get(route('2fa.setup'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.2fa-setup');

        // Generate secret manually (same as what showSetup() would do)
        $secret = $this->google2faService->generateSecret();

        // 2. Submit wrong code — inject temp secret into session
        $response = $this->actingAs($this->user)
            ->withSession(['google2fa_temp_secret' => $secret])
            ->post(route('2fa.enable'), [
                'code' => '111111',
            ]);
        $response->assertSessionHasErrors('code');

        // 3. Submit valid code — inject temp secret into session
        $timeSlice = floor(time() / 30);
        $reflection = new \ReflectionClass(Google2FAService::class);
        $method = $reflection->getMethod('calculateCode');
        $method->setAccessible(true);
        $validCode = $method->invoke($this->google2faService, $secret, $timeSlice);

        $response = $this->actingAs($this->user)
            ->withSession(['google2fa_temp_secret' => $secret])
            ->post(route('2fa.enable'), [
                'code' => $validCode,
            ]);
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        // Assert 2FA enabled on DB
        $this->user->refresh();
        $this->assertTrue((bool)$this->user->google2fa_enabled);
        $this->assertEquals($secret, $this->user->google2fa_secret);
    }

    /**
     * Test 2FA middleware guards on session.
     */
    public function test_2fa_middleware_redirects_unverified_sessions(): void
    {
        // Enable 2FA for user
        $this->user->update([
            'google2fa_enabled' => true,
            'google2fa_secret'  => $this->google2faService->generateSecret(),
        ]);
        $this->user->refresh(); // Reload to get saved secret

        // Access dashboard without verified session -> redirected to verify form
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertRedirect(route('2fa.verify'));

        // Visit verify form directly
        $response = $this->actingAs($this->user)->get(route('2fa.verify'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.2fa-verify');

        // Submit correct code
        $timeSlice = floor(time() / 30);
        $reflection = new \ReflectionClass(Google2FAService::class);
        $method = $reflection->getMethod('calculateCode');
        $method->setAccessible(true);
        $validCode = $method->invoke($this->google2faService, $this->user->google2fa_secret, $timeSlice);

        $response = $this->actingAs($this->user)->post(route('2fa.verify.post'), [
            'code' => $validCode,
        ]);
        $response->assertRedirect(route('dashboard'));

        // Can access dashboard now (using google2fa_verified in session)
        $response = $this->actingAs($this->user)
            ->withSession(['google2fa_verified' => true])
            ->get(route('dashboard'));
        $response->assertStatus(200);
    }

    /**
     * Test mock OCR document scanning and validation logic.
     */
    public function test_kyc_ocr_matching_success_and_mismatch_failure(): void
    {
        Storage::fake('local');

        // Scenario 1: Name matches profile -> Status: Pending admin review, NIK extracted
        $ktpFile = UploadedFile::fake()->image('ktp.jpg');
        $selfieFile = UploadedFile::fake()->image('selfie.jpg');

        $response = $this->actingAs($this->user)->post(route('kyc.submit'), [
            'ktp'    => $ktpFile,
            'selfie' => $selfieFile,
        ]);

        $response->assertRedirect();
        
        $kyc = KYC::where('user_id', $this->user->id)->firstOrFail();
        $this->assertEquals('pending', $kyc->status);
        $this->assertNotEmpty($kyc->nik);
        $this->assertEquals(16, strlen($kyc->nik));

        // Verify Audit Log generated
        $this->assertDatabaseHas('audit_logs', [
            'user_id'    => $this->user->id,
            'action'     => 'kyc_submit',
            'model_type' => KYC::class,
        ]);

        // Clear for next scenario
        $kyc->delete();

        // Scenario 2: Name Mismatch (simulated via bad blurry filename) -> Auto-rejected
        $blurryKtp = UploadedFile::fake()->image('blurry_ktp.jpg');

        $response = $this->actingAs($this->user)->post(route('kyc.submit'), [
            'ktp'    => $blurryKtp,
            'selfie' => $selfieFile,
        ]);

        $response->assertRedirect();

        $kyc = KYC::where('user_id', $this->user->id)->firstOrFail();
        $this->assertEquals('rejected', $kyc->status);
        $this->assertStringContainsString('OCR Validation failed', $kyc->rejected_reason);
    }

    /**
     * Test Wallet Transactions record Audit Logs.
     */
    public function test_wallet_transactions_record_audit_logs(): void
    {
        // Approve KYC first to bypass the kyc middleware
        KYC::create([
            'user_id' => $this->user->id,
            'status'  => 'approved',
        ]);

        // Act as user and perform wallet deposit
        $response = $this->actingAs($this->user)->post(route('wallet.deposit'), [
            'currency_id' => $this->idr->id,
            'amount'      => 500000.00,
        ]);

        $response->assertRedirect(route('wallet.index'));

        // Assert audit log created
        $wallet = Wallet::where('user_id', $this->user->id)->firstOrFail();
        $this->assertDatabaseHas('audit_logs', [
            'user_id'    => $this->user->id,
            'action'     => 'wallet_deposit',
            'model_type' => Wallet::class,
            'model_id'   => $wallet->id,
        ]);
    }
}
