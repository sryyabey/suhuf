<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserInvite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserDashboardInviteTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_dashboard_invite(): void
    {
        Role::findOrCreate('user', 'web');

        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)
            ->post(route('user.invites.store'));

        $response->assertRedirect(route('user.dashboard'));
        $response->assertSessionHas('invite_code_created');

        $this->assertDatabaseCount('user_invites', 1);
        $this->assertDatabaseHas('user_invites', [
            'user_id' => $user->id,
            'max_uses' => 1,
            'is_active' => 1,
        ]);
    }

    public function test_creating_new_dashboard_invite_deactivates_previous_unused_invite(): void
    {
        Role::findOrCreate('user', 'web');

        $user = User::factory()->create();
        $user->assignRole('user');

        $oldInvite = UserInvite::query()->create([
            'user_id' => $user->id,
            'code' => 'OLDINVITE01',
            'max_uses' => 1,
            'used_count' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->post(route('user.invites.store'))
            ->assertRedirect(route('user.dashboard'));

        $oldInvite->refresh();

        $this->assertFalse($oldInvite->is_active);
        $this->assertDatabaseCount('user_invites', 2);
        $this->assertSame(1, UserInvite::query()->where('user_id', $user->id)->where('is_active', true)->count());
    }
}
