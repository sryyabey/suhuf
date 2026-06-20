<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

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

    public function test_quran_database_build_includes_translation_tr_column_and_data(): void
    {
        $path = storage_path('data/test-quran.db.zip');
        $sqlPath = storage_path('data/test-quran.sql');

        if (is_file($path)) {
            @unlink($path);
        }

        file_put_contents($sqlPath, <<<'SQL'
INSERT INTO kuran.quran_words (id,aya,sura,`position`,verse_key,text,simple,page,class_name,line,char_type,`translation`,translation_tr) VALUES
    (1,1,1,1,'1:1','بِسْمِ','bismi',1,'test',1,'word','In the name','Rahman ve Rahim olan Allah’ın adıyla');
SQL);

        $this->artisan('quran:db:build', [
            '--output' => 'storage/data/test-quran.db.zip',
            '--sql' => 'storage/data/test-quran.sql',
        ])
            ->assertSuccessful();

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($path) === true);

        $databaseContents = $zip->getFromName('quran.db');
        $zip->close();

        $this->assertNotFalse($databaseContents);

        $databasePath = storage_path('data/test-quran.db');
        file_put_contents($databasePath, $databaseContents);

        $sqlite = new \SQLite3($databasePath, SQLITE3_OPEN_READONLY);
        $columns = [];
        $columnResult = $sqlite->query('PRAGMA table_info(quran_words)');

        while ($column = $columnResult->fetchArray(SQLITE3_ASSOC)) {
            $columns[] = $column['name'];
        }

        $this->assertContains('translation_tr', $columns);

        $row = $sqlite->querySingle('SELECT translation_tr FROM quran_words WHERE id = 1', true);
        $sqlite->close();

        $this->assertSame('Rahman ve Rahim olan Allah’ın adıyla', $row['translation_tr']);

        @unlink($databasePath);
        @unlink($path);
        @unlink($sqlPath);
    }
}
