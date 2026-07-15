<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeConfiguration extends Model
{
    protected $fillable = [
        'type',
        'value',
        'value_type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value'     => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }

    // ─── Scopes ──────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    public static function getByType(string $type): ?self
    {
        return static::active()->byType($type)->first();
    }
}
