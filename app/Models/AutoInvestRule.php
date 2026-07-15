<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoInvestRule extends Model
{
    use HasUuids;

    protected $fillable = [
        'lender_id',
        'is_active',
        'min_grade',
        'max_grade',
        'max_allocation_per_loan',
        'max_ltv',
    ];

    protected function casts(): array
    {
        return [
            'is_active'               => 'boolean',
            'max_allocation_per_loan' => 'decimal:2',
            'max_ltv'                 => 'decimal:2',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function lender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lender_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Check if a loan matches this investment rule.
     */
    public function matches(LoanRequest $loan): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Convert grades to numeric weights for comparisons (A = 4, B = 3, C = 2, D = 1)
        $weights = ['A' => 4, 'B' => 3, 'C' => 2, 'D' => 1];
        $loanWeight = $weights[$loan->risk_grade] ?? 0;
        $minWeight  = $weights[$this->min_grade] ?? 1;
        $maxWeight  = $weights[$this->max_grade] ?? 4;

        // Grade matching
        if ($loanWeight < $minWeight || $loanWeight > $maxWeight) {
            return false;
        }

        // LTV matching (if collateral-based)
        if ($loan->collateral_currency_id && $loan->current_ltv > $this->max_ltv) {
            return false;
        }

        return true;
    }
}
