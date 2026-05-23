@extends('layouts.user-dashboard')

@section('title', __('Overview'))

@push('head')
<style>
  /* ── Hero ─────────────────────────────────────────────────────── */
  .db-hero {
    background: linear-gradient(135deg, #1a6b5a 0%, #0f4a3d 100%);
    border-radius: 16px;
    padding: 0;
    color: #fff;
    position: relative;
    overflow: hidden;
  }
  /* Subtle Arabic watermark — does NOT overlap text */
  .db-hero::before {
    content: '﷽';
    position: absolute;
    left: -30px; bottom: -40px;
    font-family: 'Amiri', serif;
    font-size: 220px;
    color: rgba(255,255,255,0.035);
    pointer-events: none;
    line-height: 1;
    z-index: 0;
  }

  /* All direct children sit above watermark */
  .db-hero > * { position: relative; z-index: 1; }

  /* Top meta bar */
  .db-hero-meta {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.9rem 1.5rem 0.75rem;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    flex-wrap: wrap; gap: 8px;
  }
  .db-hero-meta-left { display: flex; align-items: center; gap: 10px; }
  .db-hero-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: 'Cairo', sans-serif; font-size: 11px; font-weight: 700;
    letter-spacing: 1px; text-transform: uppercase;
    background: rgba(212,168,67,0.2); border: 1px solid rgba(212,168,67,0.4);
    color: var(--gold-mid); border-radius: 999px; padding: 3px 10px;
  }
  .db-hero-date {
    font-family: 'Cairo', sans-serif; font-size: 12px;
    color: rgba(255,255,255,0.5);
  }
  .db-hero-link {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: 'Cairo', sans-serif; font-size: 12px;
    color: rgba(255,255,255,0.65);
    text-decoration: none;
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 8px; padding: 4px 10px;
    transition: background .15s, color .15s;
  }
  .db-hero-link:hover { background: rgba(255,255,255,0.1); color: #fff; }

  /* Arabic verse */
  .db-hero-body { padding: 1.25rem 1.5rem 0.5rem; }
  .db-arabic {
    font-family: 'Amiri', 'Scheherazade New', 'Noto Naskh Arabic', 'Times New Roman', serif;
    font-size: 28px;
    line-height: 2.1;
    text-align: right;
    direction: rtl;
    color: #fff;
    text-shadow: 0 1px 8px rgba(0,0,0,0.25);
    letter-spacing: 0.01em;
  }

  /* Divider */
  .db-hero-divider {
    width: 80px; height: 2px;
    background: linear-gradient(90deg, transparent, var(--gold-mid), transparent);
    margin: 0.9rem auto 0.75rem;
    border-radius: 2px;
  }

  /* Translation */
  .db-turkish {
    font-family: 'Lora', Georgia, serif;
    font-size: 14px;
    color: rgba(255,255,255,0.92);
    line-height: 1.8;
    text-align: center;
    font-style: italic;
  }

  /* Footer ref */
  .db-hero-footer {
    display: flex; align-items: center; justify-content: flex-end;
    padding: 0.6rem 1.5rem 1rem;
    gap: 6px;
  }
  .db-ref {
    font-family: 'Cairo', sans-serif; font-size: 12px;
    color: var(--gold-mid);
    display: inline-flex; align-items: center; gap: 5px;
  }
  .db-ref-sura { font-weight: 700; }
  .db-ref-sep  { opacity: 0.4; }

  /* ── Stats ────────────────────────────────────────────────────── */
  .db-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; }
  .db-stat  { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 1rem 1.1rem; display: flex; flex-direction: column; gap: 4px; }
  .db-stat-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 4px; }
  .db-stat-icon.teal   { background: var(--teal-light); color: var(--teal-dark); }
  .db-stat-icon.gold   { background: var(--gold-pale);  color: var(--gold); }
  .db-stat-icon.purple { background: #f3f0ff; color: #7c3aed; }
  .db-stat-icon.blue   { background: #eff6ff; color: #2563eb; }
  .db-stat-value { font-family: 'Cairo', sans-serif; font-size: 26px; font-weight: 700; color: var(--text-dark); line-height: 1; }
  .db-stat-label { font-family: 'Cairo', sans-serif; font-size: 11.5px; color: var(--text-light); }

  /* ── Grid layouts ─────────────────────────────────────────────── */
  .db-row-2-3  { display: grid; grid-template-columns: 1.5fr 1fr; gap: 12px; }
  .db-row-half { display: grid; grid-template-columns: 1fr 1fr;  gap: 12px; }

  /* ── Card ─────────────────────────────────────────────────────── */
  .db-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 1.1rem 1.25rem; }
  .db-card-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 0.85rem;
  }
  .db-card-title { font-family: 'Cairo', sans-serif; font-size: 13.5px; font-weight: 600; color: var(--text-dark); display: flex; align-items: center; gap: 7px; }
  .db-card-title i { font-size: 16px; color: var(--teal-mid); }
  .db-card-link { font-family: 'Cairo', sans-serif; font-size: 12px; color: var(--teal-mid); text-decoration: none; }
  .db-card-link:hover { text-decoration: underline; }

  /* ── Sura list ────────────────────────────────────────────────── */
  .db-sura-list { display: flex; flex-direction: column; gap: 6px; }
  .db-sura-item {
    display: flex; align-items: center; gap: 11px;
    padding: 8px 10px; border-radius: 10px;
    background: var(--cream); border: 1px solid var(--border);
    text-decoration: none; color: var(--text-dark);
    transition: background 0.13s, border-color 0.13s;
  }
  .db-sura-item:hover { background: var(--teal-light); border-color: rgba(45,155,132,0.2); }
  .db-sura-num {
    width: 30px; height: 30px; border-radius: 50%;
    background: var(--teal-dark); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Cairo', sans-serif; font-size: 11.5px; font-weight: 600;
    flex-shrink: 0;
  }
  .db-sura-name { font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 500; flex: 1; }
  .db-sura-count {
    font-family: 'Cairo', sans-serif; font-size: 11px;
    color: var(--text-light); background: var(--cream2);
    border: 1px solid var(--border); border-radius: 20px;
    padding: 1px 8px;
  }

  /* ── Notes list ───────────────────────────────────────────────── */
  .db-note-list { display: flex; flex-direction: column; gap: 7px; }
  .db-note-item {
    display: flex; align-items: flex-start; gap: 9px;
    padding: 8px 10px; border-radius: 10px;
    background: var(--cream); border: 1px solid var(--border);
    text-decoration: none; color: var(--text-dark);
    transition: background 0.13s;
  }
  .db-note-item:hover { background: var(--teal-light); }
  .db-note-dot {
    width: 8px; height: 8px; border-radius: 50%; margin-top: 4px; flex-shrink: 0;
  }
  .db-note-dot.note     { background: #3b82f6; }
  .db-note-dot.footnote { background: #f59e0b; }
  .db-note-dot.research { background: #8b5cf6; }
  .db-note-body { flex: 1; min-width: 0; }
  .db-note-title { font-family: 'Cairo', sans-serif; font-size: 12.5px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .db-note-ref   { font-family: 'Cairo', sans-serif; font-size: 11px; color: var(--text-light); }

  /* ── Tags ─────────────────────────────────────────────────────── */
  .db-tag-cloud { display: flex; flex-wrap: wrap; gap: 7px; }
  .db-tag {
    font-family: 'Cairo', sans-serif; padding: 5px 13px;
    border-radius: 20px; font-size: 12px;
    border: 1px solid var(--border-strong);
    background: var(--teal-light); color: var(--teal-dark);
    text-decoration: none; transition: background 0.13s;
    cursor: pointer;
  }
  .db-tag:hover { background: rgba(45,155,132,0.2); }
  .db-tag-count { opacity: 0.65; margin-left: 4px; font-size: 11px; }

  /* ── Last read ────────────────────────────────────────────────── */
  .db-resume {
    display: flex; align-items: center; gap: 14px;
    padding: 0.9rem 1.1rem;
    background: var(--teal-light); border: 1px solid rgba(45,155,132,0.2);
    border-radius: 12px; text-decoration: none; color: var(--text-dark);
    transition: background 0.13s;
  }
  .db-resume:hover { background: rgba(45,155,132,0.18); }
  .db-resume-icon { font-size: 26px; color: var(--teal-mid); }
  .db-resume-label { font-family: 'Cairo', sans-serif; font-size: 11px; color: var(--text-light); }
  .db-resume-val   { font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 600; color: var(--teal-dark); }
  .db-resume-arrow { margin-left: auto; font-size: 18px; color: var(--teal-mid); }

  /* ── Empty state ──────────────────────────────────────────────── */
  .db-empty { font-family: 'Cairo', sans-serif; font-size: 12.5px; color: var(--text-light); text-align: center; padding: 1rem 0; }

  @media (max-width: 1024px) {
    .db-stats, .db-row-2-3, .db-row-half { grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('content')
@php
  $user       = auth()->user();
  $setting    = $user?->setting;

  /* ── Stats ────────────────────────────────────────────────── */
  $noteCount  = \App\Models\ResearchNote::where('user_id', $user?->id)->count();
  $tagCount   = \App\Models\ResearchTag::where('user_id', $user?->id)->count();

  /* ── Recent suras (distinct suras from user's notes, latest first) ── */
  $recentSuraNumbers = \App\Models\ResearchNote::where('user_id', $user?->id)
      ->select('sura')
      ->distinct()
      ->orderByRaw('MAX(updated_at) DESC')
      ->groupBy('sura')
      ->take(5)
      ->pluck('sura');

  /* ── Recent notes ─────────────────────────────────────────── */
  $recentNotes = \App\Models\ResearchNote::where('user_id', $user?->id)
      ->orderByDesc('updated_at')
      ->take(4)
      ->get(['id','sura','aya','title','content','type']);

  /* ── Popular tags ─────────────────────────────────────────── */
  $popularTags = \App\Models\ResearchTag::withCount('notes')
      ->where('user_id', $user?->id)
      ->orderByDesc('notes_count')
      ->take(10)
      ->get(['id','name']);

  /* Sura name helper */
  $suraNames = [
    1=>'Fâtiha',2=>'Bakara',3=>'Âl-i İmrân',4=>'Nisâ',5=>'Mâide',
    6=>'Enâm',7=>'Arâf',8=>'Enfâl',9=>'Tevbe',10=>'Yûnus',
    11=>'Hûd',12=>'Yûsuf',13=>'Raʿd',14=>'İbrâhim',15=>'Hicr',
    16=>'Nahl',17=>'İsrâ',18=>'Kehf',19=>'Meryem',20=>'Tâhâ',
    21=>'Enbiyâ',22=>'Hac',23=>"Mü'minûn",24=>'Nûr',25=>'Furkân',
    26=>'Şuarâ',27=>'Neml',28=>'Kasas',29=>'Ankebût',30=>'Rûm',
    31=>'Lokmân',32=>'Secde',33=>'Ahzâb',34=>"Sebe'",35=>'Fâtır',
    36=>'Yâsîn',37=>'Sâffât',38=>'Sâd',39=>'Zümer',40=>"Mü'min",
    41=>'Fussilet',42=>'Şûrâ',43=>'Zuhruf',44=>'Duhân',45=>'Câsiye',
    46=>'Ahkâf',47=>'Muhammed',48=>'Feth',49=>'Hucurât',50=>'Kâf',
    51=>'Zâriyât',52=>'Tûr',53=>'Necm',54=>'Kamer',55=>'Rahmân',
    56=>'Vâkıa',57=>'Hadîd',58=>'Mücâdele',59=>'Haşr',60=>'Mümtehine',
    61=>'Saf',62=>"Cum'a",63=>'Münâfikûn',64=>'Teğâbün',65=>'Talâk',
    66=>'Tahrîm',67=>'Mülk',68=>'Kalem',69=>'Hâkka',70=>'Meâric',
    71=>'Nûh',72=>'Cin',73=>'Müzzemmil',74=>'Müddessir',75=>'Kıyâme',
    76=>'İnsân',77=>'Mürselât',78=>"Nebe'",79=>'Nâziât',80=>'Abese',
    81=>'Tekvîr',82=>'İnfitâr',83=>'Mutaffifîn',84=>'İnşikâk',85=>'Bürûc',
    86=>'Târık',87=>"A'lâ",88=>'Ğâşiye',89=>'Fecr',90=>'Beled',
    91=>'Şems',92=>'Leyl',93=>'Duhâ',94=>'İnşirâh',95=>'Tîn',
    96=>'Alak',97=>'Kadr',98=>'Beyyine',99=>'Zilzâl',100=>'Âdiyât',
    101=>'Kâria',102=>'Tekâsür',103=>'Asr',104=>'Hümeze',105=>'Fîl',
    106=>'Kureyş',107=>'Mâûn',108=>'Kevser',109=>'Kâfirûn',110=>'Nasr',
    111=>'Tebbet',112=>'İhlâs',113=>'Felak',114=>'Nâs',
  ];

  /* Note count per sura for badge */
  $notesPerSura = \App\Models\ResearchNote::where('user_id', $user?->id)
      ->whereIn('sura', $recentSuraNumbers)
      ->selectRaw('sura, COUNT(*) as cnt')
      ->groupBy('sura')
      ->pluck('cnt', 'sura');

  /* ── Günün Ayeti ──────────────────────────────────────────── */
  $dailyVerse    = null;
  $dailyArabic   = '';
  $dailySuraName = '';

  try {
      // Kullanıcının meal tercihini al; yoksa tr.diyanet kullan
      $preferredLang = $setting?->preferred_language ?: 'tr';
      $preferredMeal = \App\Models\UserMealPreference::where('user_id', $user?->id)
          ->where('language', $preferredLang)
          ->value('meal_key');
      // Tercih yoksa veya bulunan key verse_translations'ta yoksa fallback
      if (!$preferredMeal || !\App\Models\VerseTranslation::where('meal_key', $preferredMeal)->exists()) {
          $preferredMeal = 'tr.diyanet';
      }

      $totalVerses = \App\Models\VerseTranslation::where('meal_key', $preferredMeal)->count();

      if ($totalVerses > 0) {
          // Seed: her gün farklı, gün içinde sabit
          $seed   = (int) date('z') + ((int) date('Y') * 366);
          $offset = $seed % $totalVerses;

          $dailyVerse = \App\Models\VerseTranslation::where('meal_key', $preferredMeal)
              ->orderBy('sura')->orderBy('aya')
              ->offset($offset)->limit(1)
              ->first(['sura', 'aya', 'text']);

          if ($dailyVerse) {
              $dailyArabic = \App\Models\QuranWord::where('sura', $dailyVerse->sura)
                  ->where('aya', $dailyVerse->aya)
                  ->where('char_type', 'word')
                  ->orderBy('position')
                  ->pluck('text')->filter()->implode(' ');

              $dailySuraName = $suraNames[$dailyVerse->sura] ?? "Sure {$dailyVerse->sura}";
          }
      }
  } catch (\Throwable $e) {
      // Hata durumunda hero gizlenir; log kaydı yapılır
      \Illuminate\Support\Facades\Log::warning('Günün Ayeti yüklenemedi: ' . $e->getMessage());
  }
@endphp

{{-- ── Günün Ayeti ──────────────────────────────────────────────── --}}
<div class="db-hero">

  {{-- Üst meta bar --}}
  <div class="db-hero-meta">
    <div class="db-hero-meta-left">
      <span class="db-hero-badge">
        <i class="ti ti-moon-stars" style="font-size:11px;"></i>
        {{ __('Verse of the Day') }}
      </span>
      <span class="db-hero-date">
        {{ \Carbon\Carbon::now()->locale(app()->getLocale())->isoFormat('D MMMM Y') }}
      </span>
    </div>
    @if($dailyVerse)
    <a
      href="{{ route('user.quran-text') }}?sura={{ $dailyVerse->sura }}&aya={{ $dailyVerse->aya }}"
      class="db-hero-link"
    >
      {{ __('Open in reading page') }} <i class="ti ti-arrow-right" style="font-size:12px;"></i>
    </a>
    @endif
  </div>

  {{-- Ayet gövdesi --}}
  <div class="db-hero-body">
    @if($dailyVerse)
      <div class="db-arabic">{{ $dailyArabic }}</div>
      <div class="db-hero-divider"></div>
      <div class="db-turkish">{{ $dailyVerse->text }}</div>
    @else
      <div class="db-arabic" style="font-size:18px;opacity:.6;text-align:center;direction:ltr;">
        بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
      </div>
      <div class="db-hero-divider"></div>
      <div class="db-turkish" style="opacity:.7;">{{ __('basmala_translation') }}</div>
    @endif
  </div>

  {{-- Sure : Ayet referansı --}}
  <div class="db-hero-footer">
    @if($dailyVerse)
    <span class="db-ref">
      <i class="ti ti-book-2" style="font-size:12px;opacity:.6;"></i>
      <span class="db-ref-sura">{{ $dailySuraName }}</span>
      <span class="db-ref-sep">·</span>
      {{ __('verse_ref', ['number' => $dailyVerse->aya]) }}
    </span>
    @else
    <span class="db-ref" style="opacity:.5;">
      <i class="ti ti-book-2" style="font-size:12px;"></i>
      Fâtiha · {{ __('verse_ref', ['number' => 1]) }}
    </span>
    @endif
  </div>

</div>

{{-- ── Stats ─────────────────────────────────────────────────────── --}}
<div class="db-stats">
  <div class="db-stat">
    <div class="db-stat-icon teal"><i class="ti ti-book-2"></i></div>
    <div class="db-stat-value">114</div>
    <div class="db-stat-label">{{ __('Total Suras') }}</div>
  </div>
  <div class="db-stat">
    <div class="db-stat-icon gold"><i class="ti ti-notes"></i></div>
    <div class="db-stat-value">{{ $noteCount }}</div>
    <div class="db-stat-label">{{ __('Research Notes') }}</div>
  </div>
  <div class="db-stat">
    <div class="db-stat-icon purple"><i class="ti ti-tag"></i></div>
    <div class="db-stat-value">{{ $tagCount }}</div>
    <div class="db-stat-label">{{ __('Tags') }}</div>
  </div>
  <div class="db-stat">
    <div class="db-stat-icon blue"><i class="ti ti-books"></i></div>
    <div class="db-stat-value">{{ $recentSuraNumbers->count() }}</div>
    <div class="db-stat-label">{{ __('Studied Suras') }}</div>
  </div>
</div>

{{-- ── Resume reading ─────────────────────────────────────────────── --}}
@if($setting?->last_read_sura && $setting?->last_read_aya)
<a class="db-resume"
   href="{{ route('user.quran-text') }}?sura={{ $setting->last_read_sura }}&aya={{ $setting->last_read_aya }}">
  <i class="ti ti-player-play db-resume-icon"></i>
  <div>
    <div class="db-resume-label">{{ __('Continue where you left off') }}</div>
    <div class="db-resume-val">
      {{ $suraNames[$setting->last_read_sura] ?? (__('Sura prefix') . " {$setting->last_read_sura}") }}
      — {{ __('verse_ref', ['number' => $setting->last_read_aya]) }}
    </div>
  </div>
  <i class="ti ti-arrow-right db-resume-arrow"></i>
</a>
@endif

{{-- ── Main grid ───────────────────────────────────────────────────── --}}
<div class="db-row-2-3">

  {{-- Son çalışılan sureler --}}
  <div class="db-card">
    <div class="db-card-head">
      <div class="db-card-title"><i class="ti ti-history"></i> {{ __('Recently Studied Suras') }}</div>
      <a class="db-card-link" href="{{ route('user.quran-text') }}">{{ __('See all →') }}</a>
    </div>
    <div class="db-sura-list">
      @forelse($recentSuraNumbers as $suraNum)
        <a class="db-sura-item"
           href="{{ route('user.quran-text') }}?sura={{ $suraNum }}&aya=1">
          <div class="db-sura-num">{{ $suraNum }}</div>
          <div class="db-sura-name">{{ $suraNames[$suraNum] ?? (__('Sura prefix') . " {$suraNum}") }}</div>
          @if(($notesPerSura[$suraNum] ?? 0) > 0)
            <span class="db-sura-count">{{ $notesPerSura[$suraNum] }} {{ __('note') }}</span>
          @endif
        </a>
      @empty
        <div class="db-empty">{{ __('No suras with notes yet.') }}</div>
      @endforelse
    </div>
  </div>

  {{-- Etiketler --}}
  <div class="db-card">
    <div class="db-card-head">
      <div class="db-card-title"><i class="ti ti-tag"></i> {{ __('Tags') }}</div>
      <a class="db-card-link" href="{{ route('user.quran-notes-range') }}">{{ __('Go to notes →') }}</a>
    </div>
    @if($popularTags->isNotEmpty())
      <div class="db-tag-cloud">
        @foreach($popularTags as $tag)
          <a class="db-tag"
             href="{{ route('user.quran-notes-range') }}?tag={{ $tag->id }}">
            {{ $tag->name }}
            @if($tag->notes_count > 0)
              <span class="db-tag-count">{{ $tag->notes_count }}</span>
            @endif
          </a>
        @endforeach
      </div>
    @else
      <div class="db-empty">{{ __('No tags created yet.') }}</div>
    @endif
  </div>

</div>

{{-- ── Recent notes ────────────────────────────────────────────────── --}}
@if($recentNotes->isNotEmpty())
<div class="db-card">
  <div class="db-card-head">
    <div class="db-card-title"><i class="ti ti-clock"></i> {{ __('Recent Notes') }}</div>
    <a class="db-card-link" href="{{ route('user.quran-notes-range') }}">{{ __('View all →') }}</a>
  </div>
  <div class="db-note-list">
    @foreach($recentNotes as $note)
      @php
        $label = $note->title
            ?: \Illuminate\Support\Str::limit(strip_tags($note->content ?? ''), 60);
      @endphp
      <a class="db-note-item"
         href="{{ route('user.quran-text') }}?sura={{ $note->sura }}&aya={{ $note->aya }}">
        <span class="db-note-dot {{ $note->type }}"></span>
        <div class="db-note-body">
          <div class="db-note-title">{{ $label ?: __('Untitled note') }}</div>
          <div class="db-note-ref">
            {{ $suraNames[$note->sura] ?? (__('Sura prefix') . " {$note->sura}") }}
            – {{ __('verse_ref', ['number' => $note->aya]) }}
          </div>
        </div>
        <i class="ti ti-arrow-right" style="font-size:14px;color:var(--text-light);flex-shrink:0;margin-top:2px;"></i>
      </a>
    @endforeach
  </div>
</div>
@endif

@endsection
