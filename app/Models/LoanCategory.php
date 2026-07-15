<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function loanRequests(): HasMany
    {
        return $this->hasMany(LoanRequest::class, 'category_id');
    }
}
