<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LoanRequest extends Model
{
    use HasUuids;

    protected $fillable = [
        'borrower_id',
        'category_id',
        'amount',
        'interest_rate',
        'duration',
        'tenor_type',
        'purpose',
        'currency_id',
        'collateral_currency_id',
        'collateral_amount',
        'initial_ltv',
        'current_ltv',
        'liquidation_ltv',
        'liquidation_price',
        'description',
        'risk_grade',
        'status',
        'funded_percentage',
        'approved_by',
        'approved_at',
        'funded_at',
        'disbursed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'             => 'decimal:2',
            'interest_rate'      => 'decimal:2',
            'collateral_amount'  => 'decimal:8',
            'initial_ltv'        => 'decimal:2',
            'current_ltv'        => 'decimal:2',
            'liquidation_ltv'    => 'decimal:2',
            'liquidation_price'  => 'decimal:8',
            'funded_percentage'  => 'decimal:2',
            'approved_at'        => 'datetime',
            'funded_at'          => 'datetime',
            'disbursed_at'       => 'datetime',
        ];
    }

    // ─── Status Constants ─────────────────────────────────────────────

    const STATUS_DRAFT        = 'draft';
    const STATUS_PENDING      = 'pending';
    const STATUS_OPEN_FUNDING = 'open_funding';
    const STATUS_FUNDED       = 'funded';
    const STATUS_ACTIVE       = 'active';
    const STATUS_COMPLETED    = 'completed';
    const STATUS_DEFAULT      = 'default';
    const STATUS_CANCELLED    = 'cancelled';

    // ─── Scopes ───────────────────────────────────────────────────────

    public function scopeOpenFunding($query)
    {
        return $query->where('status', self::STATUS_OPEN_FUNDING);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeByGrade($query, string $grade)
    {
        return $query->where('risk_grade', $grade);
    }

    public function scopeCrypto($query)
    {
        return $query->whereHas('currency', fn ($q) => $q->where('type', 'crypto'));
    }

    // ─── Status Checks ────────────────────────────────────────────────

    public function isFullyFunded(): bool
    {
        return bccomp($this->funded_percentage, '100.00', 2) >= 0;
    }

    public function isAtRiskOfLiquidation(): bool
    {
        return $this->collateral_amount > 0
            && bccomp($this->current_ltv, $this->liquidation_ltv, 2) >= 0;
    }

    public function isCryptoLoan(): bool
    {
        return $this->collateral_currency_id !== null;
    }

    // ─── Relationships ─────────────────────────────────────────────────

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LoanCategory::class, 'category_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function collateralCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'collateral_currency_id');
    }

    public function fundings(): HasMany
    {
        return $this->hasMany(LoanFunding::class, 'loan_id');
    }

    public function agreement(): HasOne
    {
        return $this->hasOne(LoanAgreement::class, 'loan_id');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(LoanInstallment::class, 'loan_id')->orderBy('installment_number');
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class, 'loan_id');
    }
}
