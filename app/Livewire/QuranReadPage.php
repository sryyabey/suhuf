<?php

namespace App\Livewire;

use App\Models\QuranWord;
use App\Models\ResearchNote;
use App\Models\UserMealPreference;
use App\Models\UserSetting;
use App\Models\VerseTranslation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class QuranReadPage extends Component
{
    // ── Static data ────────────────────────────────────────────────────
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

    protected static array $SURA_ARABIC_NAMES = [
        1 => 'الفاتحة', 2 => 'البقرة', 3 => 'آل عمران', 4 => 'النساء', 5 => 'المائدة',
        6 => 'الأنعام', 7 => 'الأعراف', 8 => 'الأنفال', 9 => 'التوبة', 10 => 'يونس',
        11 => 'هود', 12 => 'يوسف', 13 => 'الرعد', 14 => 'إبراهيم', 15 => 'الحجر',
        16 => 'النحل', 17 => 'الإسراء', 18 => 'الكهف', 19 => 'مريم', 20 => 'طه',
        21 => 'الأنبياء', 22 => 'الحج', 23 => 'المؤمنون', 24 => 'النور', 25 => 'الفرقان',
        26 => 'الشعراء', 27 => 'النمل', 28 => 'القصص', 29 => 'العنكبوت', 30 => 'الروم',
        31 => 'لقمان', 32 => 'السجدة', 33 => 'الأحزاب', 34 => 'سبأ', 35 => 'فاطر',
        36 => 'يس', 37 => 'الصافات', 38 => 'ص', 39 => 'الزمر', 40 => 'غافر',
        41 => 'فصلت', 42 => 'الشورى', 43 => 'الزخرف', 44 => 'الدخان', 45 => 'الجاثية',
        46 => 'الأحقاف', 47 => 'محمد', 48 => 'الفتح', 49 => 'الحجرات', 50 => 'ق',
        51 => 'الذاريات', 52 => 'الطور', 53 => 'النجم', 54 => 'القمر', 55 => 'الرحمن',
        56 => 'الواقعة', 57 => 'الحديد', 58 => 'المجادلة', 59 => 'الحشر', 60 => 'الممتحنة',
        61 => 'الصف', 62 => 'الجمعة', 63 => 'المنافقون', 64 => 'التغابن', 65 => 'الطلاق',
        66 => 'التحريم', 67 => 'الملك', 68 => 'القلم', 69 => 'الحاقة', 70 => 'المعارج',
        71 => 'نوح', 72 => 'الجن', 73 => 'المزمل', 74 => 'المدثر', 75 => 'القيامة',
        76 => 'الإنسان', 77 => 'المرسلات', 78 => 'النبأ', 79 => 'النازعات', 80 => 'عبس',
        81 => 'التكوير', 82 => 'الانفطار', 83 => 'المطففين', 84 => 'الانشقاق', 85 => 'البروج',
        86 => 'الطارق', 87 => 'الأعلى', 88 => 'الغاشية', 89 => 'الفجر', 90 => 'البلد',
        91 => 'الشمس', 92 => 'الليل', 93 => 'الضحى', 94 => 'الشرح', 95 => 'التين',
        96 => 'العلق', 97 => 'القدر', 98 => 'البينة', 99 => 'الزلزلة', 100 => 'العاديات',
        101 => 'القارعة', 102 => 'التكاثر', 103 => 'العصر', 104 => 'الهمزة', 105 => 'الفيل',
        106 => 'قريش', 107 => 'الماعون', 108 => 'الكوثر', 109 => 'الكافرون', 110 => 'النصر',
        111 => 'المسد', 112 => 'الإخلاص', 113 => 'الفلق', 114 => 'الناس',
    ];

    protected static array $MEAL_KEY_LABELS = [
        'tr.diyanet'         => 'Diyanet İşleri',
        'tr.yazir'           => 'Elmalılı H. Yazır',
        'tr.ates'            => 'Süleyman Ateş',
        'tr.bulac'           => 'Ali Bulaç',
        'tr.ozturk'          => 'Y. N. Öztürk',
        'tr.vakfi'           => 'Türkiye Diyanet Vakfı',
        'tr.yildirim'        => 'Suat Yıldırım',
        'tr.yuksel'          => 'Edip Yüksel',
        'tr.golpinarli'      => 'A. Gölpınarlı',
        'tr.transliteration' => 'Türkçe Transliterasyon',
        'en.sahih'           => 'Sahih Intl (EN)',
        'en.yusufali'        => 'Yusuf Ali (EN)',
        'en.pickthall'       => 'Pickthall (EN)',
        'en.arberry'         => 'Arberry (EN)',
        'ar.muyassar'        => 'Müyesser (AR)',
        'ar.jalalayn'        => 'Celaleyn (AR)',
    ];

    // ── Livewire state ─────────────────────────────────────────────────
    public ?int   $selectedSura    = null;
    public int    $prevSura        = 0;
    public int    $nextSura        = 0;
    public string $selectedMealKey = 'tr.diyanet';

    // ── Mount ──────────────────────────────────────────────────────────
    public function mount(): void
    {
        $user    = auth()->user();
        $setting = $user?->setting;

        // URL param → last read position → sura 1
        $suraParam = request()->query('sura');
        if ($suraParam && isset(self::$SURA_NAMES[(int) $suraParam])) {
            $this->selectedSura = (int) $suraParam;
        } elseif ($setting?->last_read_sura) {
            $this->selectedSura = $setting->last_read_sura;
        } else {
            $this->selectedSura = 1;
        }

        // User's first preferred meal key
        $preferred = UserMealPreference::where('user_id', $user?->id)
            ->whereIn('meal_key', array_keys(self::$MEAL_KEY_LABELS))
            ->value('meal_key');
        if ($preferred) {
            $this->selectedMealKey = $preferred;
        }

        $this->syncNavSuras();
    }

    // ── Navigation ─────────────────────────────────────────────────────
    public function goToPrevSura(): void
    {
        if ($this->prevSura) {
            $this->selectedSura = $this->prevSura;
            $this->syncNavSuras();
        }
    }

    public function goToNextSura(): void
    {
        if ($this->nextSura) {
            $this->selectedSura = $this->nextSura;
            $this->syncNavSuras();
        }
    }

    public function updatedSelectedSura(): void
    {
        $this->syncNavSuras();
    }

    // ── Save position (called from Alpine IntersectionObserver) ────────
    public function saveReadingPosition(int $aya): void
    {
        $user = auth()->user();
        if (! $user || ! $this->selectedSura) {
            return;
        }

        UserSetting::updateOrCreate(
            ['user_id' => $user->id],
            ['last_read_sura' => $this->selectedSura, 'last_read_aya' => $aya]
        );
    }

    // ── Computed: aya texts ───────────────────────────────────────────
    public function getAyaTextsProperty(): Collection
    {
        return QuranWord::where('sura', $this->selectedSura)
            ->where('char_type', 'word')
            ->orderBy('aya')
            ->orderBy('position')
            ->get(['aya', 'text'])
            ->groupBy('aya')
            ->map(fn (Collection $words) => $words->pluck('text')->filter()->implode(' '));
    }

    // ── Computed: translations ────────────────────────────────────────
    public function getTranslationTextsProperty(): Collection
    {
        return VerseTranslation::where('sura', $this->selectedSura)
            ->where('meal_key', $this->selectedMealKey)
            ->orderBy('aya')
            ->pluck('text', 'aya');
    }

    // ── Computed: note counts per aya ─────────────────────────────────
    public function getNoteCountsProperty(): Collection
    {
        $user = auth()->user();

        return ResearchNote::where('user_id', $user?->id)
            ->where('sura', $this->selectedSura)
            ->selectRaw('aya, COUNT(*) as cnt')
            ->groupBy('aya')
            ->pluck('cnt', 'aya');
    }

    // ── Computed: available meal keys ─────────────────────────────────
    public function getMealKeysProperty(): array
    {
        $user = auth()->user();

        // User's configured preferred meals first
        $preferred = UserMealPreference::where('user_id', $user?->id)
            ->pluck('meal_key')
            ->filter(fn ($k) => isset(self::$MEAL_KEY_LABELS[$k]))
            ->values()
            ->all();

        if (! empty($preferred)) {
            return $preferred;
        }

        // Default curated list
        return ['tr.diyanet', 'tr.yazir', 'tr.ates', 'tr.bulac', 'tr.vakfi', 'en.sahih'];
    }

    // ── Computed: sura info ───────────────────────────────────────────
    public function getCurrentSuraTurkishNameProperty(): string
    {
        return self::$SURA_NAMES[$this->selectedSura] ?? "Sure {$this->selectedSura}";
    }

    public function getCurrentSuraArabicNameProperty(): string
    {
        return self::$SURA_ARABIC_NAMES[$this->selectedSura] ?? '';
    }

    public function getMealKeyLabelProperty(): string
    {
        return self::$MEAL_KEY_LABELS[$this->selectedMealKey] ?? $this->selectedMealKey;
    }

    public static function getMealLabel(string $key): string
    {
        return self::$MEAL_KEY_LABELS[$key] ?? $key;
    }

    // ── Render ────────────────────────────────────────────────────────
    public function render(): View
    {
        return view('livewire.quran-read-page');
    }

    // ── Private ───────────────────────────────────────────────────────
    private function syncNavSuras(): void
    {
        $this->prevSura = ($this->selectedSura > 1) ? $this->selectedSura - 1 : 0;
        $this->nextSura = ($this->selectedSura < 114) ? $this->selectedSura + 1 : 0;
    }
}
