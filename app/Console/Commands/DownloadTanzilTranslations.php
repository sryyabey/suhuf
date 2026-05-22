<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadTanzilTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:download-tanzil-translations
        {--format=sql : sql|txt|xml|all}
        {--path=tanzil/translations : Storage subpath under storage/app}
        {--force : Overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download Tanzil translation files into storage/app';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $format = strtolower((string) $this->option('format'));
        $path = trim((string) $this->option('path'), '/');
        $force = (bool) $this->option('force');

        if (! in_array($format, ['sql', 'txt', 'xml', 'all'], true)) {
            $this->error('Invalid --format value. Allowed: sql, txt, xml, all');

            return self::FAILURE;
        }

        $formats = $format === 'all' ? ['sql', 'txt', 'xml'] : [$format];
        $urls = collect();

        foreach ($formats as $singleFormat) {
            $this->info("Fetching Tanzil translations page for format: {$singleFormat}");
            $listingUrl = "https://tanzil.net/trans/?type={$singleFormat}";
            $response = Http::timeout(60)->get($listingUrl);

            if (! $response->ok()) {
                $this->warn("Failed to fetch {$listingUrl}");
                continue;
            }

            preg_match_all('/href\\s*=\\s*([\\\'"])(.*?)\\1/i', $response->body(), $matches);
            $hrefs = collect($matches[2] ?? []);

            $formatUrls = $hrefs
                ->filter(fn (string $href) => $this->isTranslationHref($href))
                ->map(fn (string $href) => $this->normalizeUrl($href))
                ->filter()
                ->map(fn (string $url) => str_contains($url, '?') ? "{$url}&type={$singleFormat}" : "{$url}?type={$singleFormat}")
                ->unique()
                ->values();

            $urls = $urls->merge($formatUrls);
        }

        $urls = $urls->unique()->values();

        if ($urls->isEmpty()) {
            $this->warn('No valid URLs to download.');

            return self::SUCCESS;
        }

        Storage::makeDirectory($path);

        $downloaded = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($urls as $url) {
            $pathPart = (string) parse_url($url, PHP_URL_PATH);
            $code = basename($pathPart);
            $query = (string) parse_url($url, PHP_URL_QUERY);
            parse_str($query, $queryParams);
            $ext = $queryParams['type'] ?? 'txt';
            $filename = "{$code}.{$ext}";

            if (! $filename) {
                $failed++;
                $this->warn("Skipped invalid url: {$url}");
                continue;
            }

            $destination = "{$path}/{$filename}";
            $absoluteDestination = Storage::disk('local')->path($destination);

            if (! $force && Storage::exists($destination)) {
                $skipped++;
                $this->line("Skipped existing: {$destination}");
                continue;
            }

            $parentDir = dirname($absoluteDestination);
            if (! is_dir($parentDir)) {
                mkdir($parentDir, 0755, true);
            }

            $fileResponse = Http::timeout(120)
                ->withOptions([
                    'sink' => $absoluteDestination,
                ])
                ->get($url);

            if (! $fileResponse->ok()) {
                $failed++;
                $this->warn("Failed: {$url}");
                if (is_file($absoluteDestination)) {
                    @unlink($absoluteDestination);
                }
                continue;
            }

            $downloaded++;
            $this->line("Downloaded: {$destination}");
        }

        $this->newLine();
        $this->info("Done. Downloaded: {$downloaded}, Skipped: {$skipped}, Failed: {$failed}");
        $this->info('Location: storage/app/'.$path);

        return self::SUCCESS;
    }

    private function normalizeUrl(string $href): ?string
    {
        $href = trim($href);

        if ($href === '') {
            return null;
        }

        if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
            return $href;
        }

        if (str_starts_with($href, '/')) {
            return 'https://tanzil.net'.$href;
        }

        return 'https://tanzil.net/'.ltrim($href, '/');
    }

    private function isTranslationHref(string $href): bool
    {
        $value = strtolower(trim($href));

        if ($value === '' || $value === '#' || str_starts_with($value, 'javascript:')) {
            return false;
        }

        // Accept both relative (/trans/tr.vakfi) and absolute URLs (https://tanzil.net/trans/tr.vakfi).
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            $path = (string) parse_url($value, PHP_URL_PATH);
            $host = (string) parse_url($value, PHP_URL_HOST);

            if ($host !== '' && ! str_contains($host, 'tanzil.net')) {
                return false;
            }

            return preg_match('#^/trans/(?!log/)[a-z0-9._-]+$#i', $path) === 1;
        }

        // Keep only translation endpoints like /trans/tr.vakfi, ignore logs/docs/etc.
        return preg_match('#^trans/(?!log/)[a-z0-9._-]+$#i', ltrim($value, '/')) === 1;
    }
}
