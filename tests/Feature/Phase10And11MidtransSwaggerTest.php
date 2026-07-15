<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\KYC;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class Phase10And11MidtransSwaggerTest extends TestCase
{
    use RefreshDatabase;

    private User $borrower;
    private Currency $idr;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles, currencies, settings
        $this->artisan('db:seed');

        $this->idr = Currency::where('code', 'IDR')->firstOrFail();

        // Setup Borrower
        $this->borrower = User::factory()->create();
        $this->borrower->roles()->attach(Role::where('name', 'borrower')->firstOrFail()->id);
        Profile::create(['user_id' => $this->borrower->id, 'full_name' => 'Borrower Midtrans', 'phone' => '081234567895']);
        KYC::create(['user_id' => $this->borrower->id, 'status' => 'approved']);
        Wallet::create([
            'user_id'           => $this->borrower->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 0.00,
            'hold_balance'      => 0.00,
        ]);

        // Configure dummy midtrans keys for testing
        config(['midtrans.server_key' => 'SB-Mid-server-testkey']);
    }

    // ─── Phase 10: Payment Gateway (Midtrans) Tests ─────────────────────────

    public function test_can_initiate_deposit_and_get_snap_token(): void
    {
        // Fake Midtrans Snap API call
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token'        => 'mock-snap-token-12345',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v1/pay?token=mock-snap-token-12345',
            ], 201),
        ]);

        $response = $this->actingAs($this->borrower)
            ->withSession(['two_factor_verified' => true])
            ->postJson(route('wallet.deposit.initiate'), [
                'amount' => 500000,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.snap_token', 'mock-snap-token-12345');

        $this->assertDatabaseHas('payments', [
            'user_id'        => $this->borrower->id,
            'amount'         => 500000.00,
            'status'         => 'pending',
            'gateway_ref_id' => 'mock-snap-token-12345',
        ]);
    }

    public function test_webhook_successfully_credits_wallet_on_settlement(): void
    {
        // 1. Create a pending payment
        $payment = Payment::create([
            'user_id' => $this->borrower->id,
            'gateway' => 'midtrans',
            'amount'  => 1000000.00,
            'status'  => 'pending',
        ]);

        // 2. Generate valid signature key
        // SHA512(order_id + status_code + gross_amount + server_key)
        $orderId     = $payment->id;
        $statusCode  = '200';
        $grossAmount = '1000000.00';
        $serverKey   = 'SB-Mid-server-testkey';
        $signature   = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        // 3. Fire Midtrans webhook POST request
        $response = $this->postJson(route('payment.webhook'), [
            'order_id'           => $orderId,
            'status_code'        => $statusCode,
            'gross_amount'       => $grossAmount,
            'signature_key'      => $signature,
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        // Verify status payment changed to success
        $payment->refresh();
        $this->assertEquals('success', $payment->status);
        $this->assertNotNull($payment->wallet_transaction_id);

        // Verify Wallet balance is updated
        $wallet = $this->borrower->walletFor($this->idr->id);
        $this->assertEquals(1000000.00, (float) $wallet->available_balance);

        // Verify WalletTransaction created
        $this->assertDatabaseHas('wallet_transactions', [
            'id'    => $payment->wallet_transaction_id,
            'type'  => 'deposit',
            'amount'=> 1000000.00,
        ]);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $payment = Payment::create([
            'user_id' => $this->borrower->id,
            'gateway' => 'midtrans',
            'amount'  => 250000.00,
            'status'  => 'pending',
        ]);

        $response = $this->postJson(route('payment.webhook'), [
            'order_id'           => $payment->id,
            'status_code'        => '200',
            'gross_amount'       => '250000.00',
            'signature_key'      => 'fake-signature-key-value', // Invalid signature
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('status', 'error');

        // Wallet balance should remain 0
        $wallet = $this->borrower->walletFor($this->idr->id);
        $this->assertEquals(0.00, (float) $wallet->available_balance);
        $this->assertEquals('pending', $payment->fresh()->status);
    }

    // ─── Phase 11: Swagger API Documentation Tests ─────────────────────────

    public function test_api_documentation_is_publicly_accessible(): void
    {
        $response = $this->get(route('api.docs'));

        $response->assertStatus(200)
            ->assertSee('openapi.json')
            ->assertSee('swagger-ui');
    }
}
