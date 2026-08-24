<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, SoftDeletes;

    protected $fillable = [
        'email',
        'google_id',
        'avatar',
        'password',
        'google2fa_secret',
        'google2fa_enabled',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'google2fa_enabled'  => 'boolean',
            'is_active'          => 'boolean',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function apiTokens(): HasMany
    {
        return $this->hasMany(PersonalAccessToken::class);
    }

    public function kyc(): HasOne
    {
        return $this->hasOne(KYC::class);
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    public function loanRequests(): HasMany
    {
        return $this->hasMany(LoanRequest::class, 'borrower_id');
    }

    public function fundings(): HasMany
    {
        return $this->hasMany(LoanFunding::class, 'lender_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isCustomerService(): bool
    {
        return $this->hasRole('customer_service');
    }

    public function isCollectionOfficer(): bool
    {
        return $this->hasRole('collection_officer');
    }

    public function isInternalStaff(): bool
    {
        return $this->hasAnyRole(['admin', 'customer_service', 'collection_officer']);
    }

    public function isStaff(): bool
    {
        return $this->isInternalStaff();
    }

    public function isLender(): bool
    {
        return $this->hasRole('lender');
    }

    public function isBorrower(): bool
    {
        return $this->hasRole('borrower');
    }

    public function walletFor(int $currencyId): ?Wallet
    {
        return $this->wallets()->where('currency_id', $currencyId)->first();
    }

    /**
     * Send custom LendFlow branded password reset email directly via Mailable.
     * We bypass the Notification mail channel to prevent Laravel wrapping our HTML.
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $this->email,
        ], false));

        $expireMinutes = config(
            'auth.passwords.' . config('auth.defaults.passwords') . '.expire',
            60
        );

        \Illuminate\Support\Facades\Mail::to($this->email)
            ->send(new \App\Mail\ResetPasswordMail(
                user: $this,
                url: $url,
                expireMinutes: $expireMinutes
            ));
    }
}
