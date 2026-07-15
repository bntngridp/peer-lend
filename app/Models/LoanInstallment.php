<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanInstallment extends Model
{
    use HasUuids;

    protected $fillable = [
        'loan_id',
        'installment_number',
        'due_date',
        'principal_amount',
        'interest_amount',
        'penalty_amount',
        'total_amount',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date'          => 'date',
            'principal_amount'  => 'decimal:2',
            'interest_amount'   => 'decimal:2',
            'penalty_amount'    => 'decimal:2',
            'total_amount'      => 'decimal:2',
            'paid_at'           => 'datetime',
        ];
    }

    // ─── Status Constants ─────────────────────────────────────────────

    const STATUS_PENDING  = 'pending';
    const STATUS_PAID     = 'paid';
    const STATUS_OVERDUE  = 'overdue';
    const STATUS_WAIVED   = 'waived';

    // ─── Status Checks ────────────────────────────────────────────────

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_OVERDUE;
    }

    public function isDue(): bool
    {
        return $this->status === self::STATUS_PENDING
            && $this->due_date->isToday();
    }

    // ─── Computed ─────────────────────────────────────────────────────

    public function getTotalDueAttribute(): string
    {
        return bcadd($this->total_amount, $this->penalty_amount, 2);
    }

    // ─── Relationships ─────────────────────────────────────────────────

    public function loan(): BelongsTo
    {
        return $this->belongsTo(LoanRequest::class, 'loan_id');
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class, 'installment_id');
    }
}
