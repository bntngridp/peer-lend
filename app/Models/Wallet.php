<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'currency_id',
        'available_balance',
        'hold_balance',
    ];

    protected function casts(): array
    {
        return [
            'available_balance' => 'decimal:8',
            'hold_balance'      => 'decimal:8',
        ];
    }

    // ─── Computed ─────────────────────────────────────────────────────

    public function getTotalBalanceAttribute(): string
    {
        return bcadd($this->available_balance, $this->hold_balance, 8);
    }

    public function hasSufficientBalance(string $amount): bool
    {
        return bccomp($this->available_balance, $amount, 8) >= 0;
    }

    // ─── Relationships ────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
