<div class="qpr">
  <style>
    .qpr { max-width: 860px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }

    /* ── Header ─────────────────────────────────────────────── */
    .qpr-header { display: flex; justify-content: space-between; align-items: flex-end; gap: 10px; flex-wrap: wrap; }
    .qpr-title  { margin: 0; font-family: 'Cairo', sans-serif; font-size: 21px; color: var(--teal-dark); font-weight: 700; }
    .qpr-sub    { margin: 2px 0 0; font-size: 12px; color: var(--text-light); font-family: 'Cairo', sans-serif; }

    /* ── Toolbar ────────────────────────────────────────────── */
    .qpr-toolbar { background:#fff; border:1px solid var(--border-strong); border-radius:14px; padding:12px; display:grid; grid-template-columns:repeat(3,minmax(150px,1fr)); gap:8px; }
    .qpr-field   { display:flex; flex-direction:column; gap:4px; }
    .qpr-label   { font-family:'Cairo',sans-serif; font-size:11px; color:var(--text-light); text-transform:uppercase; letter-spacing:.5px; }
    .qpr-select  { border:1px solid var(--border-strong); border-radius:9px; padding:8px 10px; font-family:'Cairo',sans-serif; font-size:13.5px; background:#fff; color:var(--text-dark); }
    .qpr-select:focus { outline:none; border-color:var(--teal-mid); box-shadow:0 0 0 3px rgba(45,155,132,.12); }

    /* ── Bookmarks ──────────────────────────────────────────── */
    .qpr-bookmarks { background:#fff; border:1px solid var(--border-strong); border-radius:14px; padding:12px; }
    .qpr-bookmarks-head { display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:8px; flex-wrap:wrap; }
    .qpr-bookmarks-title { font-family:'Cairo',sans-serif; font-size:13px; font-weight:700; color:var(--teal-dark); display:flex; align-items:center; gap:6px; }
    .qpr-bookmark-form   { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
    .qpr-bookmark-input  { border:1px solid var(--border-strong); border-radius:8px; padding:7px 9px; font-family:'Cairo',sans-serif; font-size:13px; min-width:170px; color:var(--text-dark); }
    .qpr-bookmark-btn    { border:1px solid var(--teal-mid); background:var(--teal-dark); color:#fff; border-radius:8px; padding:7px 11px; font-family:'Cairo',sans-serif; font-size:12px; cursor:pointer; display:inline-flex; align-items:center; gap:5px; transition:opacity .15s; }
    .qpr-bookmark-btn:hover { opacity:.85; }
    .qpr-bookmark-btn.alt  { background:#fff; color:var(--teal-dark); border-color:var(--border-strong); }
    .qpr-bookmarks-list  { display:flex; flex-wrap:wrap; gap:7px; }
    .qpr-bookmark-pill   { display:inline-flex; align-items:center; gap:5px; border:1px solid var(--border); background:var(--cream2); border-radius:999px; padding:4px 10px; }
    .qpr-bookmark-go     { border:0; background:transparent; color:var(--text-mid); cursor:pointer; font-family:'Cairo',sans-serif; font-size:12px; }
    .qpr-bookmark-del    { border:0; background:transparent; color:#c0392b; cursor:pointer; font-size:14px; line-height:1; padding:0 2px; }

    /* ── Mushaf page ─────────────────────────────────────────── */
    .qpr-page {
      background: linear-gradient(180deg,#fffefb 0%,#fbf7ee 100%);
      border: 1px solid rgba(184,134,11,.26);
      border-radius: 20px;
      padding: 22px 24px;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.7), 0 6px 18px rgba(66,47,11,.08);
    }
    .qpr-page-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; font-family:'Cairo',sans-serif; color:var(--text-light); font-size:12px; border-bottom:1px dashed rgba(184,134,11,.25); padding-bottom:10px; }
    .qpr-page-num  { font-weight:700; color:var(--gold); font-size:14px; }
    .qpr-page-range { font-size:11.5px; }

    /* Arabic text */
    .qpr-mushaf { direction:rtl; display:flex; flex-direction:column; gap:4px; font-family:'Amiri',serif; font-size:32px; line-height:2.2; color:var(--text-dark); }
    .qpr-ayah   { display:block; position:relative; }
    .qpr-ayah-btn {
      width:100%; border:0; background:transparent; padding:6px 8px; margin:0;
      cursor:pointer; color:inherit; font:inherit; line-height:inherit;
      direction:inherit; text-align:right; border-radius:10px;
      transition:background .12s;
      position:relative;
    }
    .qpr-ayah-btn:hover   { background:rgba(45,155,132,.07); }
    .qpr-ayah-btn:focus   { outline:none; background:rgba(45,155,132,.1); box-shadow:0 0 0 2px rgba(45,155,132,.2); }

    /* Note badge — teal pill overlaid top-left (LTR corner = right of RTL text start) */
    .qpr-note-badge {
      position:absolute;
      top:-6px;
      left:6px;
      display:inline-flex;
      align-items:center;
      gap:3px;
      background:var(--teal-dark);
      color:#fff;
      font-family:'Cairo',sans-serif;
      font-size:10px;
      font-weight:700;
      padding:1px 7px;
      border-radius:999px;
      box-shadow:0 1px 4px rgba(0,0,0,.2);
      z-index:1;
      pointer-events:none;
    }
    .qpr-note-badge i { font-size:10px; }

    /* Aya end marker — gold circle */
    .qpr-marker {
      display:inline-flex; align-items:center; justify-content:center;
      width:30px; height:30px; border-radius:50%;
      border:1.5px solid var(--gold-mid); color:var(--gold);
      font-family:'Cairo',sans-serif; font-size:12px; font-weight:700;
      margin:0 6px; vertical-align:middle; flex-shrink:0;
      background:rgba(212,168,67,.07);
    }

    /* ── Navigation ──────────────────────────────────────────── */
    .qpr-nav     { display:flex; justify-content:center; gap:8px; margin-top:14px; }
    .qpr-nav-btn { border:1px solid var(--border-strong); background:#fff; color:var(--text-mid); border-radius:9px; padding:8px 18px; font-family:'Cairo',sans-serif; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:background .13s, color .13s; }
    .qpr-nav-btn:hover:not(:disabled) { background:var(--teal-light); color:var(--teal-dark); border-color:var(--teal-mid); }
    .qpr-nav-btn:disabled { opacity:.45; cursor:not-allowed; }

    /* ── Modal ───────────────────────────────────────────────── */
    .qprm-backdrop {
      position:fixed; inset:0;
      background:rgba(22,16,8,.6);
      display:flex; align-items:center; justify-content:center;
      padding:16px; z-index:1200;
    }
    .qprm-box {
      width:min(680px,100%); max-height:90vh; overflow-y:auto;
      background:#fff; border:1px solid var(--border-strong);
      border-radius:16px; display:flex; flex-direction:column;
    }

    /* Sticky header */
    .qprm-head {
      display:flex; align-items:center; justify-content:space-between;
      padding:13px 16px; border-bottom:1px solid var(--border);
      position:sticky; top:0; background:#fff; z-index:2;
    }
    .qprm-title   { font-family:'Cairo',sans-serif; font-size:15px; font-weight:700; color:var(--teal-dark); display:flex; align-items:center; gap:8px; }
    .qprm-ref     { font-family:'Cairo',sans-serif; font-size:12px; color:var(--text-light); background:var(--cream2); border:1px solid var(--border); border-radius:6px; padding:2px 8px; }
    .qprm-close   { border:1px solid var(--border-strong); background:#fff; border-radius:8px; width:30px; height:30px; cursor:pointer; color:var(--text-mid); font-size:15px; display:flex; align-items:center; justify-content:center; transition:background .12s; }
    .qprm-close:hover { background:var(--cream2); }

    /* Aya preview in modal */
    .qprm-aya-preview {
      padding:14px 18px 10px;
      border-bottom:1px solid var(--border);
      background:var(--cream);
    }
    .qprm-aya-arabic {
      direction:rtl; text-align:right;
      font-family:'Amiri',serif; font-size:26px; line-height:2;
      color:var(--text-dark); margin-bottom:8px;
    }
    .qprm-aya-translation {
      font-family:'Lora',Georgia,serif; font-size:13.5px; line-height:1.7;
      color:var(--text-mid); font-style:italic;
    }
    .qprm-meal-label {
      display:inline-block; font-family:'Cairo',sans-serif; font-size:10px;
      color:var(--text-light); background:var(--cream2); border:1px solid var(--border);
      border-radius:4px; padding:1px 6px; margin-bottom:5px;
    }

    /* Tabs */
    .qprm-tabs    { display:flex; gap:0; border-bottom:1px solid var(--border); padding:0 16px; background:#fff; }
    .qprm-tab     { padding:10px 16px; font-family:'Cairo',sans-serif; font-size:13px; font-weight:600; color:var(--text-light); cursor:pointer; border:none; background:transparent; border-bottom:2px solid transparent; margin-bottom:-1px; transition:color .15s, border-color .15s; display:flex; align-items:center; gap:6px; }
    .qprm-tab:hover { color:var(--teal-dark); }
    .qprm-tab.active { color:var(--teal-dark); border-bottom-color:var(--teal-mid); }
    .qprm-tab-badge { display:inline-flex; align-items:center; justify-content:center; min-width:18px; height:18px; background:var(--teal-dark); color:#fff; border-radius:999px; font-size:10px; padding:0 4px; }

    /* Tab body */
    .qprm-body { padding:14px 16px; display:flex; flex-direction:column; gap:10px; flex:1; }

    /* Note card */
    .qprm-note     { border:1px solid var(--border); border-radius:10px; padding:12px 14px; background:var(--gold-pale); }
    .qprm-note-top { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:6px; }
    .qprm-note-title  { font-family:'Cairo',sans-serif; font-size:14px; font-weight:700; color:var(--text-dark); flex:1; }
    .qprm-note-type   { display:inline-block; font-family:'Cairo',sans-serif; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; padding:2px 8px; border-radius:999px; white-space:nowrap; flex-shrink:0; }
    .qprm-note-type.note     { color:#165a4c; background:#ddf4ee; border:1px solid #a8dfd2; }
    .qprm-note-type.footnote { color:#7a5500; background:#f7ebce; border:1px solid #e6c983; }
    .qprm-note-type.research { color:#5b21b6; background:#ede9fe; border:1px solid #cfc4fb; }
    .qprm-note-body { font-family:'Lora',Georgia,serif; font-size:13.5px; color:var(--text-mid); line-height:1.75; white-space:pre-wrap; }
    .qprm-word-chip { margin-bottom:8px; }
    .qprm-word-chip-label { font-family:'Cairo',sans-serif; font-size:10px; font-weight:700; color:var(--text-light); text-transform:uppercase; letter-spacing:.4px; margin-bottom:3px; }
    .qprm-word-chip-ar    { display:inline-block; direction:rtl; font-family:'Amiri',serif; font-size:26px; color:var(--teal-dark); background:var(--teal-light); border:1px solid rgba(45,155,132,.24); border-radius:9px; padding:2px 10px; line-height:1.65; }
    .qprm-note-tags { margin-top:8px; display:flex; flex-wrap:wrap; gap:5px; }
    .qprm-note-tag  { font-family:'Cairo',sans-serif; font-size:11px; color:var(--teal-dark); background:var(--teal-light); border:1px solid rgba(45,155,132,.25); border-radius:999px; padding:2px 8px; }

    /* Note form */
    .qprm-form { display:flex; flex-direction:column; gap:12px; }
    .qprm-form-row { display:flex; flex-direction:column; gap:4px; }
    .qprm-form-label { font-family:'Cairo',sans-serif; font-size:11px; font-weight:700; color:var(--text-mid); text-transform:uppercase; letter-spacing:.5px; }
    .qprm-form-input, .qprm-form-select, .qprm-form-textarea {
      border:1px solid var(--border-strong); border-radius:9px;
      padding:8px 11px; font-family:'Cairo',sans-serif; font-size:13.5px;
      color:var(--text-dark); background:#fff; width:100%;
      transition:border-color .15s, box-shadow .15s;
    }
    .qprm-form-input:focus, .qprm-form-select:focus, .qprm-form-textarea:focus {
      outline:none; border-color:var(--teal-mid); box-shadow:0 0 0 3px rgba(45,155,132,.1);
    }
    .qprm-form-textarea { resize:vertical; min-height:100px; font-family:'Lora',Georgia,serif; font-size:13.5px; line-height:1.65; }
    .qprm-type-row { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; }
    .qprm-type-opt { position:relative; }
    .qprm-type-opt input { position:absolute; opacity:0; width:0; height:0; }
    .qprm-type-btn {
      display:flex; align-items:center; justify-content:center; gap:5px;
      padding:8px 6px; border-radius:9px; border:1.5px solid var(--border-strong);
      background:#fff; cursor:pointer; font-family:'Cairo',sans-serif; font-size:12.5px;
      color:var(--text-mid); transition:all .15s;
    }
    .qprm-type-opt input:checked + .qprm-type-btn.t-note     { border-color:#3b82f6; background:#eff6ff; color:#1d4ed8; }
    .qprm-type-opt input:checked + .qprm-type-btn.t-footnote { border-color:#f59e0b; background:#fffbeb; color:#92400e; }
    .qprm-type-opt input:checked + .qprm-type-btn.t-research { border-color:#8b5cf6; background:#f5f3ff; color:#5b21b6; }
    .qprm-tag-grid { display:flex; flex-wrap:wrap; gap:6px; }
    .qprm-tag-opt  { position:relative; }
    .qprm-tag-opt input { position:absolute; opacity:0; width:0; height:0; }
    .qprm-tag-chip {
      display:inline-flex; align-items:center; gap:4px;
      font-family:'Cairo',sans-serif; font-size:12px;
      padding:4px 10px; border-radius:999px;
      border:1px solid var(--border-strong); background:#fff; cursor:pointer;
      color:var(--text-mid); transition:all .15s;
    }
    .qprm-tag-opt input:checked + .qprm-tag-chip { background:var(--teal-light); border-color:var(--teal-mid); color:var(--teal-dark); font-weight:600; }
    .qprm-new-tag-row { display:flex; gap:6px; }
    .qprm-save-btn {
      padding:10px 20px; border:none; border-radius:10px;
      background:var(--teal-dark); color:#fff;
      font-family:'Cairo',sans-serif; font-size:14px; font-weight:600;
      cursor:pointer; transition:opacity .15s;
      display:flex; align-items:center; gap:6px; align-self:flex-end;
    }
    .qprm-save-btn:hover { opacity:.88; }
    .qprm-save-btn:disabled { opacity:.5; cursor:not-allowed; }
    .qprm-error { font-family:'Cairo',sans-serif; font-size:12px; color:#c0392b; margin-top:2px; }

    /* Empty + goto */
    .qprm-empty { font-family:'Cairo',sans-serif; font-size:13px; color:var(--text-light); padding:.5rem 0; display:flex; align-items:center; gap:8px; }
    .qprm-goto  {
      display:inline-flex; align-items:center; gap:6px;
      font-family:'Cairo',sans-serif; font-size:12px; color:var(--teal-mid);
      text-decoration:none; padding:7px 13px;
      border:1px solid rgba(45,155,132,.3); border-radius:8px;
      background:var(--teal-light); transition:background .13s;
      align-self:flex-start;
    }
    .qprm-goto:hover { background:rgba(45,155,132,.15); }

    /* Footer */
    .qprm-footer { padding:10px 16px; border-top:1px solid var(--border); background:var(--cream); border-radius:0 0 16px 16px; }

    @media (max-width:768px) {
      .qpr-toolbar  { grid-template-columns:1fr; }
      .qpr-mushaf   { font-size:26px; line-height:2.2; }
      .qpr-page     { padding:14px 16px; }
      .qpr-page-head { flex-direction:column; align-items:flex-start; gap:4px; }
      .qprm-aya-arabic { font-size:22px; }
      .qprm-type-row { grid-template-columns:1fr; }
    }
  </style>

  {{-- ── Page header ─────────────────────────────────────────────────── --}}
  <div class="qpr-header">
    <div>
      <h1 class="qpr-title">Kur'an Okuma</h1>
      <p class="qpr-sub">Sure, ayet ve sayfa seçimi ile mushaf görünümünde okuma</p>
    </div>
  </div>

  {{-- ── Toolbar ──────────────────────────────────────────────────────── --}}
  <div class="qpr-toolbar">
    <label class="qpr-field">
      <span class="qpr-label">Sure</span>
      <select wire:model.live="selectedSura" class="qpr-select">
        @foreach ($suraOptions as $opt)
          <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
        @endforeach
      </select>
    </label>
    <label class="qpr-field">
      <span class="qpr-label">Ayet</span>
      <select wire:model.live="selectedAya" class="qpr-select">
        @foreach ($ayaOptions as $opt)
          <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
        @endforeach
      </select>
    </label>
    <label class="qpr-field">
      <span class="qpr-label">Sayfa</span>
      <select wire:model.live="selectedPage" class="qpr-select">
        @foreach ($pageOptions as $p)
          <option value="{{ $p }}">{{ $p }}. Sayfa</option>
        @endforeach
      </select>
    </label>
  </div>

  {{-- ── Bookmarks ─────────────────────────────────────────────────────── --}}
  <div class="qpr-bookmarks">
    <div class="qpr-bookmarks-head">
      <div class="qpr-bookmarks-title">
        <i class="ti ti-bookmark"></i> Yer İmleri
      </div>
      <div class="qpr-bookmark-form">
        <input type="text" wire:model="bookmarkLabel" class="qpr-bookmark-input" placeholder="Etiket (isteğe bağlı)">
        <button type="button" wire:click="addBookmark" class="qpr-bookmark-btn">
          <i class="ti ti-bookmark-plus"></i> Bu Sayfayı Kaydet
        </button>
        @if($this->isCurrentPageBookmarked)
          <button type="button" wire:click="removeBookmark({{ $selectedPage }})" class="qpr-bookmark-btn alt">
            <i class="ti ti-bookmark-off"></i> Yer İmini Kaldır
          </button>
        @endif
      </div>
    </div>
    <div class="qpr-bookmarks-list">
      @forelse ($this->bookmarks as $bm)
        <div class="qpr-bookmark-pill">
          <button type="button" class="qpr-bookmark-go" wire:click="$set('selectedPage', {{ $bm->page }})">
            Sayfa {{ $bm->page }}@if($bm->label) · {{ $bm->label }}@endif
          </button>
          <button type="button" class="qpr-bookmark-del" wire:click="removeBookmark({{ $bm->page }})" title="Kaldır">×</button>
        </div>
      @empty
        <span class="qprm-empty" style="font-size:12px;">Henüz yer imi yok.</span>
      @endforelse
    </div>
  </div>

  {{-- ── Mushaf page ──────────────────────────────────────────────────── --}}
  <div
    class="qpr-page"
    wire:loading.style="opacity:0.65;transition:opacity .2s"
    wire:target="selectedPage,prevPage,nextPage,selectedSura,selectedAya"
  >
    <div class="qpr-page-head">
      <span class="qpr-page-num">{{ $selectedPage }}. Sayfa</span>
      @php $firstRow = $rows->first(); $lastRow = $rows->last(); @endphp
      @if($firstRow && $lastRow)
        <span class="qpr-page-range">
          {{ $firstRow['sura_name'] }} {{ $firstRow['sura'] }}:{{ $firstRow['aya'] }}
          @if($lastRow['sura'] !== $firstRow['sura'] || $lastRow['aya'] !== $firstRow['aya'])
            — {{ $lastRow['sura_name'] }} {{ $lastRow['sura'] }}:{{ $lastRow['aya'] }}
          @endif
        </span>
      @endif
    </div>

    <div class="qpr-mushaf">
      @forelse ($rows as $row)
        <span class="qpr-ayah">
          {{-- Note count badge (top-left corner, above aya) --}}
          @if(($row['note_count'] ?? 0) > 0)
            <span class="qpr-note-badge">
              <i class="ti ti-notes"></i> {{ $row['note_count'] }}
            </span>
          @endif

          <button
            type="button"
            class="qpr-ayah-btn"
            wire:click="openNotesModal({{ $row['sura'] }}, {{ $row['aya'] }})"
            title="{{ $row['sura_name'] }} {{ $row['sura'] }}:{{ $row['aya'] }}"
          >
            {{ $row['arabic_text'] }}
            <span class="qpr-marker">{{ $row['aya'] }}</span>
          </button>
        </span>
      @empty
        <p class="qprm-empty"><i class="ti ti-mood-empty"></i> Bu sayfada ayet bulunamadı.</p>
      @endforelse
    </div>

    <div class="qpr-nav">
      <button type="button" class="qpr-nav-btn" wire:click="prevPage" wire:loading.attr="disabled" wire:target="prevPage">
        <span wire:loading.remove wire:target="prevPage"><i class="ti ti-arrow-left"></i> Önceki Sayfa</span>
        <span wire:loading wire:target="prevPage">…</span>
      </button>
      <button type="button" class="qpr-nav-btn" wire:click="nextPage" wire:loading.attr="disabled" wire:target="nextPage">
        <span wire:loading.remove wire:target="nextPage">Sonraki Sayfa <i class="ti ti-arrow-right"></i></span>
        <span wire:loading wire:target="nextPage">…</span>
      </button>
    </div>
  </div>

  {{-- ── Notes modal ──────────────────────────────────────────────────── --}}
  @if($showNotesModal)
    @php
      $typeLabels = ['note' => 'Not', 'footnote' => 'Dipnot', 'research' => 'Araştırma'];
      $noteCount  = $this->modalNotes->count();
    @endphp

    <div
      class="qprm-backdrop"
      wire:click="closeNotesModal"
      role="dialog"
      aria-modal="true"
    >
      <div class="qprm-box" wire:click.stop>

        {{-- Sticky header --}}
        <div class="qprm-head">
          <div class="qprm-title">
            <i class="ti ti-notes" style="font-size:16px;color:var(--teal-mid);"></i>
            {{ $SURA_NAMES[$modalSura] ?? "Sure {$modalSura}" }}
            <span class="qprm-ref">{{ $modalSura }}:{{ $modalAya }}</span>
          </div>
          <button type="button" class="qprm-close" wire:click="closeNotesModal" aria-label="Kapat">
            <i class="ti ti-x"></i>
          </button>
        </div>

        {{-- Aya preview: Arabic + meal --}}
        @if($modalArabicText)
          <div class="qprm-aya-preview">
            <div class="qprm-aya-arabic">{{ $modalArabicText }}</div>
            @if($modalTranslation)
              <div class="qprm-meal-label">{{ $modalMealKeyLabel }}</div>
              <div class="qprm-aya-translation">{{ $modalTranslation }}</div>
            @endif
          </div>
        @endif

        {{-- Tabs --}}
        <div class="qprm-tabs">
          <button
            type="button"
            class="qprm-tab {{ $modalTab === 'notes' ? 'active' : '' }}"
            wire:click="switchTab('notes')"
          >
            <i class="ti ti-list"></i>
            Notlar
            @if($noteCount > 0)
              <span class="qprm-tab-badge">{{ $noteCount }}</span>
            @endif
          </button>
          <button
            type="button"
            class="qprm-tab {{ $modalTab === 'add' ? 'active' : '' }}"
            wire:click="switchTab('add')"
          >
            <i class="ti ti-plus"></i>
            Not Ekle
          </button>
        </div>

        {{-- ── NOTES tab ──────────────────────────────────────── --}}
        @if($modalTab === 'notes')
          <div class="qprm-body">
            @forelse($this->modalNotes as $note)
              <article class="qprm-note">
                <div class="qprm-note-top">
                  @if($note->title)
                    <div class="qprm-note-title">{{ $note->title }}</div>
                  @endif
                  <span class="qprm-note-type {{ $note->type }}">
                    {{ $typeLabels[$note->type] ?? $note->type }}
                  </span>
                </div>

                @if(!is_null($note->word_position) && ($this->modalWordTexts[$note->id] ?? null))
                  <div class="qprm-word-chip">
                    <div class="qprm-word-chip-label">İlgili Kelime</div>
                    <div class="qprm-word-chip-ar">{{ $this->modalWordTexts[$note->id] }}</div>
                  </div>
                @endif

                <div class="qprm-note-body">{{ $note->content }}</div>

                @if($note->tags->isNotEmpty())
                  <div class="qprm-note-tags">
                    @foreach($note->tags as $tag)
                      <span class="qprm-note-tag">#{{ $tag->name }}</span>
                    @endforeach
                  </div>
                @endif
              </article>
            @empty
              <div class="qprm-empty">
                <i class="ti ti-mood-empty"></i>
                Bu ayete bağlı not bulunamadı.
              </div>
            @endforelse
          </div>

        {{-- ── ADD NOTE tab ────────────────────────────────────── --}}
        @elseif($modalTab === 'add')
          <div class="qprm-body">
            <div class="qprm-form">

              {{-- Type --}}
              <div class="qprm-form-row">
                <label class="qprm-form-label">Not Tipi</label>
                <div class="qprm-type-row">
                  <label class="qprm-type-opt">
                    <input type="radio" wire:model="newNoteType" value="note">
                    <span class="qprm-type-btn t-note">
                      <i class="ti ti-pencil" style="font-size:13px;"></i> Not
                    </span>
                  </label>
                  <label class="qprm-type-opt">
                    <input type="radio" wire:model="newNoteType" value="footnote">
                    <span class="qprm-type-btn t-footnote">
                      <i class="ti ti-bookmark" style="font-size:13px;"></i> Dipnot
                    </span>
                  </label>
                  <label class="qprm-type-opt">
                    <input type="radio" wire:model="newNoteType" value="research">
                    <span class="qprm-type-btn t-research">
                      <i class="ti ti-microscope" style="font-size:13px;"></i> Araştırma
                    </span>
                  </label>
                </div>
              </div>

              {{-- Title --}}
              <div class="qprm-form-row">
                <label class="qprm-form-label">Başlık <span style="font-weight:400;text-transform:none;letter-spacing:0;">(isteğe bağlı)</span></label>
                <input
                  type="text"
                  wire:model="newNoteTitle"
                  class="qprm-form-input"
                  placeholder="Not başlığı..."
                >
              </div>

              {{-- Content --}}
              <div class="qprm-form-row">
                <label class="qprm-form-label">İçerik <span style="color:#c0392b;">*</span></label>
                <textarea
                  wire:model="newNoteContent"
                  class="qprm-form-textarea"
                  placeholder="Notunuzu buraya yazın..."
                  rows="4"
                ></textarea>
                @error('newNoteContent')
                  <span class="qprm-error"><i class="ti ti-alert-circle" style="font-size:12px;"></i> {{ $message }}</span>
                @enderror
              </div>

              {{-- Word position --}}
              @if($this->modalWords->isNotEmpty())
                <div class="qprm-form-row">
                  <label class="qprm-form-label">İlgili Kelime <span style="font-weight:400;text-transform:none;letter-spacing:0;">(isteğe bağlı)</span></label>
                  <select wire:model="newNoteWordPos" class="qprm-form-select">
                    <option value="">— Ayet geneli —</option>
                    @foreach($this->modalWords as $word)
                      <option value="{{ $word->position }}">
                        {{ $word->text }}  ({{ $word->position }}. kelime)
                      </option>
                    @endforeach
                  </select>
                </div>
              @endif

              {{-- Tags --}}
              @if($this->userTags->isNotEmpty() || true)
                <div class="qprm-form-row">
                  <label class="qprm-form-label">Etiketler</label>
                  @if($this->userTags->isNotEmpty())
                    <div class="qprm-tag-grid">
                      @foreach($this->userTags as $tag)
                        <label class="qprm-tag-opt">
                          <input
                            type="checkbox"
                            wire:model="newNoteTagIds"
                            value="{{ $tag->id }}"
                          >
                          <span class="qprm-tag-chip"># {{ $tag->name }}</span>
                        </label>
                      @endforeach
                    </div>
                  @endif

                  {{-- New tag inline --}}
                  <div class="qprm-new-tag-row" style="margin-top:6px;">
                    <input
                      type="text"
                      wire:model="newTagName"
                      wire:keydown.enter.prevent="createAndAttachTag"
                      class="qprm-form-input"
                      style="flex:1;"
                      placeholder="Yeni etiket oluştur..."
                    >
                    <button
                      type="button"
                      wire:click="createAndAttachTag"
                      class="qpr-bookmark-btn"
                      style="white-space:nowrap;"
                    >
                      <i class="ti ti-plus"></i> Ekle
                    </button>
                  </div>
                </div>
              @endif

              {{-- Save --}}
              <button
                type="button"
                wire:click="saveNote"
                wire:loading.attr="disabled"
                wire:target="saveNote"
                class="qprm-save-btn"
              >
                <span wire:loading.remove wire:target="saveNote">
                  <i class="ti ti-device-floppy"></i> Notu Kaydet
                </span>
                <span wire:loading wire:target="saveNote">Kaydediliyor…</span>
              </button>

            </div>
          </div>
        @endif

        {{-- Footer --}}
        <div class="qprm-footer">
          <a
            href="{{ route('user.quran-text') }}?sura={{ $modalSura }}&aya={{ $modalAya }}"
            class="qprm-goto"
          >
            <i class="ti ti-external-link"></i>
            Not düzenleme sayfasında aç
          </a>
        </div>

      </div>
    </div>
  @endif

</div>
