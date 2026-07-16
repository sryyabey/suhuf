<div
  class="qpr"
  style="--arabic-font-family: {{ $this->arabicFontFamily }};"
  x-data="{
    controlsOpen: window.innerWidth > 768,
    readerFs: false,
    init() {
      const sync = () => {
        if (window.innerWidth > 768) {
          this.controlsOpen = true;
        }
      };

      sync();
      window.addEventListener('resize', sync);
      document.addEventListener('fullscreenchange', () => {
        this.readerFs = document.fullscreenElement === this.$refs.readerShell;
      });
    },
    toggleReaderFullscreen() {
      const shell = this.$refs.readerShell;

      if (!shell) {
        return;
      }

      if (document.fullscreenElement === shell) {
        document.exitFullscreen?.();
        return;
      }

      shell.requestFullscreen?.();
    }
  }"
>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Noto+Naskh+Arabic:wght@400;600;700&family=Scheherazade+New:wght@400;700&display=swap');

    .qpr { max-width: 860px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }

    /* ── Header ─────────────────────────────────────────────── */
    .qpr-header { display: flex; justify-content: space-between; align-items: flex-end; gap: 10px; flex-wrap: wrap; }
    .qpr-title  { margin: 0; font-family: 'Cairo', sans-serif; font-size: 21px; color: var(--teal-dark); font-weight: 700; }
    .qpr-sub    { margin: 2px 0 0; font-size: 12px; color: var(--text-light); font-family: 'Cairo', sans-serif; }
    .qpr-mobile-actions { display: none; gap: 8px; }
    .qpr-mobile-btn {
      border: 1px solid var(--border-strong);
      background: #fff;
      color: var(--teal-dark);
      border-radius: 10px;
      padding: 8px 12px;
      font-family: 'Cairo', sans-serif;
      font-size: 12.5px;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .qpr-controls-stack { display: flex; flex-direction: column; gap: 12px; }

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
      background: linear-gradient(180deg, #fdfaf3 0%, #f9f4e8 50%, #fdfaf3 100%);
      border: 1px solid rgba(184,134,11,.3);
      outline: 5px solid rgba(184,134,11,.07);
      outline-offset: -10px;
      border-radius: 4px;
      padding: 36px 40px 32px;
      box-shadow:
        inset 0 0 0 1px rgba(255,255,255,.55),
        6px 0 14px rgba(66,47,11,.06),
        -6px 0 14px rgba(66,47,11,.06),
        0 10px 28px rgba(66,47,11,.1);
      position: relative;
    }
    .qpr-reader-shell:fullscreen,
    .qpr-reader-shell:-webkit-full-screen {
      background: var(--cream);
      padding: 16px;
      overflow: auto;
    }
    .qpr-reader-shell { position: relative; }
    .qpr-fs-exit {
      display: none;
      position: sticky;
      top: 8px;
      margin-left: auto;
      z-index: 5;
      border: 1px solid rgba(45,155,132,.2);
      background: rgba(255,255,255,.96);
      color: var(--teal-dark);
      border-radius: 999px;
      padding: 8px 12px;
      font-family: 'Cairo', sans-serif;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      align-items: center;
      gap: 6px;
      box-shadow: 0 8px 20px rgba(0,0,0,.08);
    }
    .qpr-reader-shell:fullscreen .qpr-fs-exit,
    .qpr-reader-shell:-webkit-full-screen .qpr-fs-exit {
      display: inline-flex;
    }
    .qpr-reader-shell:fullscreen .qpr-page,
    .qpr-reader-shell:-webkit-full-screen .qpr-page {
      max-width: 980px;
      margin: 0 auto;
      min-height: calc(100vh - 32px);
    }
    /* Köşe süslemeleri */
    .qpr-page::before, .qpr-page::after {
      content: '';
      position: absolute;
      width: 20px; height: 20px;
      border-color: rgba(184,134,11,.45);
      border-style: solid;
    }
    .qpr-page::before { top: 12px; right: 12px; border-width: 2px 2px 0 0; border-radius: 0 3px 0 0; }
    .qpr-page::after  { bottom: 12px; left: 12px;  border-width: 0 0 2px 2px; border-radius: 0 0 0 3px; }

    /* Sayfa başı — ortada sayfa numarası ve sure aralığı */
    .qpr-page-head {
      text-align: center;
      margin-bottom: 22px;
      padding-bottom: 14px;
      border-bottom: 1px solid rgba(184,134,11,.22);
    }
    .qpr-page-num {
      font-family: var(--arabic-font-family);
      font-size: 16px;
      font-weight: 700;
      color: #8a6522;
      display: inline-flex;
      align-items: center;
      gap: 12px;
    }
    .qpr-page-num::before, .qpr-page-num::after {
      content: '❧';
      font-size: 13px;
      opacity: .55;
      color: #b89647;
    }
    .qpr-page-range {
      font-family: 'Cairo', sans-serif;
      font-size: 11px;
      color: var(--text-light);
      margin-top: 4px;
    }

    /* ── Akan Mushaf metni ── */
    /* Gerçek mushaf gibi: tüm ayetler tek blok, sağdan sola akar */
    .qpr-mushaf {
      direction: rtl;
      display: block;
      font-family: var(--arabic-font-family);
      font-weight: 400;
      font-feature-settings: "liga" 1, "calt" 1, "kern" 1;
      font-kerning: normal;
      text-rendering: optimizeLegibility;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      font-size: 30px;
      line-height: 2.3;
      color: #1a1208;
      text-align: right;
      text-align-last: auto;
      word-spacing: normal;
    }

    /* Her ayet inline — blok değil, metin akar */
    .qpr-ayah { display: inline; }
    .qpr-ayah-btn {
      display: inline;
      border: 0; background: transparent; padding: 0 1px; margin: 0;
      cursor: pointer; color: inherit; font: inherit; line-height: inherit;
      direction: inherit;
      border-radius: 3px;
      transition: background .12s;
    }
    .qpr-ayah-btn:hover   { background: rgba(45,155,132,.1); }
    .qpr-ayah-btn:focus   { outline: none; background: rgba(45,155,132,.13); box-shadow: 0 0 0 2px rgba(45,155,132,.28); border-radius: 3px; }

    /* Not rozeti — ayetin yanında üst simge olarak */
    .qpr-note-badge {
      display: inline-flex;
      align-items: center;
      vertical-align: baseline;
      gap: 2px;
      background: var(--teal-dark);
      color: #fff;
      font-family: 'Cairo', sans-serif;
      font-size: 9px;
      font-weight: 700;
      padding: 1px 5px;
      border-radius: 999px;
      margin-left: 2px;
      line-height: 1;
      position: relative;
      top: -0.45em;
      pointer-events: none;
    }
    .qpr-note-badge i { font-size: 8px; }

    /* Ayet sonu numarası — altın çember, inline akışta */
    .qpr-marker {
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
      flex-shrink: 0;
    }

    /* Sure geçiş kartı — inline akışı kesmek için block */
    .qpr-sura-sep {
      display: block;
      margin: 24px 0 20px;
      text-align: center;
    }
    .qpr-sura-card {
      display: inline-block;
      min-width: 320px;
      padding: 16px 30px 14px;
      border: 1px solid rgba(184,134,11,.42);
      border-radius: 6px;
      background:
        radial-gradient(circle at 50% -30px, rgba(212,168,67,.18), transparent 55%),
        linear-gradient(180deg, #fffaf0 0%, #f7eddc 52%, #fffaf0 100%);
      box-shadow:
        0 2px 8px rgba(184,134,11,.1),
        inset 0 0 0 1px rgba(255,255,255,.5);
      position: relative;
      overflow: hidden;
    }
    .qpr-sura-card::before,
    .qpr-sura-card::after {
      content: '';
      position: absolute;
      width: 14px; height: 14px;
      border-color: rgba(184,134,11,.5);
      border-style: solid;
      pointer-events: none;
    }
    .qpr-sura-card::before {
      top: 8px; right: 8px;
      border-width: 1.5px 1.5px 0 0;
      border-radius: 0 3px 0 0;
    }
    .qpr-sura-card::after {
      bottom: 8px; left: 8px;
      border-width: 0 0 1.5px 1.5px;
      border-radius: 0 0 0 3px;
    }
    .qpr-sura-card-ornrow {
      display: flex; align-items: center; justify-content: center; gap: 10px;
      margin-bottom: 8px;
    }
    .qpr-sura-card-orn  { color: #b89647; font-size: 16px; line-height: 1; }
    .qpr-sura-card-line { flex: 1; height: 1px; background: linear-gradient(90deg, transparent, rgba(184,134,11,.4), transparent); }
    .qpr-sura-card-name {
      font-family: var(--arabic-font-family);
      font-size: 26px;
      color: #5a3e0e;
      letter-spacing: .2px;
      margin-bottom: 9px;
      display: block;
      line-height: 1.35;
      text-shadow: 0 1px 0 rgba(255,255,255,.55);
    }
    .qpr-sura-card-num {
      font-family: 'Cairo', sans-serif;
      font-size: 11px;
      color: #b89647;
      margin-bottom: 10px;
      display: block;
    }
    .qpr-sura-card-stats {
      display: flex; justify-content: center; gap: 10px;
      flex-wrap: wrap;
    }
    .qpr-sura-card-stat {
      min-width: 74px;
      display: flex; flex-direction: column; align-items: center; gap: 3px;
      padding: 7px 8px 6px;
      background: rgba(255,255,255,.55);
      border: 1px solid rgba(184,134,11,.22);
      border-radius: 8px;
    }
    .qpr-sura-card-stat-val {
      font-family: 'Cairo', sans-serif;
      font-size: 16px;
      font-weight: 700;
      color: #5a3e0e;
      line-height: 1;
    }
    .qpr-sura-card-stat-lbl {
      font-family: 'Cairo', sans-serif;
      font-size: 9.5px;
      color: #a07830;
      text-transform: uppercase;
      letter-spacing: .5px;
    }
    .qpr-sura-card-divider {
      width: 1px; height: 42px;
      background: linear-gradient(180deg, transparent, rgba(184,134,11,.35), transparent);
      align-self: center;
    }

    /* ── Besmele ─────────────────────────────────────────────── */
    .qpr-besmele {
      display: block;
      text-align: center;
      direction: rtl;
      font-family: var(--arabic-font-family);
      font-size: 26px;
      font-weight: 400;
      font-feature-settings: "liga" 1, "calt" 1, "kern" 1;
      text-rendering: optimizeLegibility;
      color: #3d2a08;
      line-height: 2;
      margin: 2px 0 18px;
      padding: 10px 24px 8px;
      background: linear-gradient(90deg, transparent, rgba(184,134,11,.06) 30%, rgba(184,134,11,.06) 70%, transparent);
      border-top: 1px solid rgba(184,134,11,.2);
      border-bottom: 1px solid rgba(184,134,11,.2);
      letter-spacing: .3px;
    }

    /* ── Navigation ──────────────────────────────────────────── */
    .qpr-nav {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      margin-top: 24px;
      padding-top: 16px;
      border-top: 1px solid rgba(184,134,11,.18);
    }
    .qpr-nav-btn {
      border: 1px solid rgba(184,134,11,.35);
      background: rgba(253,250,243,.8);
      color: #7a5a1e;
      border-radius: 4px;
      padding: 7px 16px;
      font-family: 'Cairo', sans-serif;
      font-size: 13px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: background .13s, color .13s, border-color .13s;
    }
    .qpr-nav-btn:hover:not(:disabled) {
      background: rgba(212,168,67,.15);
      color: #5a3e0e;
      border-color: rgba(184,134,11,.6);
    }
    .qpr-nav-btn:disabled { opacity: .4; cursor: not-allowed; }
    .qpr-nav-center {
      font-family: var(--arabic-font-family);
      font-size: 15px;
      color: #8a6522;
      padding: 4px 16px;
      border: 1px solid rgba(184,134,11,.25);
      border-radius: 4px;
      background: rgba(253,250,243,.5);
      min-width: 60px;
      text-align: center;
    }

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
      font-family: var(--arabic-font-family);
      font-weight: 400;
      font-feature-settings: "liga" 1, "calt" 1, "kern" 1;
      text-rendering: optimizeLegibility;
      font-size:26px; line-height:2;
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
    .qprm-word-chip-ar    { display:inline-block; direction:rtl; font-family:var(--arabic-font-family); font-size:26px; color:var(--teal-dark); background:var(--teal-light); border:1px solid rgba(45,155,132,.24); border-radius:9px; padding:2px 10px; line-height:1.65; font-weight: 400; font-feature-settings: "liga" 1, "calt" 1, "kern" 1; text-rendering: optimizeLegibility; }
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

    /* ── Arama'lı Sure Dropdown ─────────────────────────── */
    .qpr-ss-wrap     { position: relative; }
    .qpr-ss-trigger  {
      width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 8px;
      border: 1px solid var(--border-strong); border-radius: 9px;
      padding: 8px 10px; font-family: 'Cairo', sans-serif; font-size: 13.5px;
      background: #fff; color: var(--text-dark); cursor: pointer; text-align: left;
      transition: border-color .15s, box-shadow .15s;
    }
    .qpr-ss-trigger:focus, .qpr-ss-trigger.open {
      outline: none; border-color: var(--teal-mid); box-shadow: 0 0 0 3px rgba(45,155,132,.12);
    }
    .qpr-ss-value    { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; text-align: left; }
    .qpr-ss-dropdown {
      position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 200;
      background: #fff; border: 1px solid var(--border-strong); border-radius: 10px;
      box-shadow: 0 8px 24px rgba(0,0,0,.12);
      display: flex; flex-direction: column; overflow: hidden;
    }
    .qpr-ss-search-wrap {
      display: flex; align-items: center; gap: 8px;
      padding: 8px 10px; border-bottom: 1px solid var(--border);
    }
    .qpr-ss-search-icon { color: var(--text-light); font-size: 14px; flex-shrink: 0; }
    .qpr-ss-search  {
      flex: 1; border: none; outline: none; font-family: 'Cairo', sans-serif;
      font-size: 13px; color: var(--text-dark); background: transparent;
    }
    .qpr-ss-list    {
      list-style: none; margin: 0; padding: 4px 0;
      max-height: 220px; overflow-y: auto;
    }
    .qpr-ss-option  {
      padding: 8px 12px; font-family: 'Cairo', sans-serif; font-size: 13px;
      color: var(--text-dark); cursor: pointer; transition: background .1s;
    }
    .qpr-ss-option:hover  { background: var(--teal-light); color: var(--teal-dark); }
    .qpr-ss-option.active { background: var(--teal-dark); color: #fff; font-weight: 700; }
    .qpr-ss-empty   {
      padding: 10px 12px; font-family: 'Cairo', sans-serif; font-size: 13px;
      color: var(--text-light); text-align: center;
    }

    @media (max-width:768px) {
      .qpr-header { align-items: stretch; }
      .qpr-mobile-actions { display: flex; flex-wrap: wrap; }
      .qpr-controls-stack { display: none; }
      .qpr-controls-stack.is-open { display: flex; }
      .qpr-toolbar  { grid-template-columns:1fr; }
      .qpr-mushaf   { font-size:24px; line-height:2.15; }
      .qpr-page     { padding:20px 18px; }
      .qpr-page::before, .qpr-page::after { width:14px; height:14px; }
      .qprm-aya-arabic { font-size:22px; }
      .qprm-type-row { grid-template-columns:1fr; }
    }
  </style>

  {{-- ── Page header ─────────────────────────────────────────────────── --}}
  <div class="qpr-header">
    <div>
      <h1 class="qpr-title">{{ __('Quran Reading') }}</h1>
      <p class="qpr-sub">{{ __('qr_subtitle') }}</p>
    </div>
    <div class="qpr-mobile-actions">
      <button type="button" class="qpr-mobile-btn" @click="controlsOpen = !controlsOpen">
        <i class="ti" :class="controlsOpen ? 'ti-layout-sidebar-right-collapse' : 'ti-layout-sidebar-right-expand'"></i>
        <span x-text="controlsOpen ? '{{ __('Hide Controls') }}' : '{{ __('Show Controls') }}'"></span>
      </button>
      <button type="button" class="qpr-mobile-btn" @click="toggleReaderFullscreen()">
        <i class="ti" :class="readerFs ? 'ti-minimize' : 'ti-maximize'"></i>
        <span x-text="readerFs ? '{{ __('Exit Full Screen') }}' : '{{ __('Full Screen') }}'"></span>
      </button>
    </div>
  </div>

  {{-- ── Toolbar ──────────────────────────────────────────────────────── --}}
  {{-- JSON'u script tag içinde tanımlıyoruz — x-data attribute içinde " tırnak çakışması yaşanmaz --}}
  <script>window.__qprSuraOptions = @json($suraOptions);</script>

  <div class="qpr-controls-stack" :class="{ 'is-open': controlsOpen }">
  <div class="qpr-toolbar">

    {{-- Sure: arama özellikli custom dropdown --}}
    <div
      class="qpr-field"
      x-data="{
        open: false,
        search: '',
        options: window.__qprSuraOptions || [],
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
      <span class="qpr-label">{{ __('Sura') }}</span>
      <div class="qpr-ss-wrap">
        <button
          type="button"
          class="qpr-ss-trigger"
          :class="{ open }"
          @click="open = !open"
        >
          <span class="qpr-ss-value" x-text="selectedLabel"></span>
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
          class="qpr-ss-dropdown"
          style="display:none;"
          @click.stop
        >
          <div class="qpr-ss-search-wrap">
            <i class="ti ti-search qpr-ss-search-icon"></i>
            <input
              x-ref="suraSrch"
              x-model="search"
              type="text"
              class="qpr-ss-search"
              placeholder="{{ __('Search sura...') }}"
              @keydown.escape.stop="open = false"
            >
          </div>
          <ul class="qpr-ss-list">
            <template x-for="opt in filtered" :key="opt.value">
              <li
                class="qpr-ss-option"
                :class="{ active: opt.value === $wire.selectedSura }"
                @click="choose(opt.value)"
                x-text="opt.label"
              ></li>
            </template>
            <li x-show="filtered.length === 0" class="qpr-ss-empty">{{ __('No results.') }}</li>
          </ul>
        </div>
      </div>
    </div>
    <label class="qpr-field">
      <span class="qpr-label">{{ __('Verse') }}</span>
      <select wire:model.live="selectedAya" class="qpr-select">
        @foreach ($ayaOptions as $opt)
          <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
        @endforeach
      </select>
    </label>
    <label class="qpr-field">
      <span class="qpr-label">{{ __('Page') }}</span>
      <select wire:model.live="selectedPage" class="qpr-select">
        @foreach ($pageOptions as $p)
          <option value="{{ $p }}">{{ __('page_ref', ['number' => $p]) }}</option>
        @endforeach
      </select>
    </label>
  </div>

  {{-- ── Bookmarks ─────────────────────────────────────────────────────── --}}
  <div class="qpr-bookmarks">
    <div class="qpr-bookmarks-head">
      <div class="qpr-bookmarks-title">
        <i class="ti ti-bookmark"></i> {{ __('Bookmarks') }}
      </div>
      <div class="qpr-bookmark-form">
        <input type="text" wire:model="bookmarkLabel" class="qpr-bookmark-input" placeholder="{{ __('Label (optional)') }}">
        <button type="button" wire:click="addBookmark" class="qpr-bookmark-btn">
          <i class="ti ti-bookmark-plus"></i> {{ __('Save This Page') }}
        </button>
        @if($this->isCurrentPageBookmarked)
          <button type="button" wire:click="removeBookmark({{ $selectedPage }})" class="qpr-bookmark-btn alt">
            <i class="ti ti-bookmark-off"></i> {{ __('Remove Bookmark') }}
          </button>
        @endif
      </div>
    </div>
    <div class="qpr-bookmarks-list">
      @forelse ($this->bookmarks as $bm)
        <div class="qpr-bookmark-pill">
          <button type="button" class="qpr-bookmark-go" wire:click="$set('selectedPage', {{ $bm->page }})">
            {{ __('page_ref', ['number' => $bm->page]) }}@if($bm->label) · {{ $bm->label }}@endif
          </button>
          <button type="button" class="qpr-bookmark-del" wire:click="removeBookmark({{ $bm->page }})" title="{{ __('Remove') }}">×</button>
        </div>
      @empty
        <span class="qprm-empty" style="font-size:12px;">{{ __('No bookmarks yet.') }}</span>
      @endforelse
    </div>
  </div>
  </div>

  {{-- ── Mushaf page ──────────────────────────────────────────────────── --}}
  <div class="qpr-reader-shell" x-ref="readerShell">
  <button
    type="button"
    class="qpr-fs-exit"
    @click="toggleReaderFullscreen()"
  >
    <i class="ti ti-x"></i>
    {{ __('Exit Full Screen') }}
  </button>
  <div
    class="qpr-page"
    wire:loading.style="opacity:0.65;transition:opacity .2s"
    wire:target="selectedPage,prevPage,nextPage,selectedSura,selectedAya"
  >
    <div class="qpr-page-head">
      <div class="qpr-page-num">{{ __('page_ref', ['number' => $selectedPage]) }}</div>
      @php $firstRow = $rows->first(); $lastRow = $rows->last(); @endphp
      @if($firstRow && $lastRow)
        <div class="qpr-page-range">
          {{ $firstRow['sura_name'] }} {{ $firstRow['sura'] }}:{{ $firstRow['aya'] }}
          @if($lastRow['sura'] !== $firstRow['sura'] || $lastRow['aya'] !== $firstRow['aya'])
            — {{ $lastRow['sura_name'] }} {{ $lastRow['sura'] }}:{{ $lastRow['aya'] }}
          @endif
        </div>
      @endif
    </div>

    <div class="qpr-mushaf">
      @forelse ($rows as $row)
        @php
          $prev = $rows[$loop->index - 1] ?? null;
          $isSuraStart = $loop->first || (($prev['sura'] ?? null) !== $row['sura']);
        @endphp

        @if(!$loop->first && $isSuraStart)
          @php $st = $suraStats[$row['sura']] ?? null; @endphp
          <div class="qpr-sura-sep">
            <div class="qpr-sura-card">
              <div class="qpr-sura-card-ornrow">
                <span class="qpr-sura-card-line"></span>
                <span class="qpr-sura-card-orn">✦</span>
                <span class="qpr-sura-card-line"></span>
              </div>
              <span class="qpr-sura-card-num">{{ $row['sura'] }}. {{ __('Sura') }}</span>
              <span class="qpr-sura-card-name">{{ $row['sura_name'] }}</span>
              @if($st)
                <div class="qpr-sura-card-stats">
                  <div class="qpr-sura-card-stat">
                    <span class="qpr-sura-card-stat-val">{{ $st['aya_count'] }}</span>
                    <span class="qpr-sura-card-stat-lbl">{{ __('Verse') }}</span>
                  </div>
                  <div class="qpr-sura-card-divider"></div>
                  <div class="qpr-sura-card-stat">
                    <span class="qpr-sura-card-stat-val">{{ number_format($st['word_count']) }}</span>
                    <span class="qpr-sura-card-stat-lbl">{{ __('Word') }}</span>
                  </div>
                  <div class="qpr-sura-card-divider"></div>
                  <div class="qpr-sura-card-stat">
                    <span class="qpr-sura-card-stat-val">{{ number_format($st['char_count']) }}</span>
                    <span class="qpr-sura-card-stat-lbl">{{ __('Letter') }}</span>
                  </div>
                </div>
              @endif
            </div>
          </div>
        @endif

        {{-- Besmele: her surenin 1. ayetinden önce göster (Sure 1 ve Sure 9 hariç) --}}
        @if($row['aya'] == 1 && $row['sura'] != 1 && $row['sura'] != 9)
          <div class="qpr-besmele">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
        @endif

        <span class="qpr-ayah">
          {{-- Not rozeti: inline, ayetin başında üst simge olarak --}}
          @if(($row['note_count'] ?? 0) > 0)
            <span class="qpr-note-badge" title="{{ $row['note_count'] }} not">
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
        <p class="qprm-empty"><i class="ti ti-mood-empty"></i> {{ __('No verses found on this page.') }}</p>
      @endforelse
    </div>

    <div class="qpr-nav">
      <button type="button" class="qpr-nav-btn" wire:click="nextPage" wire:loading.attr="disabled" wire:target="nextPage">
        <span wire:loading.remove wire:target="nextPage"><i class="ti ti-arrow-right"></i> {{ __('Next Page') }}</span>
        <span wire:loading wire:target="nextPage">…</span>
      </button>
      <span class="qpr-nav-center" wire:loading.class="opacity-50">{{ $selectedPage }}</span>
      <button type="button" class="qpr-nav-btn" wire:click="prevPage" wire:loading.attr="disabled" wire:target="prevPage">
        <span wire:loading.remove wire:target="prevPage">{{ __('Previous Page') }} <i class="ti ti-arrow-left"></i></span>
        <span wire:loading wire:target="prevPage">…</span>
      </button>
    </div>
  </div>
  </div>

  {{-- ── Notes modal ──────────────────────────────────────────────────── --}}
  @if($showNotesModal)
    @php
      $typeLabels = ['note' => __('Note'), 'footnote' => __('Footnote'), 'research' => __('Research')];
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
            {{ $SURA_NAMES[$modalSura] ?? (__('Sura prefix') . " {$modalSura}") }}
            <span class="qprm-ref">{{ $modalSura }}:{{ $modalAya }}</span>
          </div>
          <button type="button" class="qprm-close" wire:click="closeNotesModal" aria-label="{{ __('Close') }}">
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
            {{ __('Notes') }}
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
            {{ __('Add Note') }}
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
                    <div class="qprm-word-chip-label">{{ __('Related Word') }}</div>
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
                {{ __('No notes found for this verse.') }}
              </div>
            @endforelse
          </div>

        {{-- ── ADD NOTE tab ────────────────────────────────────── --}}
        @elseif($modalTab === 'add')
          <div class="qprm-body">
            <div class="qprm-form">

              {{-- Type --}}
              <div class="qprm-form-row">
                <label class="qprm-form-label">{{ __('Note Type') }}</label>
                <div class="qprm-type-row">
                  <label class="qprm-type-opt">
                    <input type="radio" wire:model="newNoteType" value="note">
                    <span class="qprm-type-btn t-note">
                      <i class="ti ti-pencil" style="font-size:13px;"></i> {{ __('Note') }}
                    </span>
                  </label>
                  <label class="qprm-type-opt">
                    <input type="radio" wire:model="newNoteType" value="footnote">
                    <span class="qprm-type-btn t-footnote">
                      <i class="ti ti-bookmark" style="font-size:13px;"></i> {{ __('Footnote') }}
                    </span>
                  </label>
                  <label class="qprm-type-opt">
                    <input type="radio" wire:model="newNoteType" value="research">
                    <span class="qprm-type-btn t-research">
                      <i class="ti ti-microscope" style="font-size:13px;"></i> {{ __('Research') }}
                    </span>
                  </label>
                </div>
              </div>

              {{-- Title --}}
              <div class="qprm-form-row">
                <label class="qprm-form-label">{{ __('Title') }} <span style="font-weight:400;text-transform:none;letter-spacing:0;">{{ __('(optional)') }}</span></label>
                <input
                  type="text"
                  wire:model="newNoteTitle"
                  class="qprm-form-input"
                  placeholder="{{ __('Note title...') }}"
                >
              </div>

              {{-- Content --}}
              <div class="qprm-form-row">
                <label class="qprm-form-label">{{ __('Content') }} <span style="color:#c0392b;">*</span></label>
                <textarea
                  wire:model="newNoteContent"
                  class="qprm-form-textarea"
                  placeholder="{{ __('Write your note here...') }}"
                  rows="4"
                ></textarea>
                @error('newNoteContent')
                  <span class="qprm-error"><i class="ti ti-alert-circle" style="font-size:12px;"></i> {{ $message }}</span>
                @enderror
              </div>

              {{-- Word position --}}
              @if($this->modalWords->isNotEmpty())
                <div class="qprm-form-row">
                  <label class="qprm-form-label">{{ __('Related Word') }} <span style="font-weight:400;text-transform:none;letter-spacing:0;">{{ __('(optional)') }}</span></label>
                  <select wire:model="newNoteWordPos" class="qprm-form-select">
                    <option value="">{{ __('— Whole verse —') }}</option>
                    @foreach($this->modalWords as $word)
                      <option value="{{ $word->position }}">
                        {{ $word->text }}  ({{ __('word_ref', ['number' => $word->position]) }})
                      </option>
                    @endforeach
                  </select>
                </div>
              @endif

              {{-- Tags --}}
              @if($this->userTags->isNotEmpty() || true)
                <div class="qprm-form-row">
                  <label class="qprm-form-label">{{ __('Tags') }}</label>
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
                      placeholder="{{ __('Create new tag...') }}"
                    >
                    <button
                      type="button"
                      wire:click="createAndAttachTag"
                      class="qpr-bookmark-btn"
                      style="white-space:nowrap;"
                    >
                      <i class="ti ti-plus"></i> {{ __('Add') }}
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
                  <i class="ti ti-device-floppy"></i> {{ __('Save Note') }}
                </span>
                <span wire:loading wire:target="saveNote">{{ __('Saving…') }}</span>
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
            {{ __('Open in note editing page') }}
          </a>
        </div>

      </div>
    </div>
  @endif

</div>
