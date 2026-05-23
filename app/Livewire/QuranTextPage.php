<?php

namespace App\Livewire;

use App\Models\QuranWord;
use App\Models\ResearchNote;
use App\Models\ResearchTag;
use App\Models\UserMealPreference;
use App\Models\UserSetting;
use App\Models\VerseTranslation;
use App\Services\TafsirService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class QuranTextPage extends Component
{
    protected static array $SURA_NAMES = [
        1 => 'Fâtiha', 2 => 'Bakara', 3 => 'Âl-i İmrân', 4 => 'Nisâ', 5 => 'Mâide',
        6 => 'Enâm', 7 => 'Arâf', 8 => 'Enfâl', 9 => 'Tevbe', 10 => 'Yûnus',
        11 => 'Hûd', 12 => 'Yûsuf', 13 => 'Raʿd', 14 => 'İbrâhim', 15 => 'Hicr',
        16 => 'Nahl', 17 => 'İsrâ', 18 => 'Kehf', 19 => 'Meryem', 20 => 'Tâhâ',
        21 => 'Enbiyâ', 22 => 'Hac', 23 => 'Mü\'minûn', 24 => 'Nûr', 25 => 'Furkân',
        26 => 'Şuarâ', 27 => 'Neml', 28 => 'Kasas', 29 => 'Ankebût', 30 => 'Rûm',
        31 => 'Lokmân', 32 => 'Secde', 33 => 'Ahzâb', 34 => 'Sebe\'', 35 => 'Fâtır',
        36 => 'Yâsîn', 37 => 'Sâffât', 38 => 'Sâd', 39 => 'Zümer', 40 => 'Mü\'min',
        41 => 'Fussilet', 42 => 'Şûrâ', 43 => 'Zuhruf', 44 => 'Duhân', 45 => 'Câsiye',
        46 => 'Ahkâf', 47 => 'Muhammed', 48 => 'Feth', 49 => 'Hucurât', 50 => 'Kâf',
        51 => 'Zâriyât', 52 => 'Tûr', 53 => 'Necm', 54 => 'Kamer', 55 => 'Rahmân',
        56 => 'Vâkıa', 57 => 'Hadîd', 58 => 'Mücâdele', 59 => 'Haşr', 60 => 'Mümtehine',
        61 => 'Saf', 62 => 'Cum\'a', 63 => 'Münâfikûn', 64 => 'Teğâbün', 65 => 'Talâk',
        66 => 'Tahrîm', 67 => 'Mülk', 68 => 'Kalem', 69 => 'Hâkka', 70 => 'Meâric',
        71 => 'Nûh', 72 => 'Cin', 73 => 'Müzzemmil', 74 => 'Müddessir', 75 => 'Kıyâme',
        76 => 'İnsân', 77 => 'Mürselât', 78 => 'Nebe\'', 79 => 'Nâziât', 80 => 'Abese',
        81 => 'Tekvîr', 82 => 'İnfitâr', 83 => 'Mutaffifîn', 84 => 'İnşikâk', 85 => 'Bürûc',
        86 => 'Târık', 87 => 'A\'lâ', 88 => 'Ğâşiye', 89 => 'Fecr', 90 => 'Beled',
        91 => 'Şems', 92 => 'Leyl', 93 => 'Duhâ', 94 => 'İnşirâh', 95 => 'Tîn',
        96 => 'Alak', 97 => 'Kadr', 98 => 'Beyyine', 99 => 'Zilzâl', 100 => 'Âdiyât',
        101 => 'Kâria', 102 => 'Tekâsür', 103 => 'Asr', 104 => 'Hümeze', 105 => 'Fîl',
        106 => 'Kureyş', 107 => 'Mâûn', 108 => 'Kevser', 109 => 'Kâfirûn', 110 => 'Nasr',
        111 => 'Tebbet', 112 => 'İhlâs', 113 => 'Felak', 114 => 'Nâs',
    ];

    public ?int $selectedSura = null;

    public ?int $selectedAya = null;

    public ?string $selectedLanguage = null;

    public ?string $selectedMeal = null;

    public array $preferredMealKeys = [];

    public string $noteType = 'note';

    public ?int $noteWordPosition = null;

    public string $noteTitle = '';

    public string $noteContent = '';

    public array $selectedTagIds = [];

    public string $newTagName = '';

    public ?int $editingNoteId = null;

    public ?int $filterTagId = null;

    public bool $resumed = false;

    /* ── Tefsir ───────────────────────────────────────────────────── */
    public ?int    $preferredTafsirId   = null;
    public ?string $preferredTafsirName = null;
    public bool    $tafsirOpen          = false;
    public ?string $tafsirText          = null;

    public function mount(): void
    {
        $user    = auth()->user();
        $setting = $user?->setting;

        // URL params take priority (e.g. from global search results)
        $suraParam = request()->query('sura');
        $ayaParam  = request()->query('aya');

        if (
            $suraParam && $ayaParam &&
            QuranWord::query()->where('sura', (int) $suraParam)->where('aya', (int) $ayaParam)->exists()
        ) {
            $this->selectedSura = (int) $suraParam;
            $this->selectedAya  = (int) $ayaParam;
            $this->resumed      = false;
        } elseif ($suraParam && QuranWord::query()->where('sura', (int) $suraParam)->exists()) {
            $this->selectedSura = (int) $suraParam;
            $this->syncAya();
            $this->resumed = false;
        } else {
            $lastSura = $setting?->last_read_sura;
            $lastAya  = $setting?->last_read_aya;

            if (
                $lastSura && $lastAya &&
                QuranWord::query()->where('sura', $lastSura)->where('aya', $lastAya)->exists()
            ) {
                $this->selectedSura = $lastSura;
                $this->selectedAya  = $lastAya;
                $this->resumed      = true;
            } else {
                $this->selectedSura = QuranWord::query()->orderBy('sura')->value('sura');
                $this->syncAya();
            }
        }

        $defaultLanguage = VerseTranslation::query()
            ->orderBy('language')
            ->value('language') ?? 'tr';

        $this->selectedLanguage = $setting?->preferred_language ?? $defaultLanguage;

        $this->loadPreferredMealsForLanguage();
        $this->syncSelectedMeal();

        $this->preferredTafsirId   = $setting?->preferred_tafsir_id;
        $this->preferredTafsirName = $setting?->preferred_tafsir_name;
    }

    public function prevAya(): void
    {
        if (! $this->selectedSura || ! $this->selectedAya) {
            return;
        }

        $prev = QuranWord::query()
            ->where('sura', $this->selectedSura)
            ->where('aya', '<', $this->selectedAya)
            ->orderByDesc('aya')
            ->value('aya');

        if ($prev !== null) {
            $this->selectedAya = (int) $prev;
            $this->resetNoteForm();
            $this->saveReadingPosition();
            return;
        }

        $prevSura = QuranWord::query()
            ->where('sura', '<', $this->selectedSura)
            ->orderByDesc('sura')
            ->value('sura');

        if ($prevSura !== null) {
            $this->selectedSura = (int) $prevSura;
            $lastAya = QuranWord::query()
                ->where('sura', $this->selectedSura)
                ->orderByDesc('aya')
                ->value('aya');
            $this->selectedAya = $lastAya ? (int) $lastAya : null;
            $this->resetNoteForm();
            $this->saveReadingPosition();
        }
    }

    public function nextAya(): void
    {
        if (! $this->selectedSura || ! $this->selectedAya) {
            return;
        }

        $next = QuranWord::query()
            ->where('sura', $this->selectedSura)
            ->where('aya', '>', $this->selectedAya)
            ->orderBy('aya')
            ->value('aya');

        if ($next !== null) {
            $this->selectedAya = (int) $next;
            $this->resetNoteForm();
            $this->saveReadingPosition();
            return;
        }

        $nextSura = QuranWord::query()
            ->where('sura', '>', $this->selectedSura)
            ->orderBy('sura')
            ->value('sura');

        if ($nextSura !== null) {
            $this->selectedSura = (int) $nextSura;
            $this->syncAya();
            $this->resetNoteForm();
            $this->saveReadingPosition();
        }
    }

    public function updatedSelectedSura(): void
    {
        $this->syncAya();
        $this->resetNoteForm();
        $this->saveReadingPosition();
        $this->resetTafsir();
    }

    public function updatedSelectedAya(): void
    {
        $this->resetNoteForm();
        $this->saveReadingPosition();
        $this->resetTafsir();
    }

    public function updatedSelectedLanguage(): void
    {
        $this->loadPreferredMealsForLanguage();
        $this->syncSelectedMeal();
    }

    public function saveMealPreferences(): void
    {
        $user = auth()->user();

        if (! $user || ! $this->selectedLanguage) {
            return;
        }

        $allowedMealKeys = VerseTranslation::query()
            ->where('language', $this->selectedLanguage)
            ->whereIn('meal_key', $this->preferredMealKeys)
            ->distinct()
            ->pluck('meal_key')
            ->unique()
            ->values()
            ->all();

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

        $this->preferredMealKeys = $allowedMealKeys;
        $this->syncSelectedMeal();
    }

    public function saveTag(): void
    {
        $this->validate([
            'newTagName' => ['required', 'string', 'max:50'],
        ]);

        $user = auth()->user();
        if (! $user) {
            return;
        }

        $name = trim($this->newTagName);
        $slug = Str::slug($name);

        if ($slug === '') {
            $slug = Str::lower(Str::random(8));
        }

        $tag = ResearchTag::query()->firstOrCreate(
            ['user_id' => $user->id, 'slug' => $slug],
            ['name' => $name]
        );

        if (! in_array($tag->id, $this->selectedTagIds, true)) {
            $this->selectedTagIds[] = $tag->id;
        }

        $this->newTagName = '';
    }

    public function saveNote(): void
    {
        $this->validate([
            'noteType' => ['required', 'in:note,footnote,research'],
            'noteTitle' => ['required', 'string', 'max:255'],
            'noteContent' => ['required', 'string'],
            'selectedTagIds' => ['array'],
            'selectedTagIds.*' => ['integer', 'exists:research_tags,id'],
        ]);

        $user = auth()->user();
        if (! $user || ! $this->selectedSura || ! $this->selectedAya) {
            return;
        }

        $note = ResearchNote::query()->create([
            'user_id' => $user->id,
            'sura' => $this->selectedSura,
            'aya' => $this->selectedAya,
            'word_position' => $this->noteWordPosition ?: null,
            'type' => $this->noteType,
            'title' => trim($this->noteTitle),
            'content' => trim($this->noteContent),
        ]);

        $allowedTagIds = ResearchTag::query()
            ->where('user_id', $user->id)
            ->whereIn('id', $this->selectedTagIds)
            ->pluck('id')
            ->all();

        $note->tags()->sync($allowedTagIds);

        $this->resetNoteForm();
        $this->dispatch('close-note-modal');
    }

    public function editNote(int $noteId): void
    {
        $note = $this->findUserNote($noteId);
        if (! $note) {
            return;
        }

        $this->editingNoteId = $note->id;
        $this->noteType = $note->type;
        $this->noteWordPosition = $note->word_position;
        $this->noteTitle = $note->title;
        $this->noteContent = $note->content;
        $this->selectedTagIds = $note->tags()->pluck('research_tags.id')->all();
    }

    public function updateNote(): void
    {
        if (! $this->editingNoteId) {
            return;
        }

        $this->validate([
            'noteType' => ['required', 'in:note,footnote,research'],
            'noteTitle' => ['required', 'string', 'max:255'],
            'noteContent' => ['required', 'string'],
            'selectedTagIds' => ['array'],
            'selectedTagIds.*' => ['integer', 'exists:research_tags,id'],
        ]);

        $note = $this->findUserNote($this->editingNoteId);
        $user = auth()->user();

        if (! $note || ! $user) {
            return;
        }

        $note->update([
            'type' => $this->noteType,
            'word_position' => $this->noteWordPosition ?: null,
            'title' => trim($this->noteTitle),
            'content' => trim($this->noteContent),
        ]);

        $allowedTagIds = ResearchTag::query()
            ->where('user_id', $user->id)
            ->whereIn('id', $this->selectedTagIds)
            ->pluck('id')
            ->all();

        $note->tags()->sync($allowedTagIds);

        $this->resetNoteForm();
        $this->dispatch('close-note-modal');
    }

    public function deleteNote(int $noteId): void
    {
        $note = $this->findUserNote($noteId);
        if (! $note) {
            return;
        }

        $note->delete();

        if ($this->editingNoteId === $noteId) {
            $this->resetNoteForm();
        }
    }

    /* ── Tefsir ───────────────────────────────────────────────────── */

    public function toggleTafsir(): void
    {
        if (! $this->preferredTafsirId || ! $this->selectedSura || ! $this->selectedAya) {
            return;
        }

        $this->tafsirOpen = ! $this->tafsirOpen;

        if ($this->tafsirOpen && $this->tafsirText === null) {
            $this->tafsirText = app(TafsirService::class)
                ->getAyahTafsir($this->selectedSura, $this->selectedAya, $this->preferredTafsirId);
        }
    }

    /* ── Not form ─────────────────────────────────────────────────── */

    public function resetNoteForm(): void
    {
        $this->editingNoteId = null;
        $this->noteType = 'note';
        $this->noteWordPosition = null;
        $this->noteTitle = '';
        $this->noteContent = '';
        $this->selectedTagIds = [];
    }

    public function getCurrentSuraNameProperty(): string
    {
        return self::$SURA_NAMES[$this->selectedSura] ?? ('Sure ' . $this->selectedSura);
    }

    public function getSurasProperty()
    {
        return QuranWord::query()
            ->select('sura')
            ->distinct()
            ->orderBy('sura')
            ->pluck('sura');
    }

    public function getAyasProperty()
    {
        if (! $this->selectedSura) {
            return collect();
        }

        return QuranWord::query()
            ->where('sura', $this->selectedSura)
            ->select('aya')
            ->distinct()
            ->orderBy('aya')
            ->pluck('aya');
    }

    public function getMealsProperty()
    {
        $query = VerseTranslation::query()
            ->where('language', $this->selectedLanguage)
            ->select('meal_key')
            ->distinct()
            ->orderBy('meal_key');

        if (! empty($this->preferredMealKeys)) {
            $query->whereIn('meal_key', $this->preferredMealKeys);
        }

        return $query->pluck('meal_key');
    }

    public function getAllMealsForLanguageProperty()
    {
        return VerseTranslation::query()
            ->where('language', $this->selectedLanguage)
            ->select('meal_key')
            ->distinct()
            ->orderBy('meal_key')
            ->pluck('meal_key');
    }

    public function getLanguagesProperty()
    {
        return VerseTranslation::query()
            ->select('language')
            ->distinct()
            ->orderBy('language')
            ->pluck('language');
    }

    public function getAvailableWordPositionsProperty()
    {
        return $this->currentWords->pluck('position');
    }

    public function getUserTagsProperty()
    {
        $user = auth()->user();

        if (! $user) {
            return collect();
        }

        return ResearchTag::query()
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get();
    }

    public function getCurrentNotesProperty()
    {
        $user = auth()->user();

        if (! $user || ! $this->selectedSura || ! $this->selectedAya) {
            return collect();
        }

        return ResearchNote::query()
            ->with('tags')
            ->where('user_id', $user->id)
            ->where('sura', $this->selectedSura)
            ->where('aya', $this->selectedAya)
            ->when($this->filterTagId, fn ($q) => $q->whereHas('tags', fn ($qq) => $qq->where('research_tags.id', $this->filterTagId)))
            ->orderByDesc('updated_at')
            ->get();
    }

    public function getCurrentWordsProperty()
    {
        if (! $this->selectedSura || ! $this->selectedAya) {
            return collect();
        }

        return QuranWord::query()
            ->where('sura', $this->selectedSura)
            ->where('aya', $this->selectedAya)
            ->orderBy('position')
            ->get(['position', 'text', 'simple']);
    }

    public function getCurrentArabicTextProperty(): string
    {
        return $this->currentWords
            ->pluck('text')
            ->implode(' ');
    }

    public function getCurrentSimpleTextProperty(): string
    {
        return $this->currentWords
            ->pluck('simple')
            ->implode(' ');
    }

    public function getCurrentTranslationProperty(): string
    {
        if (! $this->selectedSura || ! $this->selectedAya || ! $this->selectedMeal || ! $this->selectedLanguage) {
            return '';
        }

        return (string) VerseTranslation::query()
            ->where('sura', $this->selectedSura)
            ->where('aya', $this->selectedAya)
            ->where('meal_key', $this->selectedMeal)
            ->where('language', $this->selectedLanguage)
            ->value('text');
    }

    public function getCurrentPreferredTranslationsProperty()
    {
        if (! $this->selectedSura || ! $this->selectedAya || ! $this->selectedLanguage) {
            return collect();
        }

        if (empty($this->preferredMealKeys)) {
            if (! $this->selectedMeal) {
                return collect();
            }

            return VerseTranslation::query()
                ->where('sura', $this->selectedSura)
                ->where('aya', $this->selectedAya)
                ->where('language', $this->selectedLanguage)
                ->where('meal_key', $this->selectedMeal)
                ->orderBy('meal_key')
                ->get(['meal_key', 'text']);
        }

        return VerseTranslation::query()
            ->where('sura', $this->selectedSura)
            ->where('aya', $this->selectedAya)
            ->where('language', $this->selectedLanguage)
            ->whereIn('meal_key', $this->preferredMealKeys)
            ->orderBy('meal_key')
            ->get(['meal_key', 'text']);
    }

    public function render(): View
    {
        return view('livewire.quran-text-page');
    }

    private function resetTafsir(): void
    {
        $this->tafsirOpen = false;
        $this->tafsirText = null;
    }

    private function syncAya(): void
    {
        $firstAya = QuranWord::query()
            ->where('sura', $this->selectedSura)
            ->orderBy('aya')
            ->value('aya');

        $this->selectedAya = $firstAya ? (int) $firstAya : null;
    }

    private function syncSelectedMeal(): void
    {
        $meals = $this->meals;

        if ($meals->isEmpty()) {
            $this->selectedMeal = null;
            return;
        }

        if (! $this->selectedMeal || ! $meals->contains($this->selectedMeal)) {
            $this->selectedMeal = (string) $meals->first();
        }
    }

    private function loadPreferredMealsForLanguage(): void
    {
        $user = auth()->user();

        if (! $user || ! $this->selectedLanguage) {
            $this->preferredMealKeys = [];
            return;
        }

        $this->preferredMealKeys = UserMealPreference::query()
            ->where('user_id', $user->id)
            ->where('language', $this->selectedLanguage)
            ->orderBy('meal_key')
            ->pluck('meal_key')
            ->all();
    }

    private function findUserNote(int $noteId): ?ResearchNote
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        return ResearchNote::query()
            ->where('id', $noteId)
            ->where('user_id', $user->id)
            ->first();
    }

    private function saveReadingPosition(): void
    {
        $user = auth()->user();

        if (! $user || ! $this->selectedSura || ! $this->selectedAya) {
            return;
        }

        UserSetting::updateOrCreate(
            ['user_id' => $user->id],
            [
                'last_read_sura' => $this->selectedSura,
                'last_read_aya'  => $this->selectedAya,
            ]
        );
    }
}
