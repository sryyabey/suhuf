<?php

namespace App\Console\Commands;

use App\Models\HadithBook;
use App\Models\HadithChapter;
use App\Models\HadithEntry;
use App\Models\HadithTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportKutubSittahJson extends Command
{
    protected $signature = 'app:import-kutub-sittah
        {file : JSON file path}
        {--source= : Dataset source key}
        {--lang= : Force translation language (en|tr|ar)}
        {--replace : Delete existing records for the same source before import}';

    protected $description = 'Import Kutub al-Sittah dataset from JSON (supports Turkish and English translations)';

    public function handle(): int
    {
        $file = (string) $this->argument('file');
        $source = (string) ($this->option('source') ?: config('hadith.default_source', 'kutub_al_sittah'));
        $forcedLang = $this->option('lang') ? (string) $this->option('lang') : null;

        if (! is_file($file)) {
            $this->error("File not found: {$file}");
            return self::FAILURE;
        }

        $raw = file_get_contents($file);
        if ($raw === false) {
            $this->error('Could not read file.');
            return self::FAILURE;
        }

        $payload = json_decode($raw, true);
        if (! is_array($payload)) {
            $this->error('Invalid JSON payload.');
            return self::FAILURE;
        }

        $items = $this->resolveItems($payload);
        if ($items === []) {
            $this->error('No hadith records found in dataset.');
            return self::FAILURE;
        }

        if ($this->option('replace')) {
            $this->warn("Replacing existing hadith records for source: {$source}");
            $bookIds = HadithBook::query()->where('source', $source)->pluck('id');
            if ($bookIds->isNotEmpty()) {
                HadithTranslation::query()->whereIn('hadith_id', HadithEntry::query()->whereIn('book_id', $bookIds)->select('id'))->delete();
                HadithEntry::query()->whereIn('book_id', $bookIds)->delete();
                HadithChapter::query()->whereIn('book_id', $bookIds)->delete();
                HadithBook::query()->whereIn('id', $bookIds)->delete();
            }
        }

        $imported = [
            'books' => 0,
            'chapters' => 0,
            'hadiths' => 0,
            'translations' => 0,
        ];

        DB::beginTransaction();

        try {
            foreach ($items as $i => $item) {
                $bookCode = $this->pick($item, ['book_code', 'book.slug', 'book.code', 'collection', 'book']) ?: 'unknown';
                $bookNames = [
                    'name_ar' => $this->pick($item, ['book_name_ar', 'book.name_ar', 'book.arabic', 'book.ar']),
                    'name_en' => $this->pick($item, ['book_name_en', 'book.name_en', 'book.english', 'book.en']),
                    'name_tr' => $this->pick($item, ['book_name_tr', 'book.name_tr', 'book.turkish', 'book.tr']),
                ];

                $book = HadithBook::query()->firstOrCreate(
                    ['source' => $source, 'code' => (string) $bookCode],
                    array_filter([
                        ...$bookNames,
                        'sort_order' => $i + 1,
                    ], fn ($v) => $v !== null)
                );

                if ($book->wasRecentlyCreated) {
                    $imported['books']++;
                }

                $chapterCode = $this->pick($item, ['chapter_code', 'chapter.slug', 'chapter.code'])
                    ?: (string) ($this->pick($item, ['chapter_no', 'chapter.number']) ?? '0');

                $chapter = null;
                if ($chapterCode !== '0' || $this->pick($item, ['chapter_name_en', 'chapter.name_en', 'chapter.name'])) {
                    $chapter = HadithChapter::query()->firstOrCreate(
                        ['book_id' => $book->id, 'code' => (string) $chapterCode],
                        array_filter([
                            'chapter_no' => $this->toInt($this->pick($item, ['chapter_no', 'chapter.number'])),
                            'name_ar' => $this->pick($item, ['chapter_name_ar', 'chapter.name_ar', 'chapter.ar']),
                            'name_en' => $this->pick($item, ['chapter_name_en', 'chapter.name_en', 'chapter.en', 'chapter.name']),
                            'name_tr' => $this->pick($item, ['chapter_name_tr', 'chapter.name_tr', 'chapter.tr']),
                            'sort_order' => $this->toInt($this->pick($item, ['chapter_no', 'chapter.number'])) ?? ($i + 1),
                        ], fn ($v) => $v !== null)
                    );

                    if ($chapter->wasRecentlyCreated) {
                        $imported['chapters']++;
                    }
                }

                $externalId = (string) ($this->pick($item, ['external_id', 'hadith_id', 'id']) ?? Str::uuid()->toString());
                $hadith = HadithEntry::query()->firstOrCreate(
                    ['book_id' => $book->id, 'external_id' => $externalId],
                    array_filter([
                        'chapter_id' => $chapter?->id,
                        'hadith_no' => $this->toInt($this->pick($item, ['hadith_no', 'hadith.number', 'number'])),
                        'grade' => $this->pick($item, ['grade', 'hadith.grade']),
                        'arabic_text' => $this->pick($item, ['arabic_text', 'hadith.arabic', 'hadith.ar']),
                        'meta' => Arr::only($item, ['reference', 'volume', 'section']),
                    ], fn ($v) => $v !== null && $v !== [])
                );

                if ($hadith->wasRecentlyCreated) {
                    $imported['hadiths']++;
                }

                $translations = $this->resolveTranslations($item, $forcedLang);
                foreach ($translations as $translation) {
                    HadithTranslation::query()->updateOrCreate(
                        [
                            'hadith_id' => $hadith->id,
                            'language' => $translation['language'],
                        ],
                        [
                            'title' => $translation['title'] ?? null,
                            'text' => $translation['text'],
                            'narrator' => $translation['narrator'] ?? null,
                        ]
                    );
                    $imported['translations']++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Import failed: '.$e->getMessage());
            return self::FAILURE;
        }

        $this->info('Kutub-i Sitte import completed.');
        $this->line('Books: '.$imported['books']);
        $this->line('Chapters: '.$imported['chapters']);
        $this->line('Hadiths: '.$imported['hadiths']);
        $this->line('Translations (upsert): '.$imported['translations']);

        return self::SUCCESS;
    }

    private function resolveItems(array $payload): array
    {
        if (array_is_list($payload)) {
            return $payload;
        }

        foreach (['items', 'data', 'hadiths', 'results'] as $key) {
            $value = $payload[$key] ?? null;
            if (is_array($value) && array_is_list($value)) {
                return $value;
            }
        }

        return [];
    }

    private function resolveTranslations(array $item, ?string $forcedLang): array
    {
        $translations = [];

        $flatCandidates = [
            'en' => [
                'title' => $this->pick($item, ['title_en', 'en_title']),
                'text' => $this->pick($item, ['text_en', 'hadith_en', 'english', 'text.english']),
                'narrator' => $this->pick($item, ['narrator_en', 'rawi_en']),
            ],
            'tr' => [
                'title' => $this->pick($item, ['title_tr', 'tr_title']),
                'text' => $this->pick($item, ['text_tr', 'hadith_tr', 'turkish', 'text.turkish']),
                'narrator' => $this->pick($item, ['narrator_tr', 'rawi_tr']),
            ],
            'ar' => [
                'title' => $this->pick($item, ['title_ar']),
                'text' => $this->pick($item, ['text_ar', 'hadith_ar', 'arabic', 'text.arabic']),
                'narrator' => $this->pick($item, ['narrator_ar', 'rawi_ar']),
            ],
        ];

        foreach ($flatCandidates as $language => $candidate) {
            if (($forcedLang && $language !== $forcedLang) || empty($candidate['text'])) {
                continue;
            }

            $translations[] = [
                'language' => $language,
                'title' => $candidate['title'],
                'text' => (string) $candidate['text'],
                'narrator' => $candidate['narrator'],
            ];
        }

        $nested = $item['translations'] ?? null;
        if (is_array($nested)) {
            foreach ($nested as $lang => $content) {
                if (! is_string($lang) || ($forcedLang && $lang !== $forcedLang)) {
                    continue;
                }

                if (is_string($content) && $content !== '') {
                    $translations[] = [
                        'language' => $lang,
                        'title' => null,
                        'text' => $content,
                        'narrator' => null,
                    ];

                    continue;
                }

                if (is_array($content) && ! empty($content['text'])) {
                    $translations[] = [
                        'language' => $lang,
                        'title' => $content['title'] ?? null,
                        'text' => (string) $content['text'],
                        'narrator' => $content['narrator'] ?? null,
                    ];
                }
            }
        }

        return collect($translations)
            ->keyBy(fn (array $row) => $row['language'])
            ->values()
            ->all();
    }

    private function pick(array $item, array $keys): mixed
    {
        foreach ($keys as $key) {
            $value = data_get($item, $key);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function toInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }
}
