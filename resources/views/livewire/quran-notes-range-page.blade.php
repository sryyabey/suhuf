<div x-data="{ expandedAyas: {} }" class="qnr">
<style>
/* ── Temel ─────────────────────────────────────────── */
.qnr { max-width: 1100px; margin: 0 auto; display: flex; flex-direction: column; gap: 16px; }

/* ── Kart ───────────────────────────────────────────── */
.qnr-card { background: #fff; border: 1px solid var(--border-strong); border-radius: 14px; padding: 20px; }

/* ── Sayfa başlığı ──────────────────────────────────── */
.qnr-page-title { font-family: 'Cairo', sans-serif; font-size: 22px; font-weight: 600; color: var(--teal-dark); margin: 0; }
.qnr-page-sub   { font-size: 12px; color: var(--text-light); margin: 3px 0 0; }

/* ── Filtre paneli ──────────────────────────────────── */
.qnr-filter-grid  { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 14px; }
.qnr-filter-label { font-family: 'Cairo', sans-serif; font-size: 11px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: .5px; display: block; margin-bottom: 5px; }
.qnr-select       { width: 100%; padding: 9px 30px 9px 12px; border: 1px solid var(--border-strong); border-radius: 9px; font-size: 13px; font-family: 'Cairo', sans-serif; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238a7a60' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 10px center; color: var(--text-dark); appearance: none; cursor: pointer; }
.qnr-select:focus { outline: none; border-color: var(--teal-mid); box-shadow: 0 0 0 3px rgba(45,155,132,.12); }
.qnr-range-arrow  { display: flex; align-items: flex-end; padding-bottom: 10px; color: var(--text-light); font-size: 20px; justify-content: center; }

.qnr-filter-row   { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 14px; }
.qnr-filter-divider { width: 1px; height: 22px; background: var(--border-strong); flex-shrink: 0; }

.qnr-type-btns    { display: flex; gap: 6px; }
.qnr-type-btn     { padding: 6px 12px; border-radius: 7px; border: 1px solid var(--border-strong); background: #fff; font-family: 'Cairo', sans-serif; font-size: 12px; font-weight: 600; color: var(--text-mid); cursor: pointer; transition: all .12s; }
.qnr-type-btn.active    { background: var(--teal-dark); color: #fff; border-color: var(--teal-dark); }
.qnr-type-btn.t-note    { }
.qnr-type-btn.t-footnote { }
.qnr-type-btn.t-research { }

.qnr-tag-select   { padding: 6px 30px 6px 10px; border: 1px solid var(--border-strong); border-radius: 7px; font-size: 12px; font-family: 'Cairo', sans-serif; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238a7a60' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 8px center; color: var(--text-dark); appearance: none; cursor: pointer; }

.qnr-actions-row  { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.qnr-btn          { display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; border-radius: 9px; border: 1px solid var(--border-strong); background: #fff; color: var(--text-mid); font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; transition: all .15s; white-space: nowrap; }
.qnr-btn:hover    { background: var(--cream2); color: var(--text-dark); }
.qnr-btn.primary  { background: var(--teal-dark); color: #fff; border-color: var(--teal-dark); }
.qnr-btn.primary:hover { background: #145246; }
.qnr-btn:disabled { opacity: .45; cursor: not-allowed; }

.qnr-range-invalid { font-family: 'Cairo', sans-serif; font-size: 12px; color: #b42318; display: flex; align-items: center; gap: 5px; }
.qnr-result-summary { font-family: 'Cairo', sans-serif; font-size: 13px; color: var(--text-light); }
.qnr-result-count   { font-weight: 700; color: var(--teal-dark); }

/* ── Sure bölümü ────────────────────────────────────── */
.qnr-sura-section { }
.qnr-sura-header  { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
.qnr-sura-divider { flex: 1; height: 1px; background: linear-gradient(to right, var(--teal-mid), transparent); }
.qnr-sura-name    { font-family: 'Cairo', sans-serif; font-size: 17px; font-weight: 700; color: var(--teal-dark); white-space: nowrap; }
.qnr-sura-num     { font-family: 'Cairo', sans-serif; font-size: 12px; color: var(--text-light); background: var(--cream2); border: 1px solid var(--border-strong); border-radius: 6px; padding: 2px 8px; white-space: nowrap; }
.qnr-sura-count   { font-family: 'Cairo', sans-serif; font-size: 12px; color: var(--text-light); white-space: nowrap; }

/* ── Ayet bloğu ─────────────────────────────────────── */
.qnr-aya-block    { margin-bottom: 20px; }
.qnr-aya-header   { display: flex; align-items: flex-start; gap: 14px; background: var(--gold-pale); border: 1px solid var(--border); border-radius: 10px 10px 0 0; padding: 12px 16px; border-bottom: none; cursor: pointer; transition: background .12s; }
.qnr-aya-header:hover { background: var(--gold-light); }
.qnr-aya-ref      { font-family: 'Cairo', sans-serif; font-size: 12px; font-weight: 700; color: var(--text-light); background: #fff; border: 1px solid var(--border-strong); border-radius: 6px; padding: 3px 8px; white-space: nowrap; flex-shrink: 0; margin-top: 4px; }
.qnr-aya-arabic   { font-family: 'Amiri', serif; font-size: 22px; line-height: 1.9; color: var(--text-dark); direction: rtl; text-align: right; flex: 1; }
.qnr-aya-marker   { color: var(--gold); }
.qnr-aya-note-count { font-family: 'Cairo', sans-serif; font-size: 11px; color: var(--teal-dark); background: var(--teal-light); border: 1px solid rgba(45,155,132,.2); border-radius: 999px; padding: 2px 8px; white-space: nowrap; flex-shrink: 0; margin-top: 6px; }

/* ── Not kartları ızgarası ──────────────────────────── */
.qnr-notes-grid   { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; border: 1px solid var(--border); border-radius: 0 0 10px 10px; padding: 12px; background: #fdfcf9; }
.qnr-note-card    { background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 14px 15px; display: flex; flex-direction: column; gap: 8px; transition: box-shadow .12s; }
.qnr-note-card:hover { box-shadow: 0 2px 10px rgba(26,107,90,.08); }

.qnr-note-top     { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.qnr-type-badge   { font-family: 'Cairo', sans-serif; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 5px; letter-spacing: .4px; flex-shrink: 0; }
.qnr-type-note     { background: var(--teal-light); color: var(--teal-dark); }
.qnr-type-footnote { background: var(--gold-light); color: #7a5500; }
.qnr-type-research { background: #ede9fe; color: #5b21b6; }
.qnr-note-title   { font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 700; color: var(--text-dark); flex: 1; }

.qnr-word-chip    { display: inline-flex; flex-direction: column; gap: 2px; }
.qnr-word-chip-label { font-family: 'Cairo', sans-serif; font-size: 10px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: .5px; }
.qnr-word-ar      { font-family: 'Amiri', serif; font-size: 22px; direction: rtl; color: var(--teal-dark); background: var(--teal-light); border: 1px solid rgba(45,155,132,.22); border-radius: 8px; padding: 2px 14px; line-height: 1.7; display: inline-block; }

.qnr-note-body    { font-family: 'Lora', serif; font-size: 13px; line-height: 1.75; color: var(--text-dark); white-space: pre-wrap; flex: 1; }
.qnr-note-footer  { display: flex; justify-content: space-between; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 2px; padding-top: 8px; border-top: 1px solid var(--border); }
.qnr-tags         { display: flex; gap: 4px; flex-wrap: wrap; }
.qnr-tag          { padding: 2px 8px; border-radius: 999px; font-family: 'Cairo', sans-serif; font-size: 11px; border: 1px solid var(--border-strong); background: #fff; color: var(--text-mid); }
.qnr-note-date    { font-family: 'Cairo', sans-serif; font-size: 11px; color: var(--text-light); white-space: nowrap; }

/* ── Boş durumlar ───────────────────────────────────── */
.qnr-empty        { text-align: center; padding: 48px 20px; color: var(--text-light); }
.qnr-empty-icon   { font-size: 42px; display: block; margin-bottom: 12px; }
.qnr-empty-title  { font-family: 'Cairo', sans-serif; font-size: 16px; font-weight: 600; color: var(--text-mid); margin-bottom: 6px; }
.qnr-empty-sub    { font-size: 13px; }

/* ── Responsive ─────────────────────────────────────── */
@media (max-width: 820px) {
    .qnr-filter-grid  { grid-template-columns: 1fr 1fr; }
    .qnr-range-arrow  { display: none; }
}
@media (max-width: 580px) {
    .qnr-filter-grid  { grid-template-columns: 1fr; }
    .qnr-notes-grid   { grid-template-columns: 1fr; }
    .qnr-aya-arabic   { font-size: 18px; }
}

/* ── Yazdır butonu ──────────────────────────────────── */
.qnr-print-btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 9px; border: 1px solid var(--border-strong); background: #fff; color: var(--text-mid); font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; transition: all .15s; }
.qnr-print-btn:hover { background: var(--cream2); color: var(--text-dark); }

/* ── Sadece baskıda görünen başlık ──────────────────── */
.qnr-print-header { display: none; }

/* ── Baskı stilleri ─────────────────────────────────── */
@media print {
    /* Layout resetle */
    html, body         { overflow: visible !important; height: auto !important; }
    .app               { display: block !important; height: auto !important; }
    .topbar, .sidebar  { display: none !important; }
    .main              { overflow: visible !important; padding: 10mm 14mm !important; background: #fff !important; }

    /* Ekrana özel elemanları gizle */
    .qnr-no-print      { display: none !important; }

    /* Baskı başlığını göster */
    .qnr-print-header  { display: block !important; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 2px solid #1a6b5a; }
    .qnr-print-title   { font-family: 'Cairo', sans-serif; font-size: 20px; font-weight: 700; color: #1a6b5a; margin: 0 0 4px; }
    .qnr-print-meta    { font-family: 'Cairo', sans-serif; font-size: 12px; color: #8a7a60; }

    /* İçerik formatı */
    .qnr               { max-width: 100% !important; gap: 8px !important; }
    .qnr-card          { border: none !important; padding: 0 !important; border-radius: 0 !important; box-shadow: none !important; }

    /* Sure bölümleri: yeni sayfada başla (ilk hariç) */
    .qnr-sura-section  { break-before: page; }
    .qnr-sura-section:first-child { break-before: auto; }
    .qnr-sura-header   { border-bottom: 1.5px solid #1a6b5a; padding-bottom: 5px; margin-bottom: 10px !important; }
    .qnr-sura-divider  { background: #1a6b5a !important; }

    /* Ayet blokları */
    .qnr-aya-block     { break-inside: avoid; margin-bottom: 12px !important; }
    .qnr-aya-header    { background: #f9f7f2 !important; border: 1px solid #e6dcc6 !important; border-bottom: none !important; padding: 8px 12px !important; cursor: default !important; }
    .qnr-aya-arabic    { font-size: 19px !important; }

    /* Not kartları ızgarası — Alpine display:none'ı override et */
    .qnr-notes-grid    { display: grid !important; grid-template-columns: repeat(2, 1fr) !important; border: 1px solid #e6dcc6 !important; border-radius: 0 0 6px 6px !important; padding: 8px !important; background: #fff !important; }
    .qnr-note-card     { break-inside: avoid; page-break-inside: avoid; border: 1px solid #e6dcc6 !important; border-radius: 7px !important; box-shadow: none !important; }

    /* Tipografi */
    .qnr-note-title    { font-size: 13px !important; }
    .qnr-note-body     { font-size: 11.5px !important; line-height: 1.65 !important; }
    .qnr-word-ar       { font-size: 18px !important; }
    .qnr-tag           { border: 1px solid #ccc !important; }
    .qnr-note-footer   { border-top: 1px solid #eee !important; }
}
</style>

{{-- Sayfa Başlığı --}}
<div class="qnr-no-print" style="display:flex; justify-content:space-between; align-items:flex-end; gap:10px; flex-wrap:wrap;">
    <div>
        <h1 class="qnr-page-title">Not Araştırması</h1>
        <p class="qnr-page-sub">Sure ve ayet aralığı seçin, notlarınızı tek sayfada görüntüleyin</p>
    </div>
    <a href="{{ route('user.quran-text') }}" style="font-family:'Cairo',sans-serif; font-size:12px; color:var(--text-light); text-decoration:none; display:flex; align-items:center; gap:5px;">
        <i class="ti ti-book"></i> Kur'an Metnine Dön
    </a>
</div>

{{-- Filtre Paneli --}}
<div class="qnr-card qnr-no-print">

    {{-- Sure / Ayet Aralığı --}}
    <div class="qnr-filter-grid">
        <div>
            <label class="qnr-filter-label">Başlangıç Suresi</label>
            <select wire:model.live="startSura" class="qnr-select">
                @foreach($this->suras as $sura)
                    <option value="{{ $sura }}">{{ $sura }}. {{ \App\Livewire\QuranNotesRangePage::getSuraNameStatic($sura) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="qnr-filter-label">Başlangıç Ayeti</label>
            <select wire:model.live="startAya" class="qnr-select">
                @foreach($this->startAyas as $aya)
                    <option value="{{ $aya }}">{{ $aya }}. Ayet</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="qnr-filter-label">Bitiş Suresi</label>
            <select wire:model.live="endSura" class="qnr-select">
                @foreach($this->suras as $sura)
                    <option value="{{ $sura }}">{{ $sura }}. {{ \App\Livewire\QuranNotesRangePage::getSuraNameStatic($sura) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="qnr-filter-label">Bitiş Ayeti</label>
            <select wire:model.live="endAya" class="qnr-select">
                @foreach($this->endAyas as $aya)
                    <option value="{{ $aya }}">{{ $aya }}. Ayet</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Etiket + Tür Filtresi --}}
    <div class="qnr-filter-row">
        <i class="ti ti-filter" style="color:var(--text-light); font-size:15px;"></i>

        <div class="qnr-type-btns">
            <button wire:click="$set('filterType', '')"          type="button" class="qnr-type-btn {{ $filterType === '' ? 'active' : '' }}">Tümü</button>
            <button wire:click="$set('filterType', 'note')"      type="button" class="qnr-type-btn {{ $filterType === 'note' ? 'active' : '' }}">Not</button>
            <button wire:click="$set('filterType', 'footnote')"  type="button" class="qnr-type-btn {{ $filterType === 'footnote' ? 'active' : '' }}">Dipnot</button>
            <button wire:click="$set('filterType', 'research')"  type="button" class="qnr-type-btn {{ $filterType === 'research' ? 'active' : '' }}">Araştırma</button>
        </div>

        @if($this->userTags->isNotEmpty())
            <div class="qnr-filter-divider"></div>
            <select wire:model.live="filterTagId" class="qnr-tag-select">
                <option value="">Tüm Etiketler</option>
                @foreach($this->userTags as $tag)
                    <option value="{{ $tag->id }}">#{{ $tag->name }}</option>
                @endforeach
            </select>
        @endif
    </div>

    {{-- Aksiyon Satırı --}}
    <div class="qnr-actions-row">
        <button
            wire:click="load"
            wire:loading.attr="disabled"
            type="button"
            class="qnr-btn primary"
            @if(! $this->isRangeValid) disabled @endif
        >
            <span wire:loading.remove wire:target="load">
                <i class="ti ti-search"></i> Notları Getir
            </span>
            <span wire:loading wire:target="load">Yükleniyor...</span>
        </button>

        @if(! $this->isRangeValid && $startSura && $startAya && $endSura && $endAya)
            <div class="qnr-range-invalid">
                <i class="ti ti-alert-circle"></i>
                Bitiş, başlangıçtan önce olamaz.
            </div>
        @endif

        @if($loaded)
            <div class="qnr-result-summary">
                <span class="qnr-result-count">{{ $this->totalNoteCount }}</span> not ·
                <span>{{ $startSura }}:{{ $startAya }} → {{ $endSura }}:{{ $endAya }}</span>
            </div>
        @endif
    </div>
</div>

{{-- Sonuçlar --}}
@if($loaded)
    {{-- Baskıda görünen başlık (ekranda gizli) --}}
    <div class="qnr-print-header">
        <h1 class="qnr-print-title">Kur'an Not Araştırması</h1>
        <p class="qnr-print-meta">
            Aralık: {{ $startSura }}:{{ $startAya }} → {{ $endSura }}:{{ $endAya }}
            @if($filterType !== '') · Tür: {{ match($filterType) { 'note' => 'Not', 'footnote' => 'Dipnot', 'research' => 'Araştırma', default => $filterType } }} @endif
            · {{ $this->totalNoteCount }} not
            · {{ now()->format('d.m.Y H:i') }}
        </p>
    </div>

    {{-- Yazdır / Özet satırı (ekranda görünür, baskıda gizli) --}}
    <div class="qnr-no-print" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <div style="font-family:'Cairo',sans-serif; font-size:13px; color:var(--text-light);">
            <span style="font-weight:700; color:var(--teal-dark);">{{ $this->totalNoteCount }}</span> not ·
            {{ \App\Livewire\QuranNotesRangePage::getSuraNameStatic($startSura) }} {{ $startSura }}:{{ $startAya }}
            → {{ \App\Livewire\QuranNotesRangePage::getSuraNameStatic($endSura) }} {{ $endSura }}:{{ $endAya }}
        </div>
        <button type="button" onclick="window.print()" class="qnr-print-btn">
            <i class="ti ti-printer"></i> Yazdır / PDF
        </button>
    </div>

    @if($this->groupedNotes->isEmpty())
        <div class="qnr-card qnr-empty">
            <i class="ti ti-notes-off qnr-empty-icon"></i>
            <div class="qnr-empty-title">Bu aralıkta not bulunamadı</div>
            <p class="qnr-empty-sub">Farklı bir aralık veya filtre deneyin.</p>
        </div>
    @else
        @foreach($this->groupedNotes as $sura => $suraData)
            <div class="qnr-sura-section">

                {{-- Sure Başlığı --}}
                <div class="qnr-sura-header">
                    <span class="qnr-sura-name">{{ $suraData['name'] }}</span>
                    <span class="qnr-sura-num">{{ $sura }}. Sure</span>
                    <div class="qnr-sura-divider"></div>
                    <span class="qnr-sura-count">{{ $suraData['noteCount'] }} not</span>
                </div>

                {{-- Ayet Blokları --}}
                @foreach($suraData['ayas'] as $aya => $ayaData)
                    <div class="qnr-aya-block">

                        {{-- Ayet Başlığı --}}
                        <div
                            class="qnr-aya-header"
                            @click="expandedAyas[{{ $sura }}+'_'+{{ $aya }}] = !(expandedAyas[{{ $sura }}+'_'+{{ $aya }}] ?? true)"
                        >
                            <span class="qnr-aya-ref">{{ $sura }}:{{ $aya }}</span>
                            <div class="qnr-aya-arabic">
                                {{ $ayaData['arabic'] }}
                                <span class="qnr-aya-marker">﴿{{ $aya }}﴾</span>
                            </div>
                            <span class="qnr-aya-note-count">{{ $ayaData['notes']->count() }} not</span>
                        </div>

                        {{-- Not Kartları Izgarası --}}
                        <div
                            class="qnr-notes-grid"
                            x-show="expandedAyas[{{ $sura }}+'_'+{{ $aya }}] ?? true"
                            x-transition
                        >
                            @foreach($ayaData['notes'] as $note)
                                <div class="qnr-note-card">
                                    <div class="qnr-note-top">
                                        <span class="qnr-type-badge qnr-type-{{ $note->type }}">
                                            {{ match($note->type) { 'note' => 'Not', 'footnote' => 'Dipnot', 'research' => 'Araştırma', default => $note->type } }}
                                        </span>
                                        <span class="qnr-note-title">{{ $note->title }}</span>
                                    </div>

                                    @if($note->word_position && ! empty($note->word_text))
                                        <div class="qnr-word-chip">
                                            <span class="qnr-word-chip-label">İlgili Kelime</span>
                                            <span class="qnr-word-ar">{{ $note->word_text }}</span>
                                        </div>
                                    @endif

                                    <div class="qnr-note-body">{{ $note->content }}</div>

                                    <div class="qnr-note-footer">
                                        <div class="qnr-tags">
                                            @foreach($note->tags as $tag)
                                                <span class="qnr-tag">#{{ $tag->name }}</span>
                                            @endforeach
                                        </div>
                                        <span class="qnr-note-date">{{ $note->updated_at->format('d.m.Y') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                @endforeach

            </div>
        @endforeach
    @endif
@endif

</div>
