<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserInvite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InviteOnlyRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_registration_requires_invite_code(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'Yeni Kullanici',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'device_name' => 'ios',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['invite_code']);
    }

    public function test_api_registration_creates_user_from_valid_invite(): void
    {
        $referrer = User::factory()->create();
        $invite = UserInvite::query()->create([
            'user_id' => $referrer->id,
            'code' => 'SUHUF-TEST',
            'max_uses' => 1,
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Yeni Kullanici',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'invite_code' => 'suhuf-test',
            'device_name' => 'ios',
        ]);

        $response->assertCreated()
            ->assertJsonPath('user.email', 'new@example.com')
            ->assertJsonStructure(['token', 'token_type', 'user']);

        $user = User::query()->where('email', 'new@example.com')->firstOrFail();

        $this->assertSame($referrer->id, $user->referred_by_user_id);
        $this->assertSame($invite->id, $user->used_invite_id);
        $this->assertDatabaseHas('user_invites', [
            'id' => $invite->id,
            'used_count' => 1,
        ]);
    }

    public function test_web_registration_rejects_used_invite(): void
    {
        $referrer = User::factory()->create();
        UserInvite::query()->create([
            'user_id' => $referrer->id,
            'code' => 'EXPIRED1',
            'max_uses' => 1,
            'used_count' => 1,
        ]);

        $response = $this->from('/register')
            ->post('/register', [
                'name' => 'Web Kullanici',
                'email' => 'web@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'invite_code' => 'EXPIRED1',
            ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['invite_code']);
        $this->assertDatabaseMissing('users', [
            'email' => 'web@example.com',
        ]);
    }

    public function test_register_page_accepts_invite_locale_from_shared_link(): void
    {
        $response = $this->get('/register?invite=ABC123&locale=en');

        $response->assertOk();
        $response->assertSessionHas('locale', 'en');
        $response->assertSee('ABC123');
    }
}
