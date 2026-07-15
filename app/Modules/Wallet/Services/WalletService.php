<?php

namespace App\Modules\Wallet\Services;

use App\Models\Currency;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Modules\Shared\Exceptions\InsufficientBalanceException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WalletService
{
    /**
     * Deposit funds into a user's wallet.
     */
    public function deposit(User $user, int $currencyId, string $amount, ?string $description = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $currencyId, $amount, $description) {
            $currency = Currency::findOrFail($currencyId);
            
            // Lock wallet row for update
            $wallet = Wallet::lockForUpdate()->firstOrCreate([
                'user_id'     => $user->id,
                'currency_id' => $currency->id,
            ], [
                'available_balance' => 0,
                'hold_balance'      => 0,
            ]);

            $before = $wallet->available_balance;
            $after = bcadd($before, $amount, 8);

            $wallet->update([
                'available_balance' => $after,
            ]);

            return WalletTransaction::create([
                'wallet_id'      => $wallet->id,
                'type'           => 'deposit',
                'amount'         => $amount,
                'balance_before' => $before,
                'balance_after'  => $after,
                'description'    => $description ?? "Deposit of {$amount} {$currency->code}",
            ]);
        });
    }

    /**
     * Withdraw funds from a user's wallet.
     *
     * @throws ValidationException when wallet balance is insufficient.
     */
    public function withdraw(User $user, int $currencyId, string $amount, ?string $description = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $currencyId, $amount, $description) {
            $currency = Currency::findOrFail($currencyId);

            $wallet = Wallet::lockForUpdate()->where([
                'user_id'     => $user->id,
                'currency_id' => $currency->id,
            ])->first();

            if (! $wallet || bccomp($wallet->available_balance, $amount, 8) < 0) {
                throw ValidationException::withMessages([
                    'amount' => ['Insufficient wallet balance for this withdrawal.'],
                ]);
            }

            $before = $wallet->available_balance;
            $after = bcsub($before, $amount, 8);

            $wallet->update([
                'available_balance' => $after,
            ]);

            return WalletTransaction::create([
                'wallet_id'      => $wallet->id,
                'type'           => 'withdraw',
                'amount'         => $amount,
                'balance_before' => $before,
                'balance_after'  => $after,
                'description'    => $description ?? "Withdrawal of {$amount} {$currency->code}",
            ]);
        });
    }

    /**
     * Lock/Hold a portion of the available balance.
     * (Commonly used when making a loan bid/funding or repayment hold).
     */
    public function holdBalance(Wallet $wallet, string $amount): void
    {
        DB::transaction(function () use ($wallet, $amount) {
            $lockedWallet = Wallet::lockForUpdate()->findOrFail($wallet->id);

            if (bccomp($lockedWallet->available_balance, $amount, 8) < 0) {
                throw new InsufficientBalanceException("Insufficient available balance to hold {$amount}.");
            }

            $lockedWallet->update([
                'available_balance' => bcsub($lockedWallet->available_balance, $amount, 8),
                'hold_balance'      => bcadd($lockedWallet->hold_balance, $amount, 8),
            ]);
        });
    }

    /**
     * Release a held balance back to the available balance.
     */
    public function releaseHold(Wallet $wallet, string $amount): void
    {
        DB::transaction(function () use ($wallet, $amount) {
            $lockedWallet = Wallet::lockForUpdate()->findOrFail($wallet->id);

            if (bccomp($lockedWallet->hold_balance, $amount, 8) < 0) {
                $amount = $lockedWallet->hold_balance; // Release whatever is left
            }

            $lockedWallet->update([
                'available_balance' => bcadd($lockedWallet->available_balance, $amount, 8),
                'hold_balance'      => bcsub($lockedWallet->hold_balance, $amount, 8),
            ]);
        });
    }

    /**
     * Spend a portion of the held balance directly (disburse funds / settle).
     */
    public function useHold(Wallet $wallet, string $amount): void
    {
        DB::transaction(function () use ($wallet, $amount) {
            $lockedWallet = Wallet::lockForUpdate()->findOrFail($wallet->id);

            if (bccomp($lockedWallet->hold_balance, $amount, 8) < 0) {
                $amount = $lockedWallet->hold_balance;
            }

            $lockedWallet->update([
                'hold_balance' => bcsub($lockedWallet->hold_balance, $amount, 8),
            ]);
        });
    }
}
