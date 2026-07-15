<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanRepayment extends Model
{
    use HasUuids;

    protected $fillable = [
        'loan_id',
        'installment_id',
        'amount_paid',
        'payment_date',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid'  => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    // ─── Relationships ─────────────────────────────────────────────────

    public function loan(): BelongsTo
    {
        return $this->belongsTo(LoanRequest::class, 'loan_id');
    }

    public function installment(): BelongsTo
    {
        return $this->belongsTo(LoanInstallment::class, 'installment_id');
    }
}
