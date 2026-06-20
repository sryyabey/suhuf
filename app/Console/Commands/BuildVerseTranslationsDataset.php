<?php

namespace App\Console\Commands;

use App\Models\VerseTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use ZipArchive;

class BuildVerseTranslationsDataset extends Command
{
    protected $signature = 'dataset:verse-translations:build
        {--language=* : Only include the given language codes}
        {--meal-key=* : Only include the given meal keys}
        {--directory= : Output directory relative to project root}';

    protected $description = 'Build meal_key grouped zipped verse_translations datasets for mobile download';

    public function handle(): int
    {
        $directory = $this->resolveOutputDirectory();

        if (! is_dir($directory) && ! mkdir($directory, 0777, true) && ! is_dir($directory)) {
            $this->error('Could not create verse translations dataset directory.');

            return self::FAILURE;
        }

        $languages = array_values(array_filter((array) $this->option('language')));
        $mealKeys = array_values(array_filter((array) $this->option('meal-key')));

        $baseQuery = VerseTranslation::query();

        if ($languages !== []) {
            $baseQuery->whereIn('language', $languages);
        }

        if ($mealKeys !== []) {
            $baseQuery->whereIn('meal_key', $mealKeys);
        }

        $groups = $baseQuery
            ->select('meal_key', 'language')
            ->selectRaw('COUNT(*) as row_count')
            ->groupBy('meal_key', 'language')
            ->orderBy('meal_key')
            ->get();

        if ($groups->isEmpty()) {
            $this->warn('No verse translations matched the filters.');

            return self::SUCCESS;
        }

        $manifest = [];
        $totalRows = 0;

        foreach ($groups as $group) {
            $mealKey = (string) $group->meal_key;
            $language = (string) $group->language;
            $slug = $this->slugMealKey($mealKey);
            $tsvFilename = "{$slug}.tsv";
            $zipFilename = "{$slug}.zip";
            $tsvPath = $directory.'/'.$tsvFilename;
            $zipPath = $directory.'/'.$zipFilename;

            @unlink($tsvPath);
            @unlink($zipPath);

            $file = fopen($tsvPath, 'wb');

            if ($file === false) {
                $this->error("Could not create dataset TSV file for {$mealKey}.");

                return self::FAILURE;
            }

            $rowCount = 0;

            VerseTranslation::query()
                ->select(['id', 'sura', 'aya', 'meal_key', 'language', 'text'])
                ->where('meal_key', $mealKey)
                ->where('language', $language)
                ->orderBy('id')
                ->chunkById(2000, function ($rows) use (&$file, &$rowCount): void {
                    foreach ($rows as $row) {
                        $text = preg_replace('/\s+/u', ' ', trim((string) $row->text));
                        $line = implode("\t", [
                            $row->sura,
                            $row->aya,
                            $row->meal_key,
                            $row->language,
                            str_replace(["\t", "\r", "\n"], ' ', $text ?? ''),
                        ]);

                        fwrite($file, $line."\n");
                        $rowCount++;
                    }
                });

            fclose($file);

            $zip = new ZipArchive();
            $opened = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            if ($opened !== true) {
                @unlink($tsvPath);
                $this->error("Could not create zip dataset file for {$mealKey}.");

                return self::FAILURE;
            }

            $zip->addFile($tsvPath, $tsvFilename);

            if (method_exists($zip, 'setCompressionName')) {
                $zip->setCompressionName($tsvFilename, ZipArchive::CM_DEFLATE, 9);
            }

            $zip->close();
            @unlink($tsvPath);

            $zipSize = is_file($zipPath) ? round(filesize($zipPath) / 1024 / 1024, 2) : 0;
            $totalRows += $rowCount;

            $manifest[] = [
                'meal_key' => $mealKey,
                'language' => $language,
                'rows' => $rowCount,
                'file' => $zipFilename,
                'path' => 'storage/data/verse_translations/'.$zipFilename,
                'size_mb' => $zipSize,
            ];

            $this->info("Dataset ready for {$mealKey}: {$zipFilename} ({$rowCount} rows)");
        }

        file_put_contents(
            $directory.'/index.json',
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        $this->info('Meal files: '.count($manifest));
        $this->info("Total rows: {$totalRows}");

        return self::SUCCESS;
    }

    protected function slugMealKey(string $mealKey): string
    {
        $slug = Str::of($mealKey)
            ->lower()
            ->replaceMatches('/[^a-z0-9._-]+/u', '-')
            ->trim('-')
            ->toString();

        return $slug === '' ? 'meal' : $slug;
    }

    protected function resolveOutputDirectory(): string
    {
        $directory = $this->option('directory');

        if (blank($directory)) {
            return storage_path('data/verse_translations');
        }

        return str_starts_with($directory, '/')
            ? $directory
            : base_path($directory);
    }
}
