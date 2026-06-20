<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;
use RuntimeException;
use ZipArchive;

class BuildQuranDatabase extends Command
{
    protected array $integerColumns = [
        'id',
        'aya',
        'sura',
        'position',
        'juz',
        'hezb',
        'rub',
        'page',
        'line',
    ];

    protected $signature = 'quran:db:build
        {--output= : Output zip path relative to project root}
        {--sql= : SQL dump path relative to project root}';

    protected $description = 'Build the downloadable quran.db.zip file from quran_words or quran.sql';

    public function handle(): int
    {
        $zipPath = $this->resolveZipPath();
        $directory = dirname($zipPath);
        $databasePath = $directory.'/quran.db';

        if (! is_dir($directory) && ! mkdir($directory, 0777, true) && ! is_dir($directory)) {
            $this->error('Could not create output directory.');

            return self::FAILURE;
        }

        @unlink($databasePath);
        @unlink($zipPath);

        $sqlite = new PDO('sqlite:'.$databasePath);
        $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            $this->createSchema($sqlite);
            $sqlPath = $this->resolveSqlPath();

            if ($sqlPath !== null) {
                $this->createImportSchema($sqlite);
                $this->importSqlDump($sqlite, $sqlPath);
                $this->copyImportedRows($sqlite);
            } else {
                $this->copyRows($sqlite);
            }
            $sqlite = null;
            $this->createZip($databasePath, $zipPath);
        } catch (\Throwable $exception) {
            @unlink($databasePath);
            @unlink($zipPath);

            report($exception);
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $dbSize = is_file($databasePath) ? round(filesize($databasePath) / 1024 / 1024, 2) : 0;
        $zipSize = is_file($zipPath) ? round(filesize($zipPath) / 1024 / 1024, 2) : 0;
        @unlink($databasePath);

        $this->info("Quran database ready: {$zipPath}");
        $this->line("SQLite size: {$dbSize} MB");
        $this->line("Zip size: {$zipSize} MB");

        return self::SUCCESS;
    }

    protected function resolveZipPath(): string
    {
        $output = $this->option('output');

        if (blank($output)) {
            return storage_path('data/quran.db.zip');
        }

        return str_starts_with($output, '/')
            ? $output
            : base_path($output);
    }

    protected function resolveSqlPath(): ?string
    {
        $sql = $this->option('sql');

        if (filled($sql)) {
            return str_starts_with($sql, '/')
                ? $sql
                : base_path($sql);
        }

        $defaultPath = storage_path('data/quran.sql');

        return is_file($defaultPath) ? $defaultPath : null;
    }

    protected function createSchema(PDO $sqlite): void
    {
        $schema = <<<'SQL'
CREATE TABLE quran_words (
    id INTEGER PRIMARY KEY,
    aya INTEGER NOT NULL,
    sura INTEGER NOT NULL,
    position INTEGER NOT NULL,
    verse_key TEXT NOT NULL DEFAULT '',
    text TEXT NOT NULL DEFAULT '',
    simple TEXT NOT NULL DEFAULT '',
    juz INTEGER NOT NULL DEFAULT 0,
    hezb INTEGER NOT NULL DEFAULT 0,
    rub INTEGER NOT NULL DEFAULT 0,
    page INTEGER NOT NULL,
    class_name TEXT NOT NULL DEFAULT '',
    line INTEGER NOT NULL,
    code TEXT NOT NULL DEFAULT '',
    code_v3 TEXT NOT NULL DEFAULT '',
    char_type TEXT NOT NULL DEFAULT '',
    audio TEXT NOT NULL DEFAULT '',
    translation TEXT NOT NULL DEFAULT '',
    translation_tr TEXT
);
CREATE INDEX idx_sura_aya ON quran_words(sura, aya);
CREATE INDEX idx_page_line ON quran_words(page, line);
CREATE INDEX idx_juz ON quran_words(juz);
CREATE INDEX idx_verse_key ON quran_words(verse_key);
SQL;

        $sqlite->exec($schema);
    }

    protected function copyRows(PDO $sqlite): void
    {
        $columns = [
            'id',
            'aya',
            'sura',
            'position',
            'verse_key',
            'text',
            'simple',
            'juz',
            'hezb',
            'rub',
            'page',
            'class_name',
            'line',
            'code',
            'code_v3',
            'char_type',
            'audio',
            'translation',
            'translation_tr',
        ];
        $defaults = [
            'verse_key' => '',
            'text' => '',
            'simple' => '',
            'juz' => 0,
            'hezb' => 0,
            'rub' => 0,
            'class_name' => '',
            'code' => '',
            'code_v3' => '',
            'char_type' => '',
            'audio' => '',
            'translation' => '',
        ];

        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $insert = $sqlite->prepare(sprintf(
            'INSERT INTO quran_words (%s) VALUES (%s)',
            implode(', ', $columns),
            $placeholders,
        ));

        if (! $insert) {
            throw new RuntimeException('Could not prepare quran.db insert statement.');
        }

        $sqlite->beginTransaction();

        try {
            DB::table('quran_words')
                ->select($columns)
                ->orderBy('id')
                ->chunk(500, function ($rows) use ($columns, $defaults, $insert): void {
                    foreach ($rows as $row) {
                        $payload = [];

                        foreach ($columns as $index => $column) {
                            $payload[$index] = $this->normalizeValue(
                                $column,
                                $row->{$column} ?? null,
                                $defaults[$column] ?? null,
                            );
                        }

                        $insert->execute($payload);
                    }
                });

            $sqlite->commit();
        } catch (\Throwable $exception) {
            $sqlite->rollBack();

            throw $exception;
        }
    }

    protected function createImportSchema(PDO $sqlite): void
    {
        $sqlite->exec(<<<'SQL'
CREATE TABLE quran_words_import (
    id INTEGER,
    aya INTEGER,
    sura INTEGER,
    position INTEGER,
    verse_key TEXT,
    text TEXT,
    simple TEXT,
    page INTEGER,
    class_name TEXT,
    line INTEGER,
    char_type TEXT,
    translation TEXT,
    translation_tr TEXT
);
SQL);
    }

    protected function importSqlDump(PDO $sqlite, string $sqlPath): void
    {
        if (! is_file($sqlPath)) {
            throw new RuntimeException("SQL dump not found: {$sqlPath}");
        }

        $sql = file_get_contents($sqlPath);

        if ($sql === false) {
            throw new RuntimeException("Could not read SQL dump: {$sqlPath}");
        }

        $sql = preg_replace('/INSERT\s+INTO\s+`?kuran`?\.`?quran_words`?/i', 'INSERT INTO quran_words_import', $sql);
        $sql = preg_replace('/INSERT\s+INTO\s+quran\.quran_words/i', 'INSERT INTO quran_words_import', $sql);

        if ($sql === null) {
            throw new RuntimeException('Could not normalize SQL dump for SQLite import.');
        }

        $sqlite->beginTransaction();

        try {
            $sqlite->exec($sql);
            $sqlite->commit();
        } catch (\Throwable $exception) {
            $sqlite->rollBack();

            throw $exception;
        }
    }

    protected function copyImportedRows(PDO $sqlite): void
    {
        $sqlite->exec(<<<'SQL'
INSERT INTO quran_words (
    id, aya, sura, position, verse_key, text, simple, juz, hezb, rub, page,
    class_name, line, code, code_v3, char_type, audio, translation, translation_tr
)
SELECT
    id,
    aya,
    sura,
    position,
    COALESCE(verse_key, ''),
    COALESCE(text, ''),
    COALESCE(simple, ''),
    0,
    0,
    0,
    page,
    COALESCE(class_name, ''),
    line,
    '',
    '',
    COALESCE(char_type, ''),
    '',
    COALESCE(translation, ''),
    translation_tr
FROM quran_words_import
ORDER BY id;
SQL);
    }

    protected function createZip(string $databasePath, string $zipPath): void
    {
        $zip = new ZipArchive();
        $opened = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($opened !== true) {
            throw new RuntimeException('Could not create quran.db.zip archive.');
        }

        $entryName = basename($databasePath);
        $zip->addFile($databasePath, $entryName);

        if (method_exists($zip, 'setCompressionName')) {
            $zip->setCompressionName($entryName, ZipArchive::CM_DEFLATE, 9);
        }

        $zip->close();
    }

    protected function normalizeValue(string $column, mixed $value, mixed $default): mixed
    {
        if ($value === null) {
            return $default;
        }

        if (in_array($column, $this->integerColumns, true)) {
            return (int) $value;
        }

        return $value;
    }
}
