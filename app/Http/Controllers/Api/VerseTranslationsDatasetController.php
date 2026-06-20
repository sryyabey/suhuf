<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserMealPreference;
use App\Models\UserSetting;
use App\Models\VerseTranslation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VerseTranslationsDatasetController extends Controller
{
    public function mealKeys(Request $request): JsonResponse
    {
        $user = $request->user();

        $setting = UserSetting::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['preferred_language' => 'tr', 'preferred_arabic_font' => 'amiri']
        );

        $selectedMealKeys = UserMealPreference::query()
            ->where('user_id', $user->id)
            ->orderBy('meal_key')
            ->pluck('meal_key')
            ->values();

        $mealKeys = VerseTranslation::distinct()
            ->orderBy('meal_key')
            ->pluck('meal_key')
            ->values();


        return response()->json([
            'preferred_language' => $setting->preferred_language,
            'selected_meal_keys' => $selectedMealKeys,
            'meal_keys' => $mealKeys,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $setting = UserSetting::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['preferred_language' => 'tr', 'preferred_arabic_font' => 'amiri']
        );
        $selectedMealKeys = UserMealPreference::query()
            ->where('user_id', $user->id)
            ->orderBy('meal_key')
            ->pluck('meal_key')
            ->values();
        $datasets = VerseTranslation::query()
            ->select('meal_key', 'language')
            ->selectRaw('COUNT(*) as rows')
            ->groupBy('meal_key', 'language')
            ->orderBy('meal_key')
            ->get()
            ->map(function (VerseTranslation $item): array {
                $mealKey = (string) $item->meal_key;
                $filename = $this->mealKeyFilename($mealKey);
                $path = storage_path('data/verse_translations/' . $filename);

                return [
                    'meal_key' => $mealKey,
                    'language' => (string) $item->language,
                    'rows' => (int) $item->rows,
                    'file' => $filename,
                    'size_mb' => is_file($path) ? round(filesize($path) / 1024 / 1024, 2) : 0,
                ];
            });

        return response()->json([
            'preferred_language' => $setting->preferred_language,
            'selected_meal_keys' => $selectedMealKeys,
            'datasets' => $this->transformDatasets($datasets, $selectedMealKeys),
        ]);
    }

    public function download(string $mealKey): BinaryFileResponse|JsonResponse
    {
        $filename = $this->mealKeyFilename($mealKey);
        $path = storage_path('data/verse_translations/' . $filename);

        if (!is_file($path)) {
            return response()->json([
                'message' => 'Verse translations dataset file not found.',
            ], 404);
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'application/zip',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    protected function mealKeyFilename(string $mealKey): string
    {
        $slug = Str::of($mealKey)
            ->lower()
            ->replaceMatches('/[^a-z0-9._-]+/u', '-')
            ->trim('-')
            ->toString();

        return ($slug === '' ? 'meal' : $slug) . '.zip';
    }

    protected function transformDatasets(Collection $datasets, Collection $selectedMealKeys): array
    {
        $selected = $selectedMealKeys->flip();

        return $datasets
            ->map(function (array $item) use ($selected): array {
                $mealKey = (string) ($item['meal_key'] ?? '');
                $filename = (string) ($item['file'] ?? $this->mealKeyFilename($mealKey));

                return [
                    'meal_key' => $mealKey,
                    'language' => (string) ($item['language'] ?? ''),
                    'rows' => (int) ($item['rows'] ?? 0),
                    'file' => $filename,
                    'size_mb' => (float) ($item['size_mb'] ?? 0),
                    'selected' => $selected->has($mealKey),
                    'download_url' => url('/api/datasets/verse-translations/' . $mealKey . '/download'),
                ];
            })
            ->sortByDesc(fn(array $item): int => $item['selected'] ? 1 : 0)
            ->values()
            ->all();
    }
}
