<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanMessage extends Model
{
    use HasUuids;

    protected $fillable = [
        'loan_request_id',
        'sender_id',
        'message',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function loanRequest(): BelongsTo
    {
        return $this->belongsTo(LoanRequest::class, 'loan_request_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
