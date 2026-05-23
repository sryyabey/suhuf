<?php

namespace App\Livewire;

use App\Models\UserMealPreference;
use App\Models\UserSetting;
use App\Models\VerseTranslation;
use App\Services\TafsirService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class UserSettingsPage extends Component
{
    public ?string $selectedLanguage = null;

    public array   $selectedMealKeys = [];

    /* ── Tefsir ───────────────────────────────────────────────────── */
    public ?int    $preferredTafsirId   = null;
    public ?string $preferredTafsirName = null;
    public string  $tafsirLangFilter    = '';

    public function mount(): void
    {
        $user = auth()->user();

        $defaultLanguage = VerseTranslation::query()->orderBy('language')->value('language') ?? 'tr';

        $this->selectedLanguage = $user?->setting?->preferred_language ?? $defaultLanguage;

        $this->loadSelectedMeals();

        $this->preferredTafsirId   = $user?->setting?->preferred_tafsir_id;
        $this->preferredTafsirName = $user?->setting?->preferred_tafsir_name;
    }

    public function updatedSelectedLanguage(): void
    {
        $this->loadSelectedMeals();
    }

    public function addMeal(string $mealKey): void
    {
        if (! $this->selectedLanguage) {
            return;
        }

        $exists = VerseTranslation::query()
            ->where('language', $this->selectedLanguage)
            ->where('meal_key', $mealKey)
            ->exists();

        if (! $exists) {
            return;
        }

        if (! in_array($mealKey, $this->selectedMealKeys, true)) {
            $this->selectedMealKeys[] = $mealKey;
            sort($this->selectedMealKeys);
        }
    }

    public function removeMeal(string $mealKey): void
    {
        $this->selectedMealKeys = collect($this->selectedMealKeys)
            ->reject(fn (string $key) => $key === $mealKey)
            ->values()
            ->all();
    }

    public function savePreferences(): void
    {
        $user = auth()->user();

        if (! $user || ! $this->selectedLanguage) {
            return;
        }

        $allowedMealKeys = VerseTranslation::query()
            ->where('language', $this->selectedLanguage)
            ->whereIn('meal_key', $this->selectedMealKeys)
            ->distinct()
            ->pluck('meal_key')
            ->values()
            ->all();

        UserSetting::query()->updateOrCreate(
            ['user_id' => $user->id],
            ['preferred_language' => $this->selectedLanguage]
        );

        UserMealPreference::query()
            ->where('user_id', $user->id)
            ->where('language', $this->selectedLanguage)
            ->delete();

        foreach ($allowedMealKeys as $mealKey) {
            UserMealPreference::query()->create([
                'user_id' => $user->id,
                'language' => $this->selectedLanguage,
                'meal_key' => $mealKey,
            ]);
        }

        $this->selectedMealKeys = $allowedMealKeys;

        $this->dispatch('settings-saved');
    }

    /* ── Tefsir metodları ─────────────────────────────────────────── */

    public function selectTafsir(int $id, string $name): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $this->preferredTafsirId   = $id;
        $this->preferredTafsirName = $name;

        UserSetting::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'preferred_tafsir_id'   => $id,
                'preferred_tafsir_name' => $name,
            ]
        );

        $this->dispatch('settings-saved');
    }

    public function clearTafsir(): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $this->preferredTafsirId   = null;
        $this->preferredTafsirName = null;

        UserSetting::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'preferred_tafsir_id'   => null,
                'preferred_tafsir_name' => null,
            ]
        );

        $this->dispatch('settings-saved');
    }

    #[Computed]
    public function tafsirGrouped(): array
    {
        return app(TafsirService::class)->getGroupedByLanguage();
    }

    /* ── Meal ─────────────────────────────────────────────────────── */

    public function getLanguagesProperty()
    {
        return VerseTranslation::query()
            ->select('language')
            ->distinct()
            ->orderBy('language')
            ->pluck('language');
    }

    public function getMealsForLanguageProperty()
    {
        return VerseTranslation::query()
            ->where('language', $this->selectedLanguage)
            ->select('meal_key')
            ->distinct()
            ->orderBy('meal_key')
            ->pluck('meal_key');
    }

    public function getAvailableMealsProperty()
    {
        return $this->mealsForLanguage
            ->reject(fn (string $mealKey) => in_array($mealKey, $this->selectedMealKeys, true))
            ->values();
    }

    public function render(): View
    {
        return view('livewire.user-settings-page');
    }

    private function loadSelectedMeals(): void
    {
        $user = auth()->user();

        if (! $user || ! $this->selectedLanguage) {
            $this->selectedMealKeys = [];
            return;
        }

        $this->selectedMealKeys = UserMealPreference::query()
            ->where('user_id', $user->id)
            ->where('language', $this->selectedLanguage)
            ->orderBy('meal_key')
            ->pluck('meal_key')
            ->all();
    }
}
