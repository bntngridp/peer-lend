<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'reference_id',
        'reference_type',
        'description',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'         => 'decimal:8',
            'balance_before' => 'decimal:8',
            'balance_after'  => 'decimal:8',
            'created_at'     => 'datetime',
        ];
    }

    // ─── Scopes ──────────────────────────────────────────────────────

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDebit($query)
    {
        return $query->whereIn('type', [
            'withdraw', 'funding', 'fee', 'penalty',
        ]);
    }

    public function scopeCredit($query)
    {
        return $query->whereIn('type', [
            'deposit', 'repayment', 'interest', 'refund', 'loan_disbursement',
        ]);
    }

    // ─── Relationships ────────────────────────────────────────────────

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
