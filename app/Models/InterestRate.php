<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterestRate extends Model
{
    protected $fillable = [
        'risk_grade',
        'min_rate',
        'max_rate',
    ];

    protected function casts(): array
    {
        return [
            'min_rate' => 'decimal:2',
            'max_rate' => 'decimal:2',
        ];
    }

    // ─── Scopes ──────────────────────────────────────────────────────

    public function scopeForGrade($query, string $grade)
    {
        return $query->where('risk_grade', strtoupper($grade));
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    public static function rangeForGrade(string $grade): ?self
    {
        return static::forGrade($grade)->first();
    }

    public function isValidRate(float $rate): bool
    {
        return $rate >= $this->min_rate && $rate <= $this->max_rate;
    }
}
