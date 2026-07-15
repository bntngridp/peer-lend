<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanAgreement extends Model
{
    use HasUuids;

    protected $fillable = [
        'loan_id',
        'agreement_number',
        'file_path',
        'status',
        'borrower_signed_at',
        'signed_at',
    ];

    protected function casts(): array
    {
        return [
            'borrower_signed_at' => 'datetime',
            'signed_at'          => 'datetime',
        ];
    }

    // ─── Status Checks ────────────────────────────────────────────────

    public function isFullySigned(): bool
    {
        return $this->status === 'signed';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // ─── Relationships ─────────────────────────────────────────────────

    public function loan(): BelongsTo
    {
        return $this->belongsTo(LoanRequest::class, 'loan_id');
    }
}
