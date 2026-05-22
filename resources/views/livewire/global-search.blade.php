<div
    x-data="{
        open: false,
        close() { this.open = false; },
        handleKey(e) {
            if (e.key === 'Escape') { this.close(); return; }
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const first = $refs.dropdown?.querySelector('.gs-item');
                first?.focus();
            }
        },
        focusNext(e) {
            e.preventDefault();
            const items = [...$refs.dropdown.querySelectorAll('.gs-item')];
            const idx   = items.indexOf(document.activeElement);
            items[idx + 1]?.focus();
        },
        focusPrev(e) {
            e.preventDefault();
            const items = [...$refs.dropdown.querySelectorAll('.gs-item')];
            const idx   = items.indexOf(document.activeElement);
            if (idx <= 0) { $refs.input.focus(); }
            else { items[idx - 1]?.focus(); }
        },
    }"
    @click.outside="close()"
    @keydown.escape.window="close()"
    style="position:relative;"
>

    {{-- ── Search box ──────────────────────────────────────────────────── --}}
    <div class="gs-box">
        <i class="ti ti-search gs-icon" aria-hidden="true"></i>
        <input
            x-ref="input"
            type="text"
            wire:model.live.debounce.320ms="query"
            placeholder="Sure, ayet veya konu ara…"
            class="gs-input"
            autocomplete="off"
            spellcheck="false"
            @focus="open = true"
            @input="open = true"
            @keydown="handleKey($event)"
            aria-label="Ara"
            aria-haspopup="listbox"
            :aria-expanded="open"
        >
        <button
            x-show="$wire.query.length > 0"
            x-cloak
            @click="$wire.set('query', ''); close(); $refs.input.focus();"
            class="gs-clear"
            type="button"
            aria-label="Aramayı temizle"
        >
            <i class="ti ti-x"></i>
        </button>
    </div>

    {{-- ── Dropdown ────────────────────────────────────────────────────── --}}
    <div
        x-ref="dropdown"
        x-show="open && $wire.query.length >= 2"
        x-cloak
        x-transition:enter="gs-fade-enter"
        x-transition:enter-start="gs-fade-enter-start"
        x-transition:enter-end="gs-fade-enter-end"
        x-transition:leave="gs-fade-leave"
        x-transition:leave-start="gs-fade-leave-start"
        x-transition:leave-end="gs-fade-leave-end"
        class="gs-dropdown"
        role="listbox"
        @keydown.arrow-down="focusNext($event)"
        @keydown.arrow-up="focusPrev($event)"
    >
        {{-- Loading spinner --}}
        <div wire:loading wire:target="query" class="gs-loading">
            <span class="gs-spinner"></span>
            <span>Aranıyor…</span>
        </div>

        <div wire:loading.remove wire:target="query">
            @php $r = $this->results; @endphp

            {{-- Empty state --}}
            @if(! $r['hasAny'])
                <div class="gs-empty">
                    <i class="ti ti-mood-empty"></i>
                    <span>Sonuç bulunamadı</span>
                </div>
            @endif

            {{-- ── Suras ─────────────────────────────────────────── --}}
            @if(count($r['suras']))
                <div class="gs-section-label">
                    <i class="ti ti-book-2"></i> Sureler
                </div>
                @foreach($r['suras'] as $sura)
                    <a
                        href="{{ route('user.quran-text') }}?sura={{ $sura['num'] }}&aya=1"
                        class="gs-item"
                        tabindex="0"
                        role="option"
                        @click="close()"
                    >
                        <span class="gs-num-badge">{{ $sura['num'] }}</span>
                        <span class="gs-item-label">{{ $sura['name'] }}</span>
                        <span class="gs-item-hint">Sure</span>
                    </a>
                @endforeach
            @endif

            {{-- ── Notes ─────────────────────────────────────────── --}}
            @if(count($r['notes']))
                <div class="gs-section-label">
                    <i class="ti ti-notes"></i> Notlarım
                </div>
                @foreach($r['notes'] as $note)
                    <a
                        href="{{ route('user.quran-text') }}?sura={{ $note['sura'] }}&aya={{ $note['aya'] }}"
                        class="gs-item"
                        tabindex="0"
                        role="option"
                        @click="close()"
                    >
                        <span class="gs-type-dot gs-type-{{ $note['type'] }}"></span>
                        <span class="gs-item-label gs-item-label--note">{{ $note['label'] }}</span>
                        <span class="gs-item-hint">{{ $note['sura_name'] }} {{ $note['ref'] }}</span>
                    </a>
                @endforeach
            @endif

            {{-- ── Tags ──────────────────────────────────────────── --}}
            @if(count($r['tags']))
                <div class="gs-section-label">
                    <i class="ti ti-tag"></i> Etiketler
                </div>
                @foreach($r['tags'] as $tag)
                    <a
                        href="{{ route('user.quran-notes-range') }}?tag={{ $tag['id'] }}"
                        class="gs-item"
                        tabindex="0"
                        role="option"
                        @click="close()"
                    >
                        <span class="gs-tag-icon"><i class="ti ti-hash"></i></span>
                        <span class="gs-item-label">{{ $tag['name'] }}</span>
                        <span class="gs-item-hint">Etiket</span>
                    </a>
                @endforeach
            @endif
        </div>

        <div class="gs-footer">
            <span><kbd>↑↓</kbd> gezin</span>
            <span><kbd>Enter</kbd> aç</span>
            <span><kbd>Esc</kbd> kapat</span>
        </div>
    </div>

</div>

<style>
  [x-cloak] { display: none !important; }

  /* ── Search box ──────────────────────────────────────────────── */
  .gs-box {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 10px;
    padding: 7px 14px;
    width: 240px;
    transition: background 0.15s, border-color 0.15s;
  }
  .gs-box:focus-within {
    background: rgba(255,255,255,0.13);
    border-color: rgba(255,255,255,0.22);
  }
  .gs-icon { color: rgba(255,255,255,0.45); font-size: 15px; flex-shrink: 0; }
  .gs-input {
    background: none;
    border: none;
    outline: none;
    color: #fff;
    font-family: 'Cairo', sans-serif;
    font-size: 13px;
    width: 100%;
  }
  .gs-input::placeholder { color: rgba(255,255,255,0.35); }
  .gs-clear {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    color: rgba(255,255,255,0.45);
    font-size: 14px;
    line-height: 1;
    flex-shrink: 0;
    transition: color 0.15s;
  }
  .gs-clear:hover { color: rgba(255,255,255,0.8); }

  /* ── Dropdown ────────────────────────────────────────────────── */
  .gs-dropdown {
    position: fixed;
    top: 64px;
    right: 1.5rem;
    width: 420px;
    max-height: 480px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid rgba(184,134,11,0.2);
    border-top: 2px solid var(--teal-mid);
    border-radius: 0 0 14px 14px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.14), 0 2px 10px rgba(0,0,0,0.07);
    z-index: 9999;
    scrollbar-width: thin;
    scrollbar-color: var(--sand) transparent;
  }
  .gs-dropdown::-webkit-scrollbar { width: 4px; }
  .gs-dropdown::-webkit-scrollbar-thumb { background: var(--sand); border-radius: 4px; }

  /* transitions */
  .gs-fade-enter, .gs-fade-leave { transition: opacity 0.15s ease, transform 0.15s ease; }
  .gs-fade-enter-start, .gs-fade-leave-end { opacity: 0; transform: translateY(-6px); }
  .gs-fade-enter-end, .gs-fade-leave-start { opacity: 1; transform: translateY(0); }

  /* ── Loading ─────────────────────────────────────────────────── */
  .gs-loading {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 1.1rem 1.1rem;
    font-family: 'Cairo', sans-serif;
    font-size: 13px;
    color: var(--text-light);
  }
  .gs-spinner {
    width: 16px; height: 16px;
    border: 2px solid var(--sand);
    border-top-color: var(--teal-mid);
    border-radius: 50%;
    animation: gs-spin 0.7s linear infinite;
    flex-shrink: 0;
  }
  @keyframes gs-spin { to { transform: rotate(360deg); } }

  /* ── Empty ───────────────────────────────────────────────────── */
  .gs-empty {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 1.25rem 1.1rem;
    font-family: 'Cairo', sans-serif;
    font-size: 13px;
    color: var(--text-light);
  }
  .gs-empty i { font-size: 18px; }

  /* ── Section labels ──────────────────────────────────────────── */
  .gs-section-label {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px 4px;
    font-family: 'Cairo', sans-serif;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: var(--text-light);
    border-top: 1px solid var(--border);
  }
  .gs-section-label:first-child { border-top: none; }
  .gs-section-label i { font-size: 13px; }

  /* ── Result items ────────────────────────────────────────────── */
  .gs-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 14px;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.1s;
    outline: none;
  }
  .gs-item:hover, .gs-item:focus {
    background: var(--teal-light);
  }

  .gs-num-badge {
    width: 26px; height: 26px;
    border-radius: 50%;
    background: var(--teal-dark);
    color: #fff;
    font-family: 'Cairo', sans-serif;
    font-size: 11px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .gs-type-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
  }
  .gs-type-note     { background: #3b82f6; }
  .gs-type-footnote { background: #f59e0b; }
  .gs-type-research { background: #8b5cf6; }

  .gs-tag-icon {
    width: 26px; height: 26px;
    border-radius: 8px;
    background: var(--gold-pale);
    border: 1px solid var(--border-strong);
    color: var(--gold);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
  }

  .gs-item-label {
    font-family: 'Cairo', sans-serif;
    font-size: 13px;
    color: var(--text-dark);
    font-weight: 500;
    flex: 1;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .gs-item-label--note {
    font-size: 12.5px;
    font-weight: 400;
  }
  .gs-item-hint {
    font-family: 'Cairo', sans-serif;
    font-size: 11px;
    color: var(--text-light);
    white-space: nowrap;
    flex-shrink: 0;
  }

  /* ── Footer ──────────────────────────────────────────────────── */
  .gs-footer {
    display: flex;
    gap: 1rem;
    padding: 7px 14px;
    border-top: 1px solid var(--border);
    background: var(--cream);
    border-radius: 0 0 14px 14px;
  }
  .gs-footer span {
    display: flex;
    align-items: center;
    gap: 4px;
    font-family: 'Cairo', sans-serif;
    font-size: 10.5px;
    color: var(--text-light);
  }
  .gs-footer kbd {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 1px 5px;
    background: #fff;
    border: 1px solid var(--border-strong);
    border-radius: 4px;
    font-size: 10px;
    font-family: 'Cairo', sans-serif;
    color: var(--text-mid);
    line-height: 1.4;
  }

  @media (max-width: 600px) {
    .gs-dropdown { right: 0.5rem; left: 0.5rem; width: auto; }
    .gs-box { width: 160px; }
  }
</style>
