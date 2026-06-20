<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VerseTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

class VerseTranslationsDatasetDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_list_or_download_dataset_files(): void
    {
        $this->getJson('/api/datasets/verse-translations/meal-keys')
            ->assertUnauthorized();

        $this->getJson('/api/datasets/verse-translations')
            ->assertUnauthorized();

        $this->getJson('/api/datasets/verse-translations/tr.diyanet/download')
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_dataset_files(): void
    {
        $directory = storage_path('data/verse_translations');

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($directory.'/index.json', json_encode([
            [
                'meal_key' => 'tr.diyanet',
                'language' => 'tr',
                'rows' => 10,
                'file' => 'tr.diyanet.zip',
            ],
            [
                'meal_key' => 'en.sahih',
                'language' => 'en',
                'rows' => 10,
                'file' => 'en.sahih.zip',
            ],
        ]));

        $user = User::factory()->create();
        $token = $user->createToken('test-device')->plainTextToken;
        $user->setting()->create([
            'preferred_language' => 'tr',
            'preferred_arabic_font' => 'amiri',
        ]);
        $user->mealPreferences()->create([
            'language' => 'tr',
            'meal_key' => 'tr.diyanet',
        ]);

        $this->withToken($token)
            ->get('/api/datasets/verse-translations')
            ->assertOk()
            ->assertJsonPath('preferred_language', 'tr')
            ->assertJsonPath('requested_language', 'tr')
            ->assertJsonPath('selected_meal_keys.0', 'tr.diyanet')
            ->assertJsonPath('datasets.0.meal_key', 'tr.diyanet')
            ->assertJsonPath('datasets.0.selected', true)
            ->assertJsonPath('datasets.0.download_url', url('/api/datasets/verse-translations/tr.diyanet/download'))
            ->assertJsonMissingPath('datasets.1.meal_key');

        @unlink($directory.'/index.json');
    }

    public function test_authenticated_user_can_get_meal_keys(): void
    {
        $directory = storage_path('data/verse_translations');

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($directory.'/index.json', json_encode([
            [
                'meal_key' => 'tr.diyanet',
                'language' => 'tr',
                'rows' => 10,
                'file' => 'tr.diyanet.zip',
            ],
            [
                'meal_key' => 'tr.vakfi',
                'language' => 'tr',
                'rows' => 10,
                'file' => 'tr.vakfi.zip',
            ],
            [
                'meal_key' => 'en.sahih',
                'language' => 'en',
                'rows' => 10,
                'file' => 'en.sahih.zip',
            ],
        ]));

        $user = User::factory()->create();
        $token = $user->createToken('test-device')->plainTextToken;
        $user->setting()->create([
            'preferred_language' => 'tr',
            'preferred_arabic_font' => 'amiri',
        ]);
        $user->mealPreferences()->create([
            'language' => 'tr',
            'meal_key' => 'tr.diyanet',
        ]);

        $this->withToken($token)
            ->get('/api/datasets/verse-translations/meal-keys')
            ->assertOk()
            ->assertJsonPath('preferred_language', 'tr')
            ->assertJsonPath('requested_language', 'tr')
            ->assertJsonPath('selected_meal_keys.0', 'tr.diyanet')
            ->assertJsonPath('meal_keys.0', 'tr.diyanet')
            ->assertJsonPath('meal_keys.1', 'tr.vakfi')
            ->assertJsonMissingPath('meal_keys.2');

        @unlink($directory.'/index.json');
    }

    public function test_authenticated_user_can_download_selected_meal_dataset(): void
    {
        $directory = storage_path('data/verse_translations');
        $path = $directory.'/tr.diyanet.zip';

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($path, 'test-content');

        $user = User::factory()->create();
        $token = $user->createToken('test-device')->plainTextToken;

        $response = $this->withToken($token)
            ->get('/api/datasets/verse-translations/tr.diyanet/download');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/zip');

        @unlink($path);
    }

    public function test_build_creates_one_zip_per_meal_key_with_index(): void
    {
        VerseTranslation::insert([
            [
                'sura' => 1,
                'aya' => 1,
                'meal_key' => 'tr.diyanet',
                'language' => 'tr',
                'text' => 'Birinci metin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sura' => 1,
                'aya' => 2,
                'meal_key' => 'tr.diyanet',
                'language' => 'tr',
                'text' => 'İkinci metin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sura' => 1,
                'aya' => 1,
                'meal_key' => 'en.sahih',
                'language' => 'en',
                'text' => 'First text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $directory = storage_path('data/verse_translations');
        @unlink($directory.'/tr.diyanet.zip');
        @unlink($directory.'/en.sahih.zip');
        @unlink($directory.'/index.json');

        $this->artisan('dataset:verse-translations:build')
            ->assertSuccessful();

        $this->assertFileExists($directory.'/tr.diyanet.zip');
        $this->assertFileExists($directory.'/en.sahih.zip');
        $this->assertFileExists($directory.'/index.json');

        $manifest = json_decode((string) file_get_contents($directory.'/index.json'), true);
        $this->assertIsArray($manifest);
        $this->assertSame('en.sahih', $manifest[0]['meal_key']);
        $this->assertSame('tr.diyanet', $manifest[1]['meal_key']);

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($directory.'/tr.diyanet.zip') === true);
        $contents = $zip->getFromName('tr.diyanet.tsv');
        $zip->close();

        $this->assertNotFalse($contents);
        $this->assertStringContainsString("1\t1\ttr.diyanet\ttr\tBirinci metin", $contents);
        $this->assertStringContainsString("1\t2\ttr.diyanet\ttr\tİkinci metin", $contents);
    }
}
