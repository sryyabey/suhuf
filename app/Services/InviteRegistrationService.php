<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserInvite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class InviteRegistrationService
{
    /**
     * @param  array{name:string,email:string,password:string,invite_code:string}  $attributes
     */
    public function register(array $attributes): User
    {
        return DB::transaction(function () use ($attributes): User {
            $invite = UserInvite::query()
                ->where('code', $this->normalizeCode($attributes['invite_code']))
                ->lockForUpdate()
                ->first();

            $this->ensureInviteIsUsable($invite);

            $user = User::query()->create([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'password' => $attributes['password'],
                'referred_by_user_id' => $invite->user_id,
                'used_invite_id' => $invite->id,
            ]);

            $userRole = Role::findOrCreate('user', 'web');
            $user->assignRole($userRole);

            $invite->forceFill([
                'used_count' => $invite->used_count + 1,
                'last_used_at' => now(),
            ])->save();

            return $user;
        });
    }

    public function normalizeCode(?string $code): string
    {
        return Str::upper(trim((string) $code));
    }

    private function ensureInviteIsUsable(?UserInvite $invite): void
    {
        if (! $invite || ! $invite->isAvailable()) {
            throw ValidationException::withMessages([
                'invite_code' => [__('A valid invitation code is required.')],
            ]);
        }
    }
}
