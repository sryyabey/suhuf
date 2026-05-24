<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FetchKutubSittah extends Command
{
    protected $signature = 'app:fetch-kutub-sittah
        {--output=private/kutub_sittah.json : Output path on local disk}
        {--base-url=https://cdn.jsdelivr.net/gh/fawazahmed0/hadith-api@1 : Dataset base URL}
        {--books=bukhari,muslim,abudawud,tirmidhi,nasai,ibnmajah : Book slugs}
        {--languages=en,tr : Languages to fetch (comma separated)}';

    protected $description = 'Fetch Kutub al-Sittah (TR+EN) files and build a unified JSON dataset for importer';

    public function handle(): int
    {
        $baseUrl = rtrim((string) $this->option('base-url'), '/');
        $outputPath = (string) $this->option('output');
        $bookSlugs = collect(explode(',', (string) $this->option('books')))
            ->map(fn (string $v) => trim(Str::lower($v)))
            ->filter()
            ->values();

        $languages = collect(explode(',', (string) $this->option('languages')))
            ->map(fn (string $v) => trim(Str::lower($v)))
            ->filter(fn (string $v) => in_array($v, ['en', 'tr'], true))
            ->values();

        if ($bookSlugs->isEmpty() || $languages->isEmpty()) {
            $this->error('Books or languages cannot be empty.');
            return self::FAILURE;
        }

        $this->line('Fetching editions index...');
        $editionsResp = Http::timeout(40)->get("{$baseUrl}/editions.json");

        if (! $editionsResp->ok()) {
            $this->error('Could not fetch editions index.');
            return self::FAILURE;
        }

        $editions = $editionsResp->json();
        if (! is_array($editions)) {
            $this->error('Invalid editions index JSON.');
            return self::FAILURE;
        }

        $editionEntries = $this->normalizeEditionEntries($editions, $baseUrl);

        $resolved = [];
        foreach ($languages as $lang) {
            foreach ($bookSlugs as $book) {
                $match = $this->findEditionFor($editionEntries, $lang, $book);
                if (! $match) {
                    $this->warn("Edition not found for {$lang}/{$book}");
                    continue;
                }

                $resolved[$lang][$book] = $match;
            }
        }

        $missing = [];
        foreach ($languages as $lang) {
            foreach ($bookSlugs as $book) {
                if (! isset($resolved[$lang][$book])) {
                    $missing[] = "{$lang}:{$book}";
                }
            }
        }

        if ($missing !== []) {
            $this->warn('Missing editions: '.implode(', ', $missing));
        }

        $index = [];
        $stats = ['files' => 0, 'entries' => 0];

        foreach ($resolved as $lang => $books) {
            foreach ($books as $bookSlug => $edition) {
                $url = $edition['url'];
                $this->line("Fetching {$url}");

                $resp = Http::timeout(60)->get($url);
                if (! $resp->ok()) {
                    $this->warn("Failed: {$url}");
                    continue;
                }

                $payload = $resp->json();
                if (! is_array($payload)) {
                    $this->warn("Invalid JSON: {$url}");
                    continue;
                }

                $items = $this->resolveHadithItems($payload);
                foreach ($items as $item) {
                    $bookCode = $this->value($item, ['bookSlug', 'book', 'collection', 'bookName']) ?: $bookSlug;
                    $externalId = (string) ($this->value($item, ['id', 'hadithnumber', 'hadithNumber', 'hadith_no']) ?? '');
                    $chapterNo = $this->toInt($this->value($item, ['chapter', 'chapter_no', 'chapterNumber']));
                    $hadithNo = $this->toInt($this->value($item, ['hadithnumber', 'hadithNumber', 'hadith_no', 'number']));
                    $chapterTitle = $this->value($item, ['chapterTitle', 'chapter_title', 'chapterName']);
                    $text = $this->value($item, ['text', 'hadith', 'body']);

                    if (! is_string($text) || trim($text) === '') {
                        continue;
                    }

                    $key = implode('|', [
                        Str::lower((string) $bookCode),
                        (string) ($hadithNo ?? '0'),
                        (string) ($externalId !== '' ? $externalId : md5($text)),
                    ]);

                    if (! isset($index[$key])) {
                        $index[$key] = [
                            'book_code' => Str::lower((string) $bookCode),
                            'book_name_en' => null,
                            'book_name_tr' => null,
                            'chapter_no' => $chapterNo,
                            'chapter_name_en' => null,
                            'chapter_name_tr' => null,
                            'external_id' => $externalId !== '' ? $externalId : null,
                            'hadith_no' => $hadithNo,
                            'text_en' => null,
                            'text_tr' => null,
                            'grade' => $this->value($item, ['grade']),
                            'arabic_text' => $this->value($item, ['arabic', 'arabic_text']),
                        ];
                    }

                    if ($lang === 'en') {
                        $index[$key]['text_en'] = trim($text);
                        $index[$key]['book_name_en'] = $index[$key]['book_name_en'] ?: (string) ($this->value($payload, ['metadata.name', 'metadata.title']) ?? ucfirst($bookSlug));
                        $index[$key]['chapter_name_en'] = $index[$key]['chapter_name_en'] ?: (is_string($chapterTitle) ? $chapterTitle : null);
                    }

                    if ($lang === 'tr') {
                        $index[$key]['text_tr'] = trim($text);
                        $index[$key]['book_name_tr'] = $index[$key]['book_name_tr'] ?: (string) ($this->value($payload, ['metadata.name', 'metadata.title']) ?? ucfirst($bookSlug));
                        $index[$key]['chapter_name_tr'] = $index[$key]['chapter_name_tr'] ?: (is_string($chapterTitle) ? $chapterTitle : null);
                    }

                    $stats['entries']++;
                }

                $stats['files']++;
            }
        }

        $merged = collect($index)
            ->values()
            ->filter(fn (array $row) => ! empty($row['text_en']) || ! empty($row['text_tr']))
            ->values()
            ->all();

        $output = [
            'source' => 'fawazahmed0/hadith-api',
            'generated_at' => now()->toIso8601String(),
            'languages' => $languages->all(),
            'books' => $bookSlugs->all(),
            'items' => $merged,
        ];

        Storage::disk('local')->put($outputPath, json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $this->info('Kutub al-Sittah unified JSON created.');
        $this->line('Files fetched: '.$stats['files']);
        $this->line('Raw entries processed: '.$stats['entries']);
        $this->line('Unified items: '.count($merged));
        $this->line('Output: storage/app/'.$outputPath);

        return self::SUCCESS;
    }

    private function resolveHadithItems(array $payload): array
    {
        foreach (['hadiths', 'items', 'data', 'result', 'results'] as $key) {
            $value = $payload[$key] ?? null;
            if (is_array($value) && array_is_list($value)) {
                return $value;
            }
        }

        if (array_is_list($payload)) {
            return $payload;
        }

        return [];
    }

    private function value(array $source, array $keys): mixed
    {
        foreach ($keys as $key) {
            $value = Arr::get($source, $key);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function toInt(mixed $value): ?int
    {
        if ($value === null || $value === '' || ! is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }

    private function normalizeEditionEntries(array $editions, string $baseUrl): array
    {
        $rows = [];

        foreach ($editions as $key => $item) {
            if (is_string($item)) {
                $slug = $this->extractSlug($item) ?? (is_string($key) ? $this->extractSlug($key) : null);
                $url = Str::startsWith($item, ['http://', 'https://'])
                    ? $item
                    : "{$baseUrl}/editions/{$item}.json";

                $rows[] = [
                    'slug' => $slug,
                    'url' => $url,
                    'blob' => Str::lower(trim((string) $key.' '.$item)),
                ];

                continue;
            }

            if (! is_array($item)) {
                continue;
            }

            $stringValues = collect($item)
                ->filter(fn ($v) => is_string($v))
                ->map(fn (string $v) => trim($v))
                ->values()
                ->all();

            $blob = Str::lower(implode(' ', array_merge(is_string($key) ? [$key] : [], $stringValues)));
            $name = $item['name'] ?? $item['slug'] ?? $item['id'] ?? null;
            $urlValue = $item['url'] ?? $item['link'] ?? $item['path'] ?? null;
            $slug = $this->extractSlug($name) ?? $this->extractSlug((string) $urlValue) ?? (is_string($key) ? $this->extractSlug($key) : null);

            $url = null;
            if (is_string($urlValue) && $urlValue !== '') {
                $url = Str::startsWith($urlValue, ['http://', 'https://'])
                    ? $urlValue
                    : "{$baseUrl}/editions/".trim($urlValue, '/');
            } elseif ($slug) {
                $url = "{$baseUrl}/editions/{$slug}.json";
            }

            if (! $url || ! $slug) {
                continue;
            }

            $rows[] = [
                'slug' => $slug,
                'url' => Str::endsWith($url, '.json') ? $url : "{$url}.json",
                'blob' => $blob,
            ];
        }

        return collect($rows)
            ->unique(fn (array $row) => $row['slug'].'|'.$row['url'])
            ->values()
            ->all();
    }

    private function findEditionFor(array $entries, string $lang, string $book): ?array
    {
        $langTokens = $lang === 'en'
            ? [' en ', ' eng ', ' english ', '-en', 'eng-', 'english-']
            : [' tr ', ' tur ', ' turkish ', ' türkçe ', '-tr', 'tur-', 'turkish-'];

        $bookTokens = match ($book) {
            'abudawud' => ['abudawud', 'abu dawud', 'abu-dawud', 'abudawood'],
            'tirmidhi' => ['tirmidhi', 'trimidhi'],
            'ibnmajah' => ['ibnmajah', 'ibn majah', 'ibn-majah'],
            default => [$book],
        };

        foreach ($entries as $entry) {
            $haystack = ' '.Str::lower($entry['slug'].' '.$entry['blob']).' ';

            $hasBook = collect($bookTokens)->contains(fn (string $token) => Str::contains($haystack, Str::lower($token)));
            $hasLang = collect($langTokens)->contains(fn (string $token) => Str::contains($haystack, Str::lower($token)));

            if ($hasBook && $hasLang) {
                return $entry;
            }
        }

        return null;
    }

    private function extractSlug(?string $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $value = trim($value);
        if (Str::startsWith($value, ['http://', 'https://'])) {
            $path = parse_url($value, PHP_URL_PATH);
            if (! is_string($path) || $path === '') {
                return null;
            }

            $value = basename($path);
        }

        $value = preg_replace('/\.json$/i', '', $value) ?? $value;
        $value = trim($value, '/');

        return $value !== '' ? $value : null;
    }
}
