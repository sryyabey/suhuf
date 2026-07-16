<?php

namespace App\Http\Controllers;

use App\Models\UserInvite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class UserInviteController extends Controller
{
    public function store(): RedirectResponse
    {
        $user = auth()->user();

        abort_unless($user !== null, 403);

        $invite = DB::transaction(function () use ($user): UserInvite {
            UserInvite::query()
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->whereColumn('used_count', '<', 'max_uses')
                ->update([
                    'is_active' => false,
                ]);

            return UserInvite::query()->create([
                'user_id' => $user->id,
                'max_uses' => 1,
                'is_active' => true,
            ]);
        });

        return redirect()
            ->route('user.dashboard')
            ->with('invite_code_created', $invite->code);
    }
}
