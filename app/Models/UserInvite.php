<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class UserInvite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'max_uses',
        'used_count',
        'expires_at',
        'last_used_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'last_used_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $invite): void {
            if (blank($invite->code)) {
                $invite->code = self::generateCode();
            }

            $invite->code = Str::upper(trim($invite->code));
        });
    }

    public static function generateCode(): string
    {
        return Str::upper(Str::random(12));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function usedByUsers(): HasMany
    {
        return $this->hasMany(User::class, 'used_invite_id');
    }

    public function isAvailable(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at !== null && $this->expires_at->isPast()) {
            return false;
        }

        return $this->used_count < $this->max_uses;
    }
}
