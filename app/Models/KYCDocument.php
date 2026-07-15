<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KYCDocument extends Model
{
    use HasUuids;

    protected $table = 'kyc_documents';

    protected $fillable = [
        'kyc_id',
        'type',
        'file_path',
        'storage_driver',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function kyc(): BelongsTo
    {
        return $this->belongsTo(KYC::class, 'kyc_id');
    }
}
