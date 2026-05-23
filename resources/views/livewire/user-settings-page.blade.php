<div
    x-data="{ saved: false }"
    @settings-saved.window="saved = true; setTimeout(() => saved = false, 3500)"
    class="usp"
>
<style>
/* ── Temel ─────────────────────────────────────────── */
.usp { max-width: 780px; margin: 0 auto; display: flex; flex-direction: column; gap: 16px; }

/* ── Sayfa başlığı ──────────────────────────────────── */
.usp-page-title { font-family: 'Cairo', sans-serif; font-size: 22px; font-weight: 600; color: var(--teal-dark); margin: 0; }
.usp-page-sub   { font-size: 12px; color: var(--text-light); margin: 3px 0 0; }

/* ── Profil kartı ───────────────────────────────────── */
.usp-profile-card { border-radius: 16px; overflow: hidden; border: 1px solid var(--border-strong); }
.usp-profile-top  { background: linear-gradient(135deg, var(--teal-dark) 0%, #2d9b84 100%); padding: 22px 24px; display: flex; align-items: center; gap: 18px; position: relative; overflow: hidden; }
.usp-profile-top::after { content: ''; position: absolute; right: -20px; top: -20px; width: 160px; height: 160px; background: rgba(255,255,255,.06); border-radius: 50%; pointer-events: none; }
.usp-profile-top::before { content: ''; position: absolute; right: 40px; bottom: -30px; width: 100px; height: 100px; background: rgba(255,255,255,.04); border-radius: 50%; pointer-events: none; }
.usp-avatar     { width: 62px; height: 62px; border-radius: 50%; background: rgba(255,255,255,.18); border: 2.5px solid rgba(255,255,255,.35); display: flex; align-items: center; justify-content: center; font-family: 'Cairo', sans-serif; font-size: 22px; font-weight: 700; color: #fff; flex-shrink: 0; position: relative; z-index: 1; letter-spacing: 1px; }
.usp-profile-info { position: relative; z-index: 1; }
.usp-profile-name  { font-family: 'Cairo', sans-serif; font-size: 18px; font-weight: 700; color: #fff; margin: 0 0 3px; }
.usp-profile-email { font-family: 'Cairo', sans-serif; font-size: 12px; color: rgba(255,255,255,.65); margin: 0; }
.usp-profile-role  { display: inline-flex; align-items: center; gap: 5px; margin-top: 7px; padding: 3px 10px; background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.2); border-radius: 999px; font-family: 'Cairo', sans-serif; font-size: 11px; color: rgba(255,255,255,.85); }
.usp-profile-bot  { background: #fff; padding: 14px 24px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; border-top: 1px solid var(--border); }
.usp-last-read    { display: flex; align-items: center; gap: 8px; font-family: 'Cairo', sans-serif; font-size: 13px; color: var(--text-mid); }
.usp-last-read-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; background: var(--teal-light); border: 1px solid rgba(45,155,132,.2); border-radius: 7px; color: var(--teal-dark); font-weight: 600; font-size: 12px; text-decoration: none; transition: background .12s; }
.usp-last-read-badge:hover { background: #c8eee8; }
.usp-no-read { font-size: 12px; color: var(--text-light); font-style: italic; }

/* ── Bölüm kartı ────────────────────────────────────── */
.usp-card { background: #fff; border: 1px solid var(--border-strong); border-radius: 14px; overflow: hidden; }
.usp-card-header { display: flex; align-items: center; gap: 10px; padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--cream); }
.usp-card-icon   { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
.usp-card-icon.teal { background: var(--teal-light); color: var(--teal-dark); }
.usp-card-title  { font-family: 'Cairo', sans-serif; font-size: 15px; font-weight: 700; color: var(--text-dark); margin: 0; }
.usp-card-sub    { font-family: 'Cairo', sans-serif; font-size: 12px; color: var(--text-light); margin: 2px 0 0; }
.usp-card-body   { padding: 20px 22px; display: flex; flex-direction: column; gap: 20px; }

/* ── Dil seçimi ─────────────────────────────────────── */
.usp-lang-row    { display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap; }
.usp-field       { display: flex; flex-direction: column; gap: 6px; }
.usp-label       { font-family: 'Cairo', sans-serif; font-size: 11px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: .6px; }
.usp-select      { padding: 10px 34px 10px 12px; border: 1.5px solid var(--border-strong); border-radius: 9px; font-size: 14px; font-family: 'Cairo', sans-serif; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%238a7a60' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 10px center; color: var(--text-dark); appearance: none; cursor: pointer; min-width: 180px; transition: border-color .12s, box-shadow .12s; }
.usp-select:focus { outline: none; border-color: var(--teal-mid); box-shadow: 0 0 0 3px rgba(45,155,132,.12); }
.usp-lang-hint   { font-size: 12px; color: var(--text-light); padding: 8px 12px; background: var(--gold-pale); border: 1px solid var(--border); border-radius: 8px; display: flex; align-items: center; gap: 6px; }

/* ── Meal sütunları ─────────────────────────────────── */
.usp-meal-divider { display: flex; align-items: center; gap: 10px; }
.usp-meal-divider-line { flex: 1; height: 1px; background: var(--border); }
.usp-meal-divider-text { font-family: 'Cairo', sans-serif; font-size: 11px; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: .6px; white-space: nowrap; }

.usp-meal-cols  { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.usp-meal-col   { border: 1px solid var(--border-strong); border-radius: 11px; overflow: hidden; }
.usp-meal-col-head { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-bottom: 1px solid var(--border); }
.usp-meal-col-title { font-family: 'Cairo', sans-serif; font-size: 12px; font-weight: 700; color: var(--text-mid); display: flex; align-items: center; gap: 6px; }
.usp-meal-count    { display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 999px; font-family: 'Cairo', sans-serif; font-size: 11px; font-weight: 700; }
.usp-meal-count.selected { background: var(--teal-light); color: var(--teal-dark); }
.usp-meal-count.available { background: var(--cream2); color: var(--text-mid); }
.usp-col-selected { background: #fdfdfc; }
.usp-col-available { background: #fdfdfc; }

.usp-meal-list    { max-height: 300px; overflow-y: auto; display: flex; flex-direction: column; }
.usp-meal-item    { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 9px 14px; border-bottom: 1px solid var(--border); transition: background .1s; }
.usp-meal-item:last-child { border-bottom: none; }
.usp-meal-item:hover { background: var(--cream); }
.usp-meal-name    { font-family: 'Cairo', sans-serif; font-size: 13px; color: var(--text-dark); flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.usp-meal-btn     { width: 28px; height: 28px; border-radius: 7px; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 15px; flex-shrink: 0; transition: all .12s; }
.usp-meal-btn.remove { background: #fff0f0; color: #b42318; }
.usp-meal-btn.remove:hover { background: #ffd9d9; }
.usp-meal-btn.add    { background: var(--teal-light); color: var(--teal-dark); }
.usp-meal-btn.add:hover { background: #c0e9e2; }

.usp-meal-empty   { padding: 24px 14px; text-align: center; color: var(--text-light); }
.usp-meal-empty i { font-size: 24px; display: block; margin-bottom: 6px; }
.usp-meal-empty span { font-family: 'Cairo', sans-serif; font-size: 12px; }

/* ── Bilgi notu ─────────────────────────────────────── */
.usp-info-note { display: flex; align-items: flex-start; gap: 8px; padding: 10px 14px; background: var(--gold-pale); border: 1px solid var(--border); border-radius: 9px; font-family: 'Cairo', sans-serif; font-size: 12px; color: var(--text-mid); }
.usp-info-note i { color: var(--gold); font-size: 15px; flex-shrink: 0; margin-top: 1px; }

/* ── Kaydet satırı ──────────────────────────────────── */
.usp-save-row  { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.usp-save-btn  { display: inline-flex; align-items: center; gap: 7px; padding: 11px 22px; border-radius: 10px; border: none; background: var(--teal-dark); color: #fff; font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; transition: background .15s; }
.usp-save-btn:hover { background: #145246; }
.usp-save-btn:disabled { opacity: .5; cursor: not-allowed; }

/* ── Başarı toast ───────────────────────────────────── */
.usp-success-toast { display: flex; align-items: center; gap: 8px; padding: 9px 14px; background: #e8f5f2; border: 1px solid rgba(45,155,132,.3); border-radius: 9px; font-family: 'Cairo', sans-serif; font-size: 13px; color: var(--teal-dark); font-weight: 600; }
.usp-success-toast i { font-size: 17px; }


/* ── Responsive ─────────────────────────────────────── */
@media (max-width: 600px) {
    .usp-meal-cols { grid-template-columns: 1fr; }
    .usp-profile-top { flex-direction: column; align-items: flex-start; }
}
</style>

@php
    $authUser = auth()->user();
    $userName = $authUser?->name ?? __('User');
    $userEmail = $authUser?->email ?? '';
    $initials  = collect(explode(' ', trim($userName)))
        ->filter()->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->implode('');
    $initials  = $initials ?: 'U';
    $lastSura  = $authUser?->setting?->last_read_sura;
    $lastAya   = $authUser?->setting?->last_read_aya;
@endphp

{{-- Sayfa Başlığı --}}
<div>
    <h1 class="usp-page-title">{{ __('Settings') }}</h1>
    <p class="usp-page-sub">{{ __('You can manage your account information and reading preferences here.') }}</p>
</div>

{{-- Profil Kartı --}}
<div class="usp-profile-card">
    <div class="usp-profile-top">
        <div class="usp-avatar">{{ $initials }}</div>
        <div class="usp-profile-info">
            <p class="usp-profile-name">{{ $userName }}</p>
            <p class="usp-profile-email">{{ $userEmail }}</p>
        </div>
    </div>
    <div class="usp-profile-bot">
        <div class="usp-last-read">
            <i class="ti ti-bookmark" style="color:var(--text-light); font-size:15px;"></i>
            <span style="font-size:12px; color:var(--text-light);">{{ __('Last read:') }}</span>
            @if($lastSura && $lastAya)
                <a href="{{ route('user.quran-text') }}" class="usp-last-read-badge">
                    <i class="ti ti-book"></i>
                    {{ $lastSura }}:{{ $lastAya }}
                </a>
            @else
                <span class="usp-no-read">{{ __('No reading saved yet') }}</span>
            @endif
        </div>
        <a href="{{ route('user.quran-text') }}" style="font-family:'Cairo',sans-serif; font-size:12px; color:var(--teal-dark); text-decoration:none; display:flex; align-items:center; gap:5px;">
            <i class="ti ti-book"></i> {{ __('Go to Quran Text') }}
        </a>
    </div>
</div>

{{-- Okuma Tercihleri Kartı --}}
<div class="usp-card">
    <div class="usp-card-header">
        <div class="usp-card-icon teal"><i class="ti ti-adjustments-horizontal"></i></div>
        <div>
            <p class="usp-card-title">{{ __('Reading Preferences') }}</p>
            <p class="usp-card-sub">{{ __('Manage your translation language and meal selections') }}</p>
        </div>
    </div>

    <div class="usp-card-body">

        {{-- Dil Seçimi --}}
        <div>
            <div class="usp-meal-divider" style="margin-bottom:12px;">
                <div class="usp-meal-divider-line"></div>
                <span class="usp-meal-divider-text"><i class="ti ti-language" style="font-size:11px;"></i> {{ __('Translation Language') }}</span>
                <div class="usp-meal-divider-line"></div>
            </div>
            <div class="usp-lang-row">
                <div class="usp-field">
                    <label class="usp-label">{{ __('Select Language') }}</label>
                    <select wire:model.live="selectedLanguage" class="usp-select">
                        @foreach($this->languages as $language)
                            <option value="{{ $language }}">{{ $language }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="usp-lang-hint">
                    <i class="ti ti-info-circle"></i>
                    {{ __('Language selection updates the meal list below.') }}
                </div>
            </div>
        </div>

        {{-- Arabic Font --}}
        <div>
            <div class="usp-meal-divider" style="margin-bottom:12px;">
                <div class="usp-meal-divider-line"></div>
                <span class="usp-meal-divider-text"><i class="ti ti-typography" style="font-size:11px;"></i> {{ __('Arabic Font') }}</span>
                <div class="usp-meal-divider-line"></div>
            </div>
            <div class="usp-lang-row">
                <div class="usp-field">
                    <label class="usp-label">{{ __('Select Arabic Font') }}</label>
                    <select wire:model.live="selectedArabicFont" class="usp-select">
                        @foreach($this->arabicFontOptions as $fontKey => $fontLabel)
                            <option value="{{ $fontKey }}">{{ $fontLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="usp-lang-hint">
                    <i class="ti ti-info-circle"></i>
                    {{ __('This preference affects Quran Reading and Quran Text pages.') }}
                </div>
            </div>
        </div>

        {{-- Meal Tercihleri --}}
        <div>
            <div class="usp-meal-divider" style="margin-bottom:12px;">
                <div class="usp-meal-divider-line"></div>
                <span class="usp-meal-divider-text"><i class="ti ti-books" style="font-size:11px;"></i> {{ __('Meal Preferences') }}</span>
                <div class="usp-meal-divider-line"></div>
            </div>

            <div class="usp-meal-cols">

                {{-- Seçili Mealler --}}
                <div class="usp-meal-col usp-col-selected">
                    <div class="usp-meal-col-head">
                        <span class="usp-meal-col-title">
                            <i class="ti ti-circle-check" style="color:var(--teal-dark);"></i>
                            {{ __('Selected Meals') }}
                        </span>
                        <span class="usp-meal-count selected">{{ count($selectedMealKeys) }}</span>
                    </div>
                    <div class="usp-meal-list">
                        @forelse($selectedMealKeys as $mealKey)
                            <div class="usp-meal-item">
                                <span class="usp-meal-name" title="{{ $mealKey }}">{{ $mealKey }}</span>
                                <button
                                    wire:click="removeMeal('{{ $mealKey }}')"
                                    type="button"
                                    class="usp-meal-btn remove"
                                    title="{{ __('Remove') }}"
                                >
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                        @empty
                            <div class="usp-meal-empty">
                                <i class="ti ti-inbox"></i>
                                <span>{{ __('No selected meals.') }}<br>{{ __('Add from the right.') }}</span>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Eklenebilir Mealler --}}
                <div class="usp-meal-col usp-col-available">
                    <div class="usp-meal-col-head">
                        <span class="usp-meal-col-title">
                            <i class="ti ti-plus-circle" style="color:var(--text-light);"></i>
                            {{ __('Available Meals') }}
                        </span>
                        <span class="usp-meal-count available">{{ $this->availableMeals->count() }}</span>
                    </div>
                    <div class="usp-meal-list">
                        @forelse($this->availableMeals as $mealKey)
                            <div class="usp-meal-item">
                                <span class="usp-meal-name" title="{{ $mealKey }}">{{ $mealKey }}</span>
                                <button
                                    wire:click="addMeal('{{ $mealKey }}')"
                                    type="button"
                                    class="usp-meal-btn add"
                                    title="{{ __('Add') }}"
                                >
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>
                        @empty
                            <div class="usp-meal-empty">
                                <i class="ti ti-check"></i>
                                <span>{{ __('All meals are selected.') }}</span>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

        {{-- Bilgi Notu --}}
        <div class="usp-info-note">
            <i class="ti ti-alert-circle"></i>
            {{ __('Your selections are applied temporarily. To make changes permanent, click') }} <strong>{{ __('Save Preferences') }}</strong>.
        </div>

        {{-- Kaydet Satırı --}}
        <div class="usp-save-row">
            <button
                wire:click="savePreferences"
                wire:loading.attr="disabled"
                type="button"
                class="usp-save-btn"
            >
                <span wire:loading.remove wire:target="savePreferences">
                    <i class="ti ti-device-floppy"></i> {{ __('Save Preferences') }}
                </span>
                <span wire:loading wire:target="savePreferences">
                    <i class="ti ti-loader-2"></i> {{ __('Saving...') }}
                </span>
            </button>

            <div
                x-show="saved"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="usp-success-toast"
                style="display:none;"
            >
                <i class="ti ti-circle-check"></i> {{ __('Preferences saved successfully!') }}
            </div>
        </div>

    </div>
</div>


</div>
