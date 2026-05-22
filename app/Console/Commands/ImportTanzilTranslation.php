<?php

namespace App\Console\Commands;

use App\Models\VerseTranslation;
use Illuminate\Console\Command;

class ImportTanzilTranslation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-tanzil-translation
        {file : Tanzil translation txt file path}
        {meal_key : Meal key, example: diyanet}
        {--language=tr : Language code}
        {--format=pipe : pipe|sql}
        {--chunk=500 : Upsert chunk size}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Tanzil translation file (sura|aya|text) into verse_translations table';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $file = (string) $this->argument('file');
        $mealKey = (string) $this->argument('meal_key');
        $language = (string) $this->option('language');
        $format = (string) $this->option('format');
        $chunkSize = max(100, (int) $this->option('chunk'));

        if (! in_array($format, ['pipe', 'sql'], true)) {
            $this->error('Invalid format. Use --format=pipe or --format=sql');

            return self::FAILURE;
        }

        if (! is_file($file)) {
            $this->error("File not found: {$file}");

            return self::FAILURE;
        }

        $handle = fopen($file, 'r');

        if (! $handle) {
            $this->error('Could not open the file.');

            return self::FAILURE;
        }

        $rows = [];
        $lineNumber = 0;
        $imported = 0;

        while (($line = fgets($handle)) !== false) {
            $lineNumber++;
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $parts = $format === 'sql'
                ? $this->parseSqlValuesLine($line)
                : explode('|', $line, 3);

            if (count($parts) !== 3) {
                $this->warn("Skipped malformed line {$lineNumber}");
                continue;
            }

            [$sura, $aya, $text] = $parts;

            if (! ctype_digit($sura) || ! ctype_digit($aya)) {
                $this->warn("Skipped invalid sura/aya at line {$lineNumber}");
                continue;
            }

            $rows[] = [
                'sura' => (int) $sura,
                'aya' => (int) $aya,
                'meal_key' => $mealKey,
                'language' => $language,
                'text' => trim($text),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($rows) >= $chunkSize) {
                $imported += $this->flushRows($rows);
                $rows = [];
            }
        }

        fclose($handle);

        if ($rows !== []) {
            $imported += $this->flushRows($rows);
        }

        $this->info("Import completed. Upserted rows: {$imported}");

        return self::SUCCESS;
    }

    private function parseSqlValuesLine(string $line): array
    {
        // Expected: (1, 1, 1, 'text...'),
        if (! str_starts_with($line, '(')) {
            return [];
        }

        if (! preg_match("/^\\(\\s*\\d+\\s*,\\s*(\\d+)\\s*,\\s*(\\d+)\\s*,\\s*'(.*)'\\s*\\),?$/u", $line, $matches)) {
            return [];
        }

        $sura = $matches[1];
        $aya = $matches[2];
        $text = str_replace(["\\'", "\\\\"], ["'", "\\"], $matches[3]);

        return [$sura, $aya, $text];
    }

    private function flushRows(array $rows): int
    {
        VerseTranslation::query()->upsert(
            $rows,
            ['sura', 'aya', 'meal_key', 'language'],
            ['text', 'updated_at']
        );

        return count($rows);
    }
}
