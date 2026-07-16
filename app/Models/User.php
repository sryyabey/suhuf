<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'referred_by_user_id',
        'used_invite_id',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'salih') {
            return ! $this->hasRole('user');
        }

        return true;
    }

    public function mealPreferences(): HasMany
    {
        return $this->hasMany(UserMealPreference::class);
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by_user_id');
    }

    public function usedInvite(): BelongsTo
    {
        return $this->belongsTo(UserInvite::class, 'used_invite_id');
    }

    public function invites(): HasMany
    {
        return $this->hasMany(UserInvite::class);
    }

    public function referredUsers(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by_user_id');
    }

    public function setting(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    public function quranPageBookmarks(): HasMany
    {
        return $this->hasMany(UserQuranPageBookmark::class);
    }

    public function noteShareLinks(): HasMany
    {
        return $this->hasMany(NoteShareLink::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function backups(): HasMany
    {
        return $this->hasMany(UserBackup::class);
    }

    public function activeSubscription(): ?UserSubscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest('ends_at')
            ->first();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }

    public function canImpersonate(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function canBeImpersonated(): bool
    {
        return ! $this->hasRole('super_admin');
    }
}
