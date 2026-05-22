<?php

namespace App\Livewire;

use App\Models\ResearchNote;
use App\Models\ResearchTag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';

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

    public function getResultsProperty(): array
    {
        $q = trim($this->query);

        if (mb_strlen($q) < 2) {
            return ['suras' => [], 'notes' => [], 'tags' => [], 'hasAny' => false];
        }

        $user = auth()->user();

        // ── Sura search (static) ──────────────────────────────────────────
        $qLower = mb_strtolower($q);
        $suras = collect(self::$SURA_NAMES)
            ->filter(function (string $name, int $num) use ($q, $qLower): bool {
                if (is_numeric($q) && $num === (int) $q) {
                    return true;
                }

                return str_contains(mb_strtolower($name), $qLower);
            })
            ->map(fn (string $name, int $num) => ['num' => $num, 'name' => $name])
            ->take(4)
            ->values()
            ->all();

        // ── Note search ───────────────────────────────────────────────────
        $notes = ResearchNote::query()
            ->where('user_id', $user?->id)
            ->where(function ($builder) use ($q): void {
                $builder->where('title', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            })
            ->select(['id', 'sura', 'aya', 'title', 'content', 'type'])
            ->orderByDesc('updated_at')
            ->take(5)
            ->get()
            ->map(function (ResearchNote $note): array {
                $label = $note->title
                    ? $note->title
                    : Str::limit(strip_tags($note->content ?? ''), 55);

                return [
                    'sura'      => $note->sura,
                    'aya'       => $note->aya,
                    'label'     => $label ?: 'Başlıksız not',
                    'type'      => $note->type,
                    'sura_name' => self::$SURA_NAMES[$note->sura] ?? "Sure {$note->sura}",
                    'ref'       => "{$note->sura}:{$note->aya}",
                ];
            })
            ->all();

        // ── Tag search ────────────────────────────────────────────────────
        $tags = ResearchTag::query()
            ->where('user_id', $user?->id)
            ->where('name', 'like', "%{$q}%")
            ->select(['id', 'name'])
            ->orderBy('name')
            ->take(4)
            ->get()
            ->map(fn (ResearchTag $tag) => ['id' => $tag->id, 'name' => $tag->name])
            ->all();

        return [
            'suras'  => $suras,
            'notes'  => $notes,
            'tags'   => $tags,
            'hasAny' => (count($suras) + count($notes) + count($tags)) > 0,
        ];
    }

    public function render(): View
    {
        return view('livewire.global-search');
    }
}
