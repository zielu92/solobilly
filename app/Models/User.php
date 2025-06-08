<?php

namespace App\Models;

use Chiiya\FilamentAccessControl\Contracts\AccessControlUser;
use Chiiya\FilamentAccessControl\Enumerators\RoleName;
use Chiiya\FilamentAccessControl\Notifications\TwoFactorCode;
use Filament\Models\Contracts\FilamentUser as FilamentUserInterface;
use Filament\Models\Contracts\HasName;
use Database\Factories\UserFactory;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements AccessControlUser, FilamentUserInterface, HasName
{
    use HasFactory, HasRoles, Notifiable, SoftDeletes;
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasVerifiedEmail();
    }

    protected $guard_name = 'filament';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'expires_at',
        'two_factor_code',
        'two_factor_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'two_factor_expires_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(RoleName::SUPER_ADMIN);
    }

    /**
     * Provides full name of the current filament user.
     */
    public function getFullNameAttribute(): string
    {
        if (! $this->first_name && ! $this->last_name) {
            return '—';
        }

        $name = $this->first_name ?? '';

        if ($this->last_name) {
            $name .= ' '.$this->last_name;
        }

        return $name;
    }

    public function getFilamentName(): string
    {
        return $this->full_name;
    }

    /**
     * Return a name.
     *
     * Needed for compatibility with filament-logger.
     */
    public function getNameAttribute(): string
    {
        return $this->getFilamentName();
    }

    /**
     * {@inheritDoc}
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && now()->gt($this->expires_at);
    }

    /**
     * {@inheritDoc}
     */
    public function extend(): void
    {
        $this->update([
            'expires_at' => now()->addMonths(6)->endOfDay(),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function hasTwoFactorCode(): bool
    {
        return $this->getTwoFactorCode() !== null;
    }

    /**
     * {@inheritDoc}
     */
    public function getTwoFactorCode(): ?string
    {
        return $this->two_factor_code;
    }

    /**
     * {@inheritDoc}
     */
    public function twoFactorCodeIsExpired(): bool
    {
        return $this->two_factor_expires_at !== null && now()->gt($this->two_factor_expires_at);
    }

    /**
     * {@inheritDoc}
     */
    public function sendTwoFactorCodeNotification(): void
    {
        $this->notify(new TwoFactorCode);
    }
}
