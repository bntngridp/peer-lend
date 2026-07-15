<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'decimal_places',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'      => 'boolean',
            'decimal_places' => 'integer',
        ];
    }

    // ─── Scopes ──────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFiat($query)
    {
        return $query->where('type', 'fiat');
    }

    public function scopeCrypto($query)
    {
        return $query->where('type', 'crypto');
    }

    // ─── Relationships ───────────────────────────────────────────────

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    public function loanRequests(): HasMany
    {
        return $this->hasMany(LoanRequest::class, 'currency_id');
    }
}
