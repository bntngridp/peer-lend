<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\KYC;
use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletTransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Currency $idr;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Roles, Currencies and configurations
        $this->artisan('db:seed');

        $this->idr = Currency::where('code', 'IDR')->firstOrFail();

        // Create standard User with a Wallet and a borrower role
        $this->user = User::factory()->create();
        $borrowerRole = Role::where('name', 'borrower')->firstOrFail();
        $this->user->roles()->attach($borrowerRole->id);

        Wallet::create([
            'user_id'           => $this->user->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 0,
            'hold_balance'      => 0,
        ]);
    }

    /**
     * Verify that unverified KYC users cannot perform deposit/withdraw actions.
     */
    public function test_unverified_kyc_user_cannot_deposit_or_withdraw(): void
    {
        // 1. Send deposit request without KYC (should redirect to KYC)
        $response = $this->actingAs($this->user)->post(route('wallet.deposit'), [
            'currency_id' => $this->idr->id,
            'amount'      => 50000,
        ]);
        $response->assertRedirect(route('kyc.index'));

        // 2. Send withdraw request without KYC (should redirect to KYC)
        $response = $this->actingAs($this->user)->post(route('wallet.withdraw'), [
            'currency_id' => $this->idr->id,
            'amount'      => 10000,
        ]);
        $response->assertRedirect(route('kyc.index'));
    }

    /**
     * Verify successful deposits, withdrawals, and overdraft blockages.
     */
    public function test_verified_user_can_deposit_and_withdraw_funds(): void
    {
        // 1. Approve KYC for the user first
        KYC::create([
            'user_id' => $this->user->id,
            'status'  => 'approved',
        ]);

        // 2. Deposit Rp 1.000.000
        $response = $this->actingAs($this->user)->post(route('wallet.deposit'), [
            'currency_id' => $this->idr->id,
            'amount'      => 1000000,
        ]);
        $response->assertRedirect(route('wallet.index'));

        $this->assertDatabaseHas('wallets', [
            'user_id'           => $this->user->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 1000000,
        ]);

        $this->assertDatabaseHas('wallet_transactions', [
            'type'           => 'deposit',
            'amount'         => 1000000,
            'balance_before' => 0,
            'balance_after'  => 1000000,
        ]);

        // 3. Withdraw Rp 400.000
        $response = $this->actingAs($this->user)->post(route('wallet.withdraw'), [
            'currency_id' => $this->idr->id,
            'amount'      => 400000,
        ]);
        $response->assertRedirect(route('wallet.index'));

        $this->assertDatabaseHas('wallets', [
            'user_id'           => $this->user->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 600000,
        ]);

        $this->assertDatabaseHas('wallet_transactions', [
            'type'           => 'withdraw',
            'amount'         => 400000,
            'balance_before' => 1000000,
            'balance_after'  => 600000,
        ]);

        // 4. Overdraft: Attempt to withdraw Rp 700.000 (should fail validation due to insufficient funds)
        $response = $this->actingAs($this->user)->post(route('wallet.withdraw'), [
            'currency_id' => $this->idr->id,
            'amount'      => 700000,
        ]);
        
        $response->assertSessionHasErrors('amount');
        
        // Assert balance remains unchanged
        $this->assertDatabaseHas('wallets', [
            'user_id'           => $this->user->id,
            'currency_id'       => $this->idr->id,
            'available_balance' => 600000,
        ]);
    }
}
