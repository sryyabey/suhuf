<?php

namespace App\Console\Commands;

use App\Models\VerseTranslation;
use Illuminate\Console\Command;

class ImportTanzilDirectory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-tanzil-directory
        {dir=storage/app/private/tanzil/translations : Directory containing Tanzil .sql files}
        {--language=tr : Language code}
        {--chunk=500 : Upsert chunk size}
        {--fresh-meal : Delete existing rows for each meal_key before importing file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all Tanzil SQL files in a directory into verse_translations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dir = (string) $this->argument('dir');
        $language = (string) $this->option('language');
        $chunkSize = max(100, (int) $this->option('chunk'));
        $freshMeal = (bool) $this->option('fresh-meal');

        if (! is_dir($dir)) {
            $this->error("Directory not found: {$dir}");

            return self::FAILURE;
        }

        $files = collect(glob(rtrim($dir, '/').'/*.sql') ?: [])->sort()->values();

        if ($files->isEmpty()) {
            $this->warn('No .sql files found in directory.');

            return self::SUCCESS;
        }

        $this->info('Found '.$files->count().' file(s).');

        $totalImported = 0;
        $totalFailed = 0;

        foreach ($files as $file) {
            $mealKey = pathinfo($file, PATHINFO_FILENAME);

            $this->line("Importing {$mealKey} from {$file}");

            if ($freshMeal) {
                VerseTranslation::query()
                    ->where('meal_key', $mealKey)
                    ->where('language', $language)
                    ->delete();
            }

            $handle = fopen($file, 'r');
            if (! $handle) {
                $totalFailed++;
                $this->warn("Cannot open file: {$file}");
                continue;
            }

            $rows = [];
            $lineNumber = 0;
            $imported = 0;

            while (($line = fgets($handle)) !== false) {
                $lineNumber++;
                $line = trim($line);

                if ($line === '' || ! str_starts_with($line, '(')) {
                    continue;
                }

                $parts = $this->parseSqlValuesLine($line);
                if (count($parts) !== 3) {
                    continue;
                }

                [$sura, $aya, $text] = $parts;

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

            $totalImported += $imported;
            $this->line("Imported rows: {$imported}");
        }

        $this->newLine();
        $this->info("Done. Total imported: {$totalImported}, Failed files: {$totalFailed}");

        return self::SUCCESS;
    }

    private function parseSqlValuesLine(string $line): array
    {
        if (! preg_match("/^\\(\\s*\\d+\\s*,\\s*(\\d+)\\s*,\\s*(\\d+)\\s*,\\s*'(.*)'\\s*\\),?$/u", $line, $matches)) {
            return [];
        }

        $sura = $matches[1];
        $aya = $matches[2];
        $text = str_replace(["\\\\'", "\\\\\\\\"], ["'", "\\\\"], $matches[3]);

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
