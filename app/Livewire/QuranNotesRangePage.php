<?php

namespace App\Livewire;

use App\Models\QuranWord;
use App\Models\ResearchNote;
use App\Models\ResearchTag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class QuranNotesRangePage extends Component
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

    public ?int $startSura = null;
    public ?int $startAya  = null;
    public ?int $endSura   = null;
    public ?int $endAya    = null;

    public ?int  $filterTagId  = null;
    public string $filterType  = '';

    public bool $loaded = false;

    public function mount(): void
    {
        $firstSura = QuranWord::query()->orderBy('sura')->value('sura');

        // Pre-fill from URL params (e.g. from dashboard tag/search links)
        $tagParam = request()->query('tag');
        if ($tagParam && is_numeric($tagParam)) {
            $this->filterTagId = (int) $tagParam;
        }

        $this->startSura = $firstSura;
        $this->endSura   = QuranWord::query()->orderByDesc('sura')->value('sura') ?? $firstSura;

        $this->syncStartAya();
        $this->syncEndAya(last: true);

        // Auto-load results when arriving with a tag filter
        if ($this->filterTagId) {
            $this->loaded = true;
        }
    }

    /* ── Lifecycle hooks ──────────────────────────────── */

    public function updatedStartSura(): void
    {
        $this->syncStartAya();
        $this->loaded = false;
    }

    public function updatedEndSura(): void
    {
        $this->syncEndAya();
        $this->loaded = false;
    }

    public function updatedStartAya(): void  { $this->loaded = false; }
    public function updatedEndAya(): void    { $this->loaded = false; }

    /* ── Yükle ────────────────────────────────────────── */

    public function load(): void
    {
        if (! $this->isRangeValid) {
            return;
        }

        $this->loaded = true;
    }

    /* ── Computed: range geçerliliği ──────────────────── */

    public function getIsRangeValidProperty(): bool
    {
        if (! $this->startSura || ! $this->startAya || ! $this->endSura || ! $this->endAya) {
            return false;
        }

        if ($this->endSura < $this->startSura) {
            return false;
        }

        if ($this->endSura === $this->startSura && $this->endAya < $this->startAya) {
            return false;
        }

        return true;
    }

    public static function getSuraNameStatic(int $sura): string
    {
        return self::$SURA_NAMES[$sura] ?? ('Sure ' . $sura);
    }

    /* ── Computed: select listeleri ───────────────────── */

    public function getSurasProperty(): Collection
    {
        return QuranWord::query()
            ->select('sura')
            ->distinct()
            ->orderBy('sura')
            ->pluck('sura');
    }

    public function getStartAyasProperty(): Collection
    {
        if (! $this->startSura) {
            return collect();
        }

        return QuranWord::query()
            ->where('sura', $this->startSura)
            ->select('aya')
            ->distinct()
            ->orderBy('aya')
            ->pluck('aya');
    }

    public function getEndAyasProperty(): Collection
    {
        if (! $this->endSura) {
            return collect();
        }

        return QuranWord::query()
            ->where('sura', $this->endSura)
            ->select('aya')
            ->distinct()
            ->orderBy('aya')
            ->pluck('aya');
    }

    public function getUserTagsProperty(): Collection
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

    /* ── Computed: gruplu notlar (ana veri) ───────────── */

    public function getGroupedNotesProperty(): Collection
    {
        if (! $this->loaded || ! $this->isRangeValid) {
            return collect();
        }

        $user = auth()->user();
        if (! $user) {
            return collect();
        }

        /* 1. Notları aralıkta sorgula */
        $notes = ResearchNote::query()
            ->with('tags')
            ->where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('sura', '>', $this->startSura)
                    ->orWhere(fn ($q) => $q->where('sura', $this->startSura)->where('aya', '>=', $this->startAya));
            })
            ->where(function ($q) {
                $q->where('sura', '<', $this->endSura)
                    ->orWhere(fn ($q) => $q->where('sura', $this->endSura)->where('aya', '<=', $this->endAya));
            })
            ->when($this->filterTagId, fn ($q) => $q->whereHas('tags', fn ($qq) => $qq->where('research_tags.id', $this->filterTagId)))
            ->when($this->filterType !== '', fn ($q) => $q->where('type', $this->filterType))
            ->orderBy('sura')
            ->orderBy('aya')
            ->orderByDesc('updated_at')
            ->get();

        if ($notes->isEmpty()) {
            return collect();
        }

        /* 2. Ayet Arapça metinlerini toplu çek */
        $ayaPairs = $notes
            ->map(fn ($n) => ['sura' => $n->sura, 'aya' => $n->aya])
            ->unique(fn ($p) => $p['sura'] . ':' . $p['aya'])
            ->values();

        $arabicTexts = QuranWord::query()
            ->where(function ($q) use ($ayaPairs) {
                foreach ($ayaPairs as $pair) {
                    $q->orWhere(fn ($qq) => $qq->where('sura', $pair['sura'])->where('aya', $pair['aya']));
                }
            })
            ->orderBy('sura')
            ->orderBy('aya')
            ->orderBy('position')
            ->get(['sura', 'aya', 'text'])
            ->groupBy(fn ($w) => $w->sura . ':' . $w->aya)
            ->map(fn ($g) => $g->pluck('text')->implode(' '));

        /* 3. Kelime pozisyonlu notlar için Arapça kelimeleri çek */
        $wordNotes = $notes->filter(fn ($n) => $n->word_position);

        if ($wordNotes->isNotEmpty()) {
            $wordMap = QuranWord::query()
                ->where(function ($q) use ($wordNotes) {
                    foreach ($wordNotes as $n) {
                        $q->orWhere(fn ($qq) => $qq
                            ->where('sura', $n->sura)
                            ->where('aya', $n->aya)
                            ->where('position', $n->word_position));
                    }
                })
                ->get(['sura', 'aya', 'position', 'text'])
                ->keyBy(fn ($w) => "{$w->sura}:{$w->aya}:{$w->position}");

            $wordNotes->each(function ($note) use ($wordMap) {
                $note->word_text = $wordMap->get("{$note->sura}:{$note->aya}:{$note->word_position}")?->text;
            });
        }

        /* 4. Sure → Ayet → Notlar şeklinde grupla */
        return $notes
            ->groupBy('sura')
            ->map(function ($suraGroup, $sura) use ($arabicTexts) {
                return [
                    'name'      => self::$SURA_NAMES[$sura] ?? ('Sure ' . $sura),
                    'noteCount' => $suraGroup->count(),
                    'ayas'      => $suraGroup->groupBy('aya')->map(function ($ayaGroup, $aya) use ($sura, $arabicTexts) {
                        return [
                            'arabic' => $arabicTexts->get($sura . ':' . $aya, ''),
                            'notes'  => $ayaGroup,
                        ];
                    }),
                ];
            });
    }

    /* ── Computed: özet sayılar ───────────────────────── */

    public function getTotalNoteCountProperty(): int
    {
        return $this->groupedNotes->sum(fn ($s) => $s['noteCount']);
    }

    /* ── Render ───────────────────────────────────────── */

    public function render(): View
    {
        return view('livewire.quran-notes-range-page');
    }

    /* ── Private ──────────────────────────────────────── */

    private function syncStartAya(): void
    {
        $first = QuranWord::query()
            ->where('sura', $this->startSura)
            ->orderBy('aya')
            ->value('aya');

        $this->startAya = $first ? (int) $first : null;
    }

    private function syncEndAya(bool $last = false): void
    {
        if ($last) {
            $aya = QuranWord::query()
                ->where('sura', $this->endSura)
                ->orderByDesc('aya')
                ->value('aya');
        } else {
            $aya = QuranWord::query()
                ->where('sura', $this->endSura)
                ->orderBy('aya')
                ->value('aya');
        }

        $this->endAya = $aya ? (int) $aya : null;
    }
}
