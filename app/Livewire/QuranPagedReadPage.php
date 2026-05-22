<?php

namespace App\Livewire;

use App\Models\QuranWord;
use App\Models\ResearchNote;
use App\Models\ResearchTag;
use App\Models\UserMealPreference;
use App\Models\UserQuranPageBookmark;
use App\Models\UserSetting;
use App\Models\VerseTranslation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class QuranPagedReadPage extends Component
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

    // ── Navigation state ───────────────────────────────────────────────
    public int    $selectedSura = 1;
    public int    $selectedAya  = 1;
    public int    $selectedPage = 1;

    // ── Bookmark state ─────────────────────────────────────────────────
    public string $bookmarkLabel = '';

    // ── Modal state ────────────────────────────────────────────────────
    public bool   $showNotesModal    = false;
    public int    $modalSura         = 1;
    public int    $modalAya          = 1;
    public string $modalArabicText   = '';
    public string $modalTranslation  = '';
    public string $modalMealKeyLabel = '';
    public string $modalTab          = 'notes'; // 'notes' | 'add'

    // ── Note form ──────────────────────────────────────────────────────
    public string $newNoteType    = 'note';
    public string $newNoteTitle   = '';
    public string $newNoteContent = '';
    public ?int   $newNoteWordPos = null;
    public array  $newNoteTagIds  = [];
    public string $newTagName     = '';

    // ── Mount ──────────────────────────────────────────────────────────
    public function mount(): void
    {
        $first = QuranWord::query()
            ->where('char_type', 'word')
            ->orderBy('page')
            ->orderBy('sura')
            ->orderBy('aya')
            ->first(['sura', 'aya', 'page']);

        $this->selectedSura = (int) (request()->query('sura', $first?->sura ?? 1));
        $this->selectedAya  = (int) (request()->query('aya', $first?->aya ?? 1));

        if (! $this->ayaExists($this->selectedSura, $this->selectedAya)) {
            $this->selectedSura = (int) ($first?->sura ?? 1);
            $this->selectedAya  = (int) ($first?->aya ?? 1);
        }

        $this->selectedPage = $this->findPageForAya($this->selectedSura, $this->selectedAya)
            ?? (int) ($first?->page ?? 1);
    }

    // ── Navigation ─────────────────────────────────────────────────────
    public function updatedSelectedSura($value): void
    {
        $this->selectedSura = (int) $value;
        $this->selectedAya  = (int) QuranWord::query()
            ->where('char_type', 'word')
            ->where('sura', $this->selectedSura)
            ->min('aya');

        $this->selectedPage = $this->findPageForAya($this->selectedSura, $this->selectedAya)
            ?? $this->selectedPage;
    }

    public function updatedSelectedAya($value): void
    {
        $this->selectedAya = (int) $value;

        if (! $this->ayaExists($this->selectedSura, $this->selectedAya)) {
            $this->selectedAya = (int) QuranWord::query()
                ->where('char_type', 'word')
                ->where('sura', $this->selectedSura)
                ->min('aya');
        }

        $this->selectedPage = $this->findPageForAya($this->selectedSura, $this->selectedAya)
            ?? $this->selectedPage;
    }

    public function updatedSelectedPage($value): void
    {
        $this->selectedPage = (int) $value;

        $firstOnPage = QuranWord::query()
            ->where('char_type', 'word')
            ->where('page', $this->selectedPage)
            ->orderBy('sura')
            ->orderBy('aya')
            ->first(['sura', 'aya']);

        if ($firstOnPage) {
            $this->selectedSura = (int) $firstOnPage->sura;
            $this->selectedAya  = (int) $firstOnPage->aya;
        }
    }

    public function prevPage(): void
    {
        $pages = $this->getPageOptions();
        $idx   = array_search($this->selectedPage, $pages, true);

        if ($idx !== false && $idx > 0) {
            $this->selectedPage = $pages[$idx - 1];
            $this->updatedSelectedPage($this->selectedPage);
        }
    }

    public function nextPage(): void
    {
        $pages = $this->getPageOptions();
        $idx   = array_search($this->selectedPage, $pages, true);

        if ($idx !== false && $idx < count($pages) - 1) {
            $this->selectedPage = $pages[$idx + 1];
            $this->updatedSelectedPage($this->selectedPage);
        }
    }

    // ── Bookmarks ──────────────────────────────────────────────────────
    public function addBookmark(): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        UserQuranPageBookmark::query()->updateOrCreate(
            ['user_id' => $user->id, 'page' => $this->selectedPage],
            ['label' => trim($this->bookmarkLabel) ?: null]
        );

        $this->bookmarkLabel = '';
    }

    public function removeBookmark(int $page): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        UserQuranPageBookmark::query()
            ->where('user_id', $user->id)
            ->where('page', $page)
            ->delete();
    }

    // ── Modal ──────────────────────────────────────────────────────────
    public function openNotesModal(int $sura, int $aya): void
    {
        $this->modalSura = $sura;
        $this->modalAya  = $aya;
        $this->modalTab  = 'notes';

        $this->loadModalArabicText();
        $this->loadModalTranslation();
        $this->resetNoteForm();

        $this->showNotesModal = true;
    }

    public function closeNotesModal(): void
    {
        $this->showNotesModal = false;
        $this->resetNoteForm();
    }

    public function switchTab(string $tab): void
    {
        $this->modalTab = $tab;
        if ($tab === 'add') {
            $this->resetNoteForm();
        }
    }

    // ── Note CRUD ──────────────────────────────────────────────────────
    public function saveNote(): void
    {
        $this->validate([
            'newNoteContent' => 'required|min:2',
            'newNoteType'    => 'required|in:note,footnote,research',
            'newNoteTitle'   => 'nullable|max:255',
        ]);

        $user = auth()->user();
        if (! $user) {
            return;
        }

        $note = ResearchNote::query()->create([
            'user_id'       => $user->id,
            'sura'          => $this->modalSura,
            'aya'           => $this->modalAya,
            'type'          => $this->newNoteType,
            'title'         => trim($this->newNoteTitle) ?: null,
            'content'       => trim($this->newNoteContent),
            'word_position' => $this->newNoteWordPos ?: null,
        ]);

        if (! empty($this->newNoteTagIds)) {
            $note->tags()->sync($this->newNoteTagIds);
        }

        $this->resetNoteForm();
        $this->modalTab = 'notes';

        $this->dispatch('note-saved');
    }

    public function createAndAttachTag(): void
    {
        $name = trim($this->newTagName);
        if (! $name) {
            return;
        }

        $user = auth()->user();
        if (! $user) {
            return;
        }

        $tag = ResearchTag::query()->firstOrCreate(
            ['user_id' => $user->id, 'name' => $name],
            ['slug' => \Illuminate\Support\Str::slug($name)]
        );

        if (! in_array($tag->id, $this->newNoteTagIds, true)) {
            $this->newNoteTagIds[] = $tag->id;
        }

        $this->newTagName = '';
    }

    // ── Computed properties ────────────────────────────────────────────
    public function getModalNotesProperty(): Collection
    {
        $user = auth()->user();
        if (! $user) {
            return collect();
        }

        return ResearchNote::query()
            ->with('tags')
            ->where('user_id', $user->id)
            ->where('sura', $this->modalSura)
            ->where('aya', $this->modalAya)
            ->orderByDesc('updated_at')
            ->get();
    }

    public function getModalWordTextsProperty(): array
    {
        $notes = $this->modalNotes;
        if ($notes->isEmpty()) {
            return [];
        }

        $pairs = $notes
            ->filter(fn ($n) => ! is_null($n->word_position))
            ->map(fn ($n) => [
                'note_id'  => (int) $n->id,
                'sura'     => (int) $n->sura,
                'aya'      => (int) $n->aya,
                'position' => (int) $n->word_position,
            ])
            ->values();

        if ($pairs->isEmpty()) {
            return [];
        }

        $words = QuranWord::query()
            ->where('char_type', 'word')
            ->where(function ($q) use ($pairs): void {
                foreach ($pairs as $p) {
                    $q->orWhere(fn ($nq) => $nq
                        ->where('sura', $p['sura'])
                        ->where('aya', $p['aya'])
                        ->where('position', $p['position'])
                    );
                }
            })
            ->get(['sura', 'aya', 'position', 'text']);

        $wordMap = [];
        foreach ($words as $w) {
            $wordMap["{$w->sura}:{$w->aya}:{$w->position}"] = $w->text;
        }

        $result = [];
        foreach ($pairs as $p) {
            $result[$p['note_id']] = $wordMap["{$p['sura']}:{$p['aya']}:{$p['position']}"] ?? null;
        }

        return $result;
    }

    /** Words of the currently open aya (for word-position select in form) */
    public function getModalWordsProperty(): Collection
    {
        if (! $this->showNotesModal) {
            return collect();
        }

        return QuranWord::query()
            ->where('sura', $this->modalSura)
            ->where('aya', $this->modalAya)
            ->where('char_type', 'word')
            ->orderBy('position')
            ->get(['position', 'text']);
    }

    /** User's own tags for the note form */
    public function getUserTagsProperty(): Collection
    {
        $user = auth()->user();
        if (! $user) {
            return collect();
        }

        return ResearchTag::query()
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getBookmarksProperty(): Collection
    {
        $user = auth()->user();
        if (! $user) {
            return collect();
        }

        return UserQuranPageBookmark::query()
            ->where('user_id', $user->id)
            ->orderBy('page')
            ->get();
    }

    public function getIsCurrentPageBookmarkedProperty(): bool
    {
        return $this->bookmarks->contains(fn ($b) => (int) $b->page === $this->selectedPage);
    }

    // ── Render ─────────────────────────────────────────────────────────
    public function render(): View
    {
        $rows       = $this->getPageRows($this->selectedPage);
        $noteCounts = $this->getNoteCountsForRows($rows);

        $rows = $rows->map(function (array $row) use ($noteCounts): array {
            $row['note_count'] = (int) ($noteCounts["{$row['sura']}:{$row['aya']}"] ?? 0);

            return $row;
        });

        return view('livewire.quran-paged-read-page', [
            'rows'        => $rows,
            'suraOptions' => $this->getSuraOptions(),
            'ayaOptions'  => $this->getAyaOptions(),
            'pageOptions' => $this->getPageOptions(),
            'SURA_NAMES'  => self::$SURA_NAMES,
        ]);
    }

    // ── Private helpers ─────────────────────────────────────────────────
    private function loadModalArabicText(): void
    {
        $this->modalArabicText = QuranWord::query()
            ->where('sura', $this->modalSura)
            ->where('aya', $this->modalAya)
            ->where('char_type', 'word')
            ->orderBy('position')
            ->pluck('text')
            ->filter()
            ->implode(' ');
    }

    private function loadModalTranslation(): void
    {
        $user     = auth()->user();
        $setting  = $user?->setting;
        $language = $setting?->preferred_language ?? 'tr';

        // User's first preferred meal key for their language
        $mealKey = UserMealPreference::query()
            ->where('user_id', $user?->id)
            ->where('language', $language)
            ->value('meal_key') ?? 'tr.diyanet';

        $this->modalTranslation  = VerseTranslation::query()
            ->where('sura', $this->modalSura)
            ->where('aya', $this->modalAya)
            ->where('meal_key', $mealKey)
            ->value('text') ?? '';

        // Human-readable label (strip prefix like "tr." → "diyanet")
        $parts = explode('.', $mealKey);
        $this->modalMealKeyLabel = count($parts) === 2
            ? mb_strtoupper($parts[0]) . ' — ' . ucwords(str_replace(['-', '_'], ' ', $parts[1]))
            : $mealKey;
    }

    private function resetNoteForm(): void
    {
        $this->newNoteType    = 'note';
        $this->newNoteTitle   = '';
        $this->newNoteContent = '';
        $this->newNoteWordPos = null;
        $this->newNoteTagIds  = [];
        $this->newTagName     = '';
        $this->resetValidation();
    }

    private function getPageRows(int $page): Collection
    {
        $words = QuranWord::query()
            ->where('char_type', 'word')
            ->where('page', $page)
            ->orderBy('sura')
            ->orderBy('aya')
            ->orderBy('line')
            ->orderBy('position')
            ->get(['sura', 'aya', 'text']);

        return $words
            ->groupBy(fn ($w) => "{$w->sura}:{$w->aya}")
            ->map(function (Collection $group, string $key): array {
                [$sura, $aya] = array_map('intval', explode(':', $key));

                return [
                    'sura'        => $sura,
                    'aya'         => $aya,
                    'sura_name'   => self::$SURA_NAMES[$sura] ?? "Sure {$sura}",
                    'arabic_text' => $group->pluck('text')->filter()->implode(' '),
                ];
            })
            ->values();
    }

    private function getSuraOptions(): array
    {
        return QuranWord::query()
            ->where('char_type', 'word')
            ->distinct()
            ->orderBy('sura')
            ->pluck('sura')
            ->map(fn ($s) => (int) $s)
            ->map(fn (int $s): array => [
                'value' => $s,
                'label' => "{$s}. Sure — " . (self::$SURA_NAMES[$s] ?? "Sure {$s}"),
            ])
            ->all();
    }

    private function getAyaOptions(): array
    {
        return QuranWord::query()
            ->where('char_type', 'word')
            ->where('sura', $this->selectedSura)
            ->distinct()
            ->orderBy('aya')
            ->pluck('aya')
            ->map(fn ($a) => (int) $a)
            ->map(fn (int $a): array => ['value' => $a, 'label' => "{$a}. Ayet"])
            ->all();
    }

    private function getPageOptions(): array
    {
        return QuranWord::query()
            ->where('char_type', 'word')
            ->distinct()
            ->orderBy('page')
            ->pluck('page')
            ->map(fn ($p) => (int) $p)
            ->all();
    }

    private function findPageForAya(int $sura, int $aya): ?int
    {
        $page = QuranWord::query()
            ->where('char_type', 'word')
            ->where('sura', $sura)
            ->where('aya', $aya)
            ->min('page');

        return $page !== null ? (int) $page : null;
    }

    private function ayaExists(int $sura, int $aya): bool
    {
        return QuranWord::query()
            ->where('char_type', 'word')
            ->where('sura', $sura)
            ->where('aya', $aya)
            ->exists();
    }

    private function getNoteCountsForRows(Collection $rows): array
    {
        $user = auth()->user();
        if (! $user || $rows->isEmpty()) {
            return [];
        }

        $counts = ResearchNote::query()
            ->where('user_id', $user->id)
            ->where(function ($q) use ($rows): void {
                foreach ($rows as $row) {
                    $q->orWhere(fn ($nq) => $nq
                        ->where('sura', $row['sura'])
                        ->where('aya', $row['aya'])
                    );
                }
            })
            ->selectRaw('sura, aya, COUNT(*) as cnt')
            ->groupBy('sura', 'aya')
            ->get();

        $map = [];
        foreach ($counts as $c) {
            $map["{$c->sura}:{$c->aya}"] = (int) $c->cnt;
        }

        return $map;
    }
}
