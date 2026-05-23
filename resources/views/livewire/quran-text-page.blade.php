<div
    x-data="{ showNoteModal: false, showWordAnalysis: false, showNotesViewer: false, arabicHover: false }"
    @close-note-modal.window="showNoteModal = false"
    @keydown.window.left.prevent="document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA' || document.activeElement.tagName === 'SELECT' ? null : $wire.prevAya()"
    @keydown.window.right.prevent="document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA' || document.activeElement.tagName === 'SELECT' ? null : $wire.nextAya()"
    class="qtp"
    style="--arabic-font-family: {{ $this->arabicFontFamily }};"
>
<style>
@import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Noto+Naskh+Arabic:wght@400;600;700&family=Scheherazade+New:wght@400;700&display=swap');

/* ── Temel ─────────────────────────────────────────── */
.qtp { max-width: 860px; margin: 0 auto; display: flex; flex-direction: column; gap: 14px; }

/* ── Sayfa başlığı ──────────────────────────────────── */
.qtp-page-header { display: flex; justify-content: space-between; align-items: flex-end; gap: 10px; flex-wrap: wrap; }
.qtp-page-title  { font-family: 'Cairo', sans-serif; font-size: 22px; font-weight: 600; color: var(--teal-dark); margin: 0; }
.qtp-page-sub    { font-size: 12px; color: var(--text-light); margin: 3px 0 0; }

/* ── Kart ───────────────────────────────────────────── */
.qtp-card { background: #fff; border: 1px solid var(--border-strong); border-radius: 14px; padding: 18px; }

/* ── Navigasyon ─────────────────────────────────────── */
.qtp-nav-row     { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
.qtp-selects-row { display: flex; gap: 10px; flex: 1; min-width: 220px; }
.qtp-field       { display: flex; flex-direction: column; gap: 5px; font-family: 'Cairo', sans-serif; font-size: 12px; font-weight: 600; color: var(--text-light); letter-spacing: .5px; text-transform: uppercase; flex: 1; }
.qtp-select      { padding: 9px 12px; border: 1px solid var(--border-strong); border-radius: 9px; font-size: 14px; font-family: 'Cairo', sans-serif; background: #fff; color: var(--text-dark); cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238a7a60' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; padding-right: 30px; }
.qtp-select:focus { outline: none; border-color: var(--teal-mid); box-shadow: 0 0 0 3px rgba(45,155,132,.12); }

/* ── Arama'lı Sure Dropdown (quran-reading ile uyumlu) ───────────── */
.qtp-ss-wrap     { position: relative; }
.qtp-ss-trigger  {
    width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 8px;
    border: 1px solid var(--border-strong); border-radius: 9px;
    padding: 9px 12px; font-family: 'Cairo', sans-serif; font-size: 14px;
    background: #fff; color: var(--text-dark); cursor: pointer; text-align: left;
    transition: border-color .15s, box-shadow .15s;
}
.qtp-ss-trigger:focus, .qtp-ss-trigger.open {
    outline: none; border-color: var(--teal-mid); box-shadow: 0 0 0 3px rgba(45,155,132,.12);
}
.qtp-ss-value    { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; text-align: left; }
.qtp-ss-dropdown {
    position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 200;
    background: #fff; border: 1px solid var(--border-strong); border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    display: flex; flex-direction: column; overflow: hidden;
}
.qtp-ss-search-wrap {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 10px; border-bottom: 1px solid var(--border);
}
.qtp-ss-search-icon { color: var(--text-light); font-size: 14px; flex-shrink: 0; }
.qtp-ss-search  {
    flex: 1; border: none; outline: none; font-family: 'Cairo', sans-serif;
    font-size: 13px; color: var(--text-dark); background: transparent;
}
.qtp-ss-list    {
    list-style: none; margin: 0; padding: 4px 0;
    max-height: 220px; overflow-y: auto;
}
.qtp-ss-option  {
    padding: 8px 12px; font-family: 'Cairo', sans-serif; font-size: 13px;
    color: var(--text-dark); cursor: pointer; transition: background .1s;
}
.qtp-ss-option:hover  { background: var(--teal-light); color: var(--teal-dark); }
.qtp-ss-option.active { background: var(--teal-dark); color: #fff; font-weight: 700; }
.qtp-ss-empty   {
    padding: 10px 12px; font-family: 'Cairo', sans-serif; font-size: 13px;
    color: var(--text-light); text-align: center;
}

.qtp-aya-nav     { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.qtp-nav-btn     { display: flex; align-items: center; gap: 5px; padding: 9px 14px; border-radius: 9px; border: 1px solid var(--border-strong); background: #fff; color: var(--text-mid); font-family: 'Cairo', sans-serif; font-size: 13px; cursor: pointer; transition: all .15s; white-space: nowrap; }
.qtp-nav-btn:hover:not(:disabled) { background: var(--teal-light); color: var(--teal-dark); border-color: var(--teal-mid); }
.qtp-nav-btn:disabled { opacity: .4; cursor: not-allowed; }
.qtp-nav-badge   { font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 700; color: var(--teal-dark); background: var(--teal-light); border: 1px solid rgba(45,155,132,.25); border-radius: 8px; padding: 6px 12px; min-width: 58px; text-align: center; }
.qtp-kbd-hint    { font-size: 11px; color: var(--text-light); }
.qtp-kbd         { display: inline-block; padding: 1px 5px; background: var(--cream2); border: 1px solid var(--border-strong); border-radius: 4px; font-family: monospace; font-size: 10px; }

/* ── Ayet kartı ─────────────────────────────────────── */
.qtp-ayah-header  { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 18px; flex-wrap: wrap; }
.qtp-sura-ref     { display: flex; align-items: center; gap: 10px; }
.qtp-sura-name    { font-family: 'Cairo', sans-serif; font-size: 18px; font-weight: 700; color: var(--teal-dark); }
.qtp-aya-badge    { font-family: 'Cairo', sans-serif; font-size: 13px; color: var(--text-light); background: var(--cream2); border: 1px solid var(--border-strong); border-radius: 7px; padding: 3px 9px; }
.qtp-add-note-btn { display: flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 9px; border: 1px solid var(--border-strong); background: var(--gold-pale); color: var(--gold); font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; transition: all .15s; text-decoration: none; }
.qtp-add-note-btn:hover { background: var(--gold-light); border-color: var(--gold-mid); }

/* ── Arapça metin ───────────────────────────────────── */
.qtp-arabic-block { background: var(--cream); border: 1px solid var(--border); border-radius: 12px; padding: 22px 28px; margin-bottom: 16px; }
.qtp-ar           {
    direction: rtl;
    font-family: var(--arabic-font-family);
    font-size: 30px;
    line-height: 2.3;
    color: var(--text-dark);
    text-align: right;
    font-weight: 400;
    font-feature-settings: "liga" 1, "calt" 1, "kern" 1;
    font-kerning: normal;
    text-rendering: optimizeLegibility;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
.qtp-aya-marker   {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px; height: 24px;
    border-radius: 50%;
    border: 1.5px solid rgba(184,134,11,.5);
    color: #8a6522;
    font-family: var(--arabic-font-family);
    font-size: 12px;
    font-weight: 700;
    margin: 0 4px;
    vertical-align: baseline;
    background: rgba(212,168,67,.1);
}

/* ── Tercümeler ─────────────────────────────────────── */
.qtp-translations  { display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px; }
.qtp-translation-item { border-radius: 10px; padding: 14px 16px; border-left: 3px solid var(--teal-mid); background: var(--gold-pale); }
.qtp-meal-key     { display: flex; align-items: center; gap: 5px; font-family: 'Cairo', sans-serif; font-size: 11px; font-weight: 700; color: var(--text-light); letter-spacing: .8px; text-transform: uppercase; margin-bottom: 6px; }
.qtp-translation-text { font-family: 'Lora', Georgia, serif; font-size: 15px; line-height: 1.8; color: var(--text-dark); margin: 0; }
.qtp-no-translation { font-size: 13px; color: var(--text-light); padding: 10px 0; }
.qtp-link         { color: var(--teal-dark); text-decoration: underline; }

/* ── Kelime analizi (collapsible) ───────────────────── */
.qtp-collapsible-btn { display: flex; align-items: center; gap: 7px; width: 100%; padding: 9px 12px; border: 1px solid var(--border-strong); border-radius: 9px; background: var(--cream2); color: var(--text-mid); font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; transition: all .15s; }
.qtp-collapsible-btn:hover { background: var(--cream); color: var(--teal-dark); }
.qtp-collapsible-chevron { margin-left: auto; transition: transform .2s; }
.qtp-collapsible-chevron.open { transform: rotate(180deg); }
.qtp-word-table   { width: 100%; border-collapse: collapse; font-size: 13px; }
.qtp-word-table th, .qtp-word-table td { border: 1px solid var(--border); padding: 9px 12px; text-align: left; }
.qtp-word-table th { background: var(--cream2); color: var(--text-mid); font-family: 'Cairo', sans-serif; font-weight: 600; }
.qtp-word-table td { color: var(--text-dark); }

/* ── Notlar bölümü ──────────────────────────────────── */
.qtp-notes-top    { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap; margin-bottom: 12px; }
.qtp-section-title { font-family: 'Cairo', sans-serif; font-size: 16px; font-weight: 700; color: var(--teal-dark); margin: 0; }
.qtp-tag-add-row  { display: flex; gap: 8px; align-items: center; }
.qtp-input        { padding: 9px 12px; border: 1px solid var(--border-strong); border-radius: 9px; font-size: 14px; font-family: 'Cairo', sans-serif; background: #fff; color: var(--text-dark); }
.qtp-input:focus  { outline: none; border-color: var(--teal-mid); box-shadow: 0 0 0 3px rgba(45,155,132,.12); }

.qtp-tags-filter  { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px; }
.qtp-tag          { padding: 4px 10px; border-radius: 999px; font-family: 'Cairo', sans-serif; font-size: 12px; border: 1px solid var(--border-strong); background: #fff; color: var(--text-mid); cursor: pointer; transition: all .12s; }
.qtp-tag:hover    { background: var(--teal-light); border-color: var(--teal-mid); color: var(--teal-dark); }
.qtp-tag.active   { background: var(--teal-dark); color: #fff; border-color: var(--teal-dark); }

/* ── Not kartı ──────────────────────────────────────── */
.qtp-note-card    { border: 1px solid var(--border); border-radius: 11px; padding: 14px 16px; background: var(--gold-pale); margin-bottom: 10px; }
.qtp-note-top     { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin-bottom: 8px; }
.qtp-note-title-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.qtp-note-type-badge { font-family: 'Cairo', sans-serif; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 5px; letter-spacing: .4px; }
.qtp-type-note       { background: var(--teal-light); color: var(--teal-dark); }
.qtp-type-footnote   { background: var(--gold-light); color: #7a5500; }
.qtp-type-research   { background: #ede9fe; color: #5b21b6; }
.qtp-note-title   { font-family: 'Cairo', sans-serif; font-size: 15px; font-weight: 700; color: var(--text-dark); }
.qtp-note-actions { display: flex; gap: 4px; flex-shrink: 0; }
.qtp-action-btn   { width: 30px; height: 30px; border-radius: 7px; border: 1px solid var(--border-strong); background: #fff; color: var(--text-mid); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all .12s; font-size: 15px; }
.qtp-action-btn:hover { background: var(--teal-light); color: var(--teal-dark); border-color: var(--teal-mid); }
.qtp-action-btn.danger:hover { background: #fff1f0; color: #b42318; border-color: #f1b6b6; }
.qtp-note-word-ref { display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--text-light); margin-bottom: 8px; }
.qtp-note-body    { font-family: 'Lora', Georgia, serif; font-size: 14px; line-height: 1.75; color: var(--text-dark); white-space: pre-wrap; }
.qtp-note-footer  { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-top: 10px; flex-wrap: wrap; }
.qtp-note-date    { font-size: 11px; color: var(--text-light); white-space: nowrap; }
.qtp-note-tags    { display: flex; gap: 5px; flex-wrap: wrap; }

.qtp-notes-empty  { text-align: center; padding: 32px 20px; color: var(--text-light); }
.qtp-notes-empty i { font-size: 36px; display: block; margin-bottom: 10px; }
.qtp-notes-empty p { font-size: 14px; margin: 0 0 14px; }
.qtp-empty-state  { padding: 20px; text-align: center; color: var(--text-light); font-size: 14px; }

/* ── Butonlar ───────────────────────────────────────── */
.qtp-btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 15px; border-radius: 9px; border: 1px solid var(--border-strong); background: #fff; color: var(--text-mid); font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 500; cursor: pointer; text-decoration: none; transition: all .15s; white-space: nowrap; }
.qtp-btn:hover { background: var(--cream2); color: var(--text-dark); }
.qtp-btn.primary { background: var(--teal-dark); color: #fff; border-color: var(--teal-dark); }
.qtp-btn.primary:hover { background: #145246; }
.qtp-btn:disabled { opacity: .5; cursor: not-allowed; }

/* ── Modal ──────────────────────────────────────────── */
.qtp-modal-backdrop { position: fixed; inset: 0; background: rgba(22,16,8,.5); display: flex; align-items: center; justify-content: center; padding: 18px; z-index: 1200; backdrop-filter: blur(2px); }
.qtp-modal          { width: min(780px, 100%); max-height: 90vh; overflow-y: auto; background: #fff; border-radius: 16px; border: 1px solid var(--border-strong); display: flex; flex-direction: column; }
.qtp-modal-head     { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 18px 22px 14px; border-bottom: 1px solid var(--border); }
.qtp-modal-title    { font-family: 'Cairo', sans-serif; font-size: 18px; font-weight: 700; color: var(--teal-dark); margin: 0; }
.qtp-modal-close    { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-strong); background: #fff; color: var(--text-light); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 17px; transition: all .12s; flex-shrink: 0; }
.qtp-modal-close:hover { background: #fff1f0; color: #b42318; border-color: #f1b6b6; }
.qtp-modal-body     { padding: 18px 22px; flex: 1; display: flex; flex-direction: column; gap: 12px; }
.qtp-modal-foot     { padding: 14px 22px 18px; border-top: 1px solid var(--border); display: flex; gap: 8px; }

.qtp-form-grid  { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
.qtp-textarea   { width: 100%; padding: 10px 12px; border: 1px solid var(--border-strong); border-radius: 9px; font-size: 14px; font-family: 'Lora', Georgia, serif; background: #fff; color: var(--text-dark); min-height: 160px; resize: vertical; }
.qtp-textarea:focus, .qtp-input:focus { outline: none; border-color: var(--teal-mid); box-shadow: 0 0 0 3px rgba(45,155,132,.12); }
.qtp-error      { font-size: 12px; color: #b42318; }
.qtp-preview    { background: var(--cream); border: 1px dashed var(--border-strong); border-radius: 10px; padding: 14px 16px; }
.qtp-preview-label { font-family: 'Cairo', sans-serif; font-size: 11px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: .8px; margin-bottom: 8px; }
.qtp-preview-title { font-family: 'Cairo', sans-serif; font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px; }
.qtp-preview-body  { font-family: 'Lora', serif; font-size: 14px; line-height: 1.7; color: var(--text-mid); }

/* ── Not kelime chip'i ──────────────────────────────── */
.qtp-note-word-chip  { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 10px; }
.qtp-note-word-label { font-family: 'Cairo', sans-serif; font-size: 10px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: .6px; margin-bottom: 3px; }
.qtp-note-word-ar    { font-family: var(--arabic-font-family); font-size: 26px; direction: rtl; color: var(--teal-dark); background: var(--teal-light); border: 1px solid rgba(45,155,132,.22); border-radius: 10px; padding: 3px 16px; line-height: 1.7; }
.qtp-nv-note .qtp-note-word-ar { font-size: 22px; padding: 2px 13px; }

/* ── Arapça blok tıklanabilir ───────────────────────── */
.qtp-arabic-clickable { cursor: pointer; position: relative; transition: border-color .15s, background .15s; }
.qtp-arabic-clickable:hover { border-color: var(--teal-mid) !important; background: #f4fbf9 !important; }
.qtp-ayah-hover-hint { display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 12px; padding: 7px 14px; background: rgba(26,107,90,.07); border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 12px; color: var(--teal-dark); }

/* ── Not sayısı rozeti ──────────────────────────────── */
.qtp-notes-count-pill { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 999px; background: var(--gold-light); border: 1px solid var(--gold-mid); color: #7a5500; font-family: 'Cairo', sans-serif; font-size: 12px; font-weight: 700; cursor: pointer; transition: all .12s; }
.qtp-notes-count-pill:hover { background: var(--gold-mid); color: #fff; }

/* ── Notlar görüntüleyici modal ─────────────────────── */
.qtp-nv { width: min(640px, 100%); }
.qtp-nv-arabic { padding: 14px 22px; background: var(--cream); border-bottom: 1px solid var(--border); direction: rtl; font-family: var(--arabic-font-family); font-size: 24px; line-height: 2; color: var(--text-dark); text-align: right; font-feature-settings: "liga" 1, "calt" 1, "kern" 1; text-rendering: optimizeLegibility; }
.qtp-nv-arabic-marker { color: var(--gold); }
.qtp-nv-list { padding: 16px 22px; display: flex; flex-direction: column; gap: 10px; max-height: 52vh; overflow-y: auto; }
.qtp-nv-note { border: 1px solid var(--border); border-radius: 10px; padding: 12px 14px; background: var(--gold-pale); }
.qtp-nv-note-head { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 8px; }
.qtp-nv-note-title { font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 700; color: var(--text-dark); flex: 1; }
.qtp-nv-note-body { font-family: 'Lora', serif; font-size: 13px; line-height: 1.75; color: var(--text-dark); white-space: pre-wrap; max-height: 100px; overflow-y: auto; }

/* ── Devam banneri ──────────────────────────────────── */
.qtp-resume-banner { display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: linear-gradient(135deg, var(--teal-light), #d6f5ed); border: 1px solid rgba(45,155,132,.3); border-radius: 12px; }
.qtp-resume-icon   { width: 36px; height: 36px; border-radius: 9px; background: var(--teal-dark); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.qtp-resume-text   { flex: 1; }
.qtp-resume-title  { font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 700; color: var(--teal-dark); }
.qtp-resume-sub    { font-family: 'Cairo', sans-serif; font-size: 12px; color: var(--text-mid); margin-top: 1px; }
.qtp-resume-close  { width: 28px; height: 28px; border-radius: 7px; border: 1px solid rgba(45,155,132,.25); background: rgba(255,255,255,.6); color: var(--teal-dark); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 15px; flex-shrink: 0; transition: background .12s; }
.qtp-resume-close:hover { background: rgba(255,255,255,.9); }


/* ── Responsive ─────────────────────────────────────── */
@media (max-width: 640px) {
    .qtp-form-grid  { grid-template-columns: 1fr; }
    .qtp-selects-row { flex-direction: column; }
    .qtp-nav-row    { flex-direction: column; align-items: stretch; }
    .qtp-aya-nav    { justify-content: center; }
    .qtp-ar         { font-size: 24px; line-height: 2.15; }
    .qtp-notes-top  { flex-direction: column; }
}
</style>

{{-- Kaldığı Yerden Devam Banneri --}}
@if($resumed)
    <div
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 6000)"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="qtp-resume-banner"
    >
        <div class="qtp-resume-icon"><i class="ti ti-bookmark"></i></div>
        <div class="qtp-resume-text">
            <div class="qtp-resume-title">{{ __('Continuing where you left off') }}</div>
            <div class="qtp-resume-sub">
                {{ $this->currentSuraName }} · {{ __('sura_ref', ['number' => $selectedSura]) }}, {{ __('verse_ref', ['number' => $selectedAya]) }}
            </div>
        </div>
        <button type="button" @click="show = false" class="qtp-resume-close" title="{{ __('Close') }}">
            <i class="ti ti-x"></i>
        </button>
    </div>
@endif

{{-- Sayfa Başlığı --}}
<div class="qtp-page-header">
    <div>
        <h1 class="qtp-page-title">{{ __('Quran Text') }}</h1>
        <p class="qtp-page-sub">{{ __('qt_subtitle') }}</p>
    </div>
    <span class="qtp-kbd-hint"><kbd class="qtp-kbd">←</kbd> <kbd class="qtp-kbd">→</kbd> {{ __('navigate between verses') }}</span>
</div>

{{-- Navigasyon Kartı --}}
<div class="qtp-card">
    <script>window.__qtpSuraOptions = @json($this->suraOptions);</script>
    <div class="qtp-nav-row">
        <div class="qtp-selects-row">
            <div
                class="qtp-field"
                x-data="{
                    open: false,
                    search: '',
                    options: window.__qtpSuraOptions || [],
                    get filtered() {
                        if (!this.search.trim()) return this.options;
                        const q = this.search.toLowerCase();
                        return this.options.filter(o => o.label.toLowerCase().includes(q));
                    },
                    get selectedLabel() {
                        const opt = this.options.find(o => o.value === $wire.selectedSura);
                        return opt ? opt.label : '—';
                    },
                    choose(val) {
                        $wire.$set('selectedSura', val);
                        this.open = false;
                        this.search = '';
                    }
                }"
                x-init="$watch('open', v => v && $nextTick(() => $refs.suraSrch?.focus()))"
                @click.outside="open = false"
                @keydown.escape.window="open = false"
            >
                <span>{{ __('Sura') }}</span>
                <div class="qtp-ss-wrap">
                    <button
                        type="button"
                        class="qtp-ss-trigger"
                        :class="{ open }"
                        @click="open = !open"
                    >
                        <span class="qtp-ss-value" x-text="selectedLabel"></span>
                        <i class="ti" :class="open ? 'ti-chevron-up' : 'ti-chevron-down'" style="font-size:12px;opacity:.55;flex-shrink:0;"></i>
                    </button>

                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="qtp-ss-dropdown"
                        style="display:none;"
                        @click.stop
                    >
                        <div class="qtp-ss-search-wrap">
                            <i class="ti ti-search qtp-ss-search-icon"></i>
                            <input
                                x-ref="suraSrch"
                                x-model="search"
                                type="text"
                                class="qtp-ss-search"
                                placeholder="{{ __('Search sura...') }}"
                                @keydown.escape.stop="open = false"
                            >
                        </div>
                        <ul class="qtp-ss-list">
                            <template x-for="opt in filtered" :key="opt.value">
                                <li
                                    class="qtp-ss-option"
                                    :class="{ active: opt.value === $wire.selectedSura }"
                                    @click="choose(opt.value)"
                                    x-text="opt.label"
                                ></li>
                            </template>
                            <li x-show="filtered.length === 0" class="qtp-ss-empty">{{ __('No results.') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <label class="qtp-field">
                {{ __('Verse') }}
                <select wire:model.live="selectedAya" class="qtp-select">
                    @foreach ($this->ayas as $aya)
                        <option value="{{ $aya }}">{{ __('verse_ref', ['number' => $aya]) }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <div class="qtp-aya-nav">
            <button wire:click="prevAya" wire:loading.attr="disabled" type="button" class="qtp-nav-btn" title="{{ __('Previous') }} (←)">
                <i class="ti ti-chevron-left"></i> {{ __('Previous') }}
            </button>
            <span class="qtp-nav-badge" wire:loading.class="opacity-50">{{ $selectedSura }}:{{ $selectedAya }}</span>
            <button wire:click="nextAya" wire:loading.attr="disabled" type="button" class="qtp-nav-btn" title="{{ __('Next') }} (→)">
                {{ __('Next') }} <i class="ti ti-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

{{-- Ayet Kartı --}}
<div class="qtp-card">
    <div class="qtp-ayah-header">
        <div class="qtp-sura-ref">
            <span class="qtp-sura-name">{{ $this->currentSuraName }}</span>
            <span class="qtp-aya-badge">{{ $selectedSura }}:{{ $selectedAya }}</span>
            @if($this->currentNotes->isNotEmpty())
                <button
                    type="button"
                    @click="showNotesViewer = true"
                    class="qtp-notes-count-pill"
                    title="{{ __('notes_hover_existing', ['count' => $this->currentNotes->count()]) }}"
                >
                    <i class="ti ti-notes"></i>
                    {{ $this->currentNotes->count() }} {{ __('note') }}
                </button>
            @endif
        </div>
        <button type="button" @click="showNoteModal = true" class="qtp-add-note-btn">
            <i class="ti ti-pencil-plus"></i> {{ __('Take Note') }}
        </button>
    </div>

    @if ($this->currentWords->isEmpty())
        <div class="qtp-empty-state">{{ __('No data found for this verse.') }}</div>
    @else
        {{-- Arapça Metin --}}
        <div
            class="qtp-arabic-block qtp-arabic-clickable"
            @mouseenter="arabicHover = true"
            @mouseleave="arabicHover = false"
            @click="showNotesViewer = true"
        >
            <div class="qtp-ar">
                {{ $this->currentArabicText }}
                <span class="qtp-aya-marker">﴿{{ $selectedAya }}﴾</span>
            </div>
            <div x-show="arabicHover" x-transition.opacity.duration.100ms class="qtp-ayah-hover-hint">
                @if($this->currentNotes->isNotEmpty())
                    <i class="ti ti-notes"></i>
                    {{ __('notes_hover_existing', ['count' => $this->currentNotes->count()]) }}
                @else
                    <i class="ti ti-notes"></i>
                    {{ __('Click to add a note') }}
                @endif
            </div>
        </div>

        {{-- Tercümeler --}}
        @if ($this->currentPreferredTranslations->isNotEmpty())
            <div class="qtp-translations">
                @foreach($this->currentPreferredTranslations as $translation)
                    <div class="qtp-translation-item">
                        <div class="qtp-meal-key">
                            <i class="ti ti-book-2"></i>
                            {{ $translation->meal_key }}
                        </div>
                        <p class="qtp-translation-text">{{ $translation->text }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="qtp-no-translation">
                {{ __('qt_no_translation_pre') }}
                <a href="{{ route('user.settings') }}" class="qtp-link">{{ __('qt_no_translation_link') }}</a>
            </div>
        @endif

        {{-- Kelime Analizi (gizlenebilir) --}}
        <div>
            <button
                type="button"
                @click="showWordAnalysis = !showWordAnalysis"
                class="qtp-collapsible-btn"
            >
                <i class="ti ti-letters"></i>
                {{ __('Word Analysis') }}
                <i
                    class="ti qtp-collapsible-chevron"
                    :class="showWordAnalysis ? 'ti-chevron-up open' : 'ti-chevron-down'"
                ></i>
            </button>
            <div x-show="showWordAnalysis" x-transition style="overflow: auto; margin-top: 10px;">
                <table class="qtp-word-table">
                    <thead>
                        <tr>
                            <th style="width:32px;">#</th>
                            <th>{{ __('Arabic') }}</th>
                            <th>{{ __('Transliteration') }}</th>
                            <th>{{ __('Turkish Meaning') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->currentWords as $word)
                            <tr>
                                <td style="color:var(--text-light);font-size:12px;">{{ $word->position }}</td>
                                <td dir="rtl" style="font-family:var(--arabic-font-family); font-size: 20px; text-align:right;">{{ $word->text }}</td>
                                <td style="color:var(--text-mid);">{{ $word->simple }}</td>
                                @php
                                    $hasTr = ! empty($word->translation_tr);
                                @endphp
                                <td style="font-weight: {{ $hasTr ? '600' : '400' }}; color: {{ $hasTr ? 'var(--teal-dark)' : 'var(--text-light)' }}; font-style: {{ $hasTr ? 'normal' : 'italic' }};">
                                    {{ $word->translation_tr ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

{{-- Notlar Bölümü --}}
<div class="qtp-card">
    <div class="qtp-notes-top">
        <h2 class="qtp-section-title">{{ __('My Verse Notes') }}</h2>
        <div class="qtp-tag-add-row">
            <input
                wire:model.defer="newTagName"
                type="text"
                class="qtp-input"
                placeholder="{{ __('New tag...') }}"
                style="width: 160px;"
            >
            <button wire:click="saveTag" type="button" class="qtp-btn">
                <i class="ti ti-tag"></i> {{ __('Add Tag') }}
            </button>
        </div>
    </div>

    {{-- Etiket Filtresi --}}
    @if($this->userTags->isNotEmpty())
        <div class="qtp-tags-filter">
            <button wire:click="$set('filterTagId', null)" type="button" class="qtp-tag {{ ! $filterTagId ? 'active' : '' }}">
                {{ __('All') }}
            </button>
            @foreach($this->userTags as $tag)
                <button
                    wire:click="$set('filterTagId', {{ $tag->id }})"
                    type="button"
                    class="qtp-tag {{ $filterTagId === $tag->id ? 'active' : '' }}"
                >#{{ $tag->name }}</button>
            @endforeach
        </div>
    @endif

    {{-- Not Listesi --}}
    @forelse($this->currentNotes as $note)
        <div class="qtp-note-card">
            <div class="qtp-note-top">
                <div class="qtp-note-title-row">
                    <span class="qtp-note-type-badge qtp-type-{{ $note->type }}">
                        {{ match($note->type) { 'note' => __('Note'), 'footnote' => __('Footnote'), 'research' => __('Research'), default => $note->type } }}
                    </span>
                    <span class="qtp-note-title">{{ $note->title }}</span>
                </div>
                <div class="qtp-note-actions">
                    <button
                        wire:click="editNote({{ $note->id }})"
                        @click="showNoteModal = true"
                        type="button"
                        class="qtp-action-btn"
                        title="{{ __('Edit') }}"
                    ><i class="ti ti-edit"></i></button>
                    <button
                        wire:click="deleteNote({{ $note->id }})"
                        type="button"
                        class="qtp-action-btn danger"
                        title="{{ __('Delete') }}"
                        onclick="return confirm('{{ __('delete_note_confirm') }}')"
                    ><i class="ti ti-trash"></i></button>
                </div>
            </div>

            @if($note->word_position)
                @php $noteWord = $this->currentWords->firstWhere('position', $note->word_position); @endphp
                <div class="qtp-note-word-chip">
                    <div>
                        <div class="qtp-note-word-label">{{ __('Related Word') }}</div>
                        <div class="qtp-note-word-ar">{{ $noteWord?->text ?? '—' }}</div>
                    </div>
                </div>
            @endif

            <div class="qtp-note-body">{{ $note->content }}</div>

            <div class="qtp-note-footer">
                <div class="qtp-note-tags">
                    @foreach($note->tags as $tag)
                        <span class="qtp-tag" style="cursor:default;">#{{ $tag->name }}</span>
                    @endforeach
                </div>
                <span class="qtp-note-date">{{ $note->updated_at->format('d.m.Y H:i') }}</span>
            </div>
        </div>
    @empty
        <div class="qtp-notes-empty">
            <i class="ti ti-notes-off"></i>
            <p>{{ __('No notes for this verse yet.') }}</p>
            <button type="button" @click="showNoteModal = true" class="qtp-btn primary">{{ __('Take Your First Note') }}</button>
        </div>
    @endforelse
</div>

{{-- Notlar Görüntüleyici Modal --}}
<div
    x-show="showNotesViewer"
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="qtp-modal-backdrop"
    @click.self="showNotesViewer = false"
    role="dialog"
    aria-modal="true"
    style="display: none;"
>
    <div class="qtp-modal qtp-nv" @click.stop>
        <div class="qtp-modal-head">
            <div>
                <h2 class="qtp-modal-title">{{ $this->currentSuraName }} · {{ $selectedSura }}:{{ $selectedAya }}</h2>
                <p style="margin:3px 0 0; font-size:12px; color:var(--text-light); font-family:'Cairo',sans-serif;">
                    {{ $this->currentNotes->count() }} {{ __('note') }}
                </p>
            </div>
            <div style="display:flex; gap:8px; align-items:center;">
                <button
                    type="button"
                    @click="showNotesViewer = false; showNoteModal = true"
                    class="qtp-btn primary"
                    style="font-size:12px; padding:7px 12px;"
                >
                    <i class="ti ti-pencil-plus"></i> {{ __('Take Note') }}
                </button>
                <button type="button" @click="showNotesViewer = false" class="qtp-modal-close" title="{{ __('Close') }}">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        </div>

        {{-- Ayet özeti --}}
        <div class="qtp-nv-arabic">
            {{ $this->currentArabicText }}
            <span class="qtp-nv-arabic-marker">﴿{{ $selectedAya }}﴾</span>
        </div>

        {{-- Not listesi --}}
        <div class="qtp-nv-list">
            @forelse($this->currentNotes as $note)
                <div class="qtp-nv-note">
                    <div class="qtp-nv-note-head">
                        <span class="qtp-note-type-badge qtp-type-{{ $note->type }}">
                            {{ match($note->type) { 'note' => __('Note'), 'footnote' => __('Footnote'), 'research' => __('Research'), default => $note->type } }}
                        </span>
                        <span class="qtp-nv-note-title">{{ $note->title }}</span>
                        <span class="qtp-note-date">{{ $note->updated_at->format('d.m.Y') }}</span>
                        <button
                            wire:click="editNote({{ $note->id }})"
                            @click="showNotesViewer = false; showNoteModal = true"
                            type="button"
                            class="qtp-action-btn"
                            style="width:26px; height:26px; font-size:13px;"
                            title="{{ __('Edit') }}"
                        ><i class="ti ti-edit"></i></button>
                    </div>
                    @if($note->word_position)
                        @php $noteWord = $this->currentWords->firstWhere('position', $note->word_position); @endphp
                        <div class="qtp-note-word-chip">
                            <div>
                                <div class="qtp-note-word-label">{{ __('Related Word') }}</div>
                                <div class="qtp-note-word-ar">{{ $noteWord?->text ?? '—' }}</div>
                            </div>
                        </div>
                    @endif
                    <div class="qtp-nv-note-body">{{ $note->content }}</div>
                    @if($note->tags->isNotEmpty())
                        <div class="qtp-note-tags" style="margin-top: 8px;">
                            @foreach($note->tags as $tag)
                                <span class="qtp-tag" style="cursor:default; font-size:11px;">#{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="qtp-notes-empty">
                    <i class="ti ti-notes-off"></i>
                    <p>{{ __('No notes for this verse yet.') }}</p>
                    <button
                        type="button"
                        @click="showNotesViewer = false; showNoteModal = true"
                        class="qtp-btn primary"
                    >{{ __('Take Your First Note') }}</button>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Not Modalı --}}
<div
    x-show="showNoteModal"
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="qtp-modal-backdrop"
    @click.self="showNoteModal = false"
    role="dialog"
    aria-modal="true"
    style="display: none;"
>
    <div class="qtp-modal" @click.stop>
        <div class="qtp-modal-head">
            <h2 class="qtp-modal-title">
                {{ $editingNoteId ? __('Edit Note') : __('New Note') }}
                <span style="font-size:13px; font-weight:400; color:var(--text-light); margin-left:8px;">
                    {{ $this->currentSuraName }} · {{ $selectedSura }}:{{ $selectedAya }}
                </span>
            </h2>
            <button type="button" @click="showNoteModal = false" class="qtp-modal-close" title="{{ __('Close') }}">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <div class="qtp-modal-body">
            <div class="qtp-form-grid">
                <label class="qtp-field">
                    {{ __('Type') }}
                    <select wire:model.live="noteType" class="qtp-select">
                        <option value="note">{{ __('Note') }}</option>
                        <option value="footnote">{{ __('Footnote') }}</option>
                        <option value="research">{{ __('Research') }}</option>
                    </select>
                </label>
                <label class="qtp-field">
                    {{ __('Word (optional)') }}
                    <select wire:model.live="noteWordPosition" class="qtp-select">
                        <option value="" @selected(! $noteWordPosition)>{{ __('Whole verse') }}</option>
                        @foreach($this->currentWords as $word)
                            <option value="{{ $word->position }}" @selected($noteWordPosition == $word->position)>
                                {{ $word->text }} ({{ $word->position }})
                            </option>
                        @endforeach
                    </select>
                </label>
                <label class="qtp-field">
                    {{ __('Tags') }}
                    <select wire:model="selectedTagIds" multiple class="qtp-select" style="min-height: 80px;">
                        @foreach($this->userTags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <input
                wire:model.defer="noteTitle"
                type="text"
                class="qtp-input"
                placeholder="{{ __('Note title *') }}"
                style="width: 100%;"
            >
            @error('noteTitle') <div class="qtp-error">{{ $message }}</div> @enderror

            <textarea
                wire:model.live="noteContent"
                class="qtp-textarea"
                placeholder="{{ __('Write your note...') }}"
            ></textarea>
            @error('noteContent') <div class="qtp-error">{{ $message }}</div> @enderror

            @if($noteContent !== '')
                <div class="qtp-preview">
                    <div class="qtp-preview-label">{{ __('Preview') }}</div>
                    <div class="qtp-preview-title">{{ $noteTitle ?: __('No title entered') }}</div>
                    <div class="qtp-preview-body">{!! nl2br(e($noteContent)) !!}</div>
                </div>
            @endif
        </div>

        <div class="qtp-modal-foot">
            @if($editingNoteId)
                <button wire:click="updateNote" wire:loading.attr="disabled" type="button" class="qtp-btn primary">
                    <span wire:loading.remove wire:target="updateNote"><i class="ti ti-device-floppy"></i> {{ __('Update') }}</span>
                    <span wire:loading wire:target="updateNote">{{ __('Saving...') }}</span>
                </button>
                <button wire:click="resetNoteForm" @click="showNoteModal = false" type="button" class="qtp-btn">
                    {{ __('Discard') }}
                </button>
            @else
                <button wire:click="saveNote" wire:loading.attr="disabled" type="button" class="qtp-btn primary">
                    <span wire:loading.remove wire:target="saveNote"><i class="ti ti-device-floppy"></i> {{ __('Save') }}</span>
                    <span wire:loading wire:target="saveNote">{{ __('Saving...') }}</span>
                </button>
                <button type="button" @click="showNoteModal = false" class="qtp-btn">{{ __('Cancel') }}</button>
            @endif
        </div>
    </div>
</div>

</div>
