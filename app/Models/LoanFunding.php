<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanFunding extends Model
{
    use HasUuids;

    protected $fillable = [
        'loan_id',
        'lender_id',
        'amount',
        'percentage',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount'     => 'decimal:2',
            'percentage' => 'decimal:2',
        ];
    }

    // ─── Status Checks ────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    // ─── Relationships ─────────────────────────────────────────────────

    public function loan(): BelongsTo
    {
        return $this->belongsTo(LoanRequest::class, 'loan_id');
    }

    public function lender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lender_id');
    }
}
