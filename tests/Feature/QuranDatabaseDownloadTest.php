<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuranDatabaseDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_download_quran_database(): void
    {
        $this->getJson('/api/quran/db/download')
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_download_quran_database(): void
    {
        $path = storage_path('data/quran.db.zip');
        $createdByTest = false;

        if (! is_file($path)) {
            if (! is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            file_put_contents($path, 'test-content');
            $createdByTest = true;
        }

        $user = User::factory()->create();
        $token = $user->createToken('test-device')->plainTextToken;

        $response = $this->withToken($token)
            ->get('/api/quran/db/download');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/zip');

        if ($createdByTest) {
            @unlink($path);
        }
    }
}
