<div class="sl-wrap" x-data="{
    copied: null,
    copyUrl(token, id) {
        const url = '{{ url('/share/notes/') }}/' + token;
        navigator.clipboard.writeText(url).then(() => {
            this.copied = id;
            setTimeout(() => this.copied = null, 2000);
        });
    }
}" @flash-show.window="setTimeout(() => $wire.flashMsg = '', 3200)">

<style>
/* ── Tokens ────────────────────────────────────────────────────────── */
.sl-wrap { display: flex; flex-direction: column; gap: 1.25rem; }

/* ── Toolbar ───────────────────────────────────────────────────────── */
.sl-toolbar {
  display: flex; align-items: center; gap: 10px;
  flex-wrap: wrap;
  background: #fff;
  border: 1px solid var(--border-strong);
  border-radius: 14px;
  padding: .75rem 1rem;
}
.sl-toolbar-title {
  font-family: 'Cairo', sans-serif;
  font-size: 16px; font-weight: 700;
  color: var(--text-dark);
  margin-right: auto;
  display: flex; align-items: center; gap: 8px;
}
.sl-toolbar-title i { font-size: 18px; color: var(--teal-mid); }

.sl-filter-group { display: flex; gap: 4px; }
.sl-filter-btn {
  font-family: 'Cairo', sans-serif;
  font-size: 12px; font-weight: 500;
  border: 1px solid var(--border-strong);
  border-radius: 8px;
  padding: 5px 11px;
  cursor: pointer;
  background: var(--cream);
  color: var(--text-mid);
  transition: all .13s;
}
.sl-filter-btn:hover { background: var(--teal-light); color: var(--teal-dark); }
.sl-filter-btn.active { background: var(--teal-dark); color: #fff; border-color: var(--teal-dark); }

.sl-sort {
  font-family: 'Cairo', sans-serif; font-size: 12px;
  border: 1px solid var(--border-strong);
  border-radius: 8px; padding: 5px 10px;
  background: var(--cream); color: var(--text-mid);
  cursor: pointer; outline: none;
}

.sl-new-btn {
  display: inline-flex; align-items: center; gap: 6px;
  font-family: 'Cairo', sans-serif; font-size: 12.5px; font-weight: 600;
  background: var(--teal-dark); color: #fff;
  border: none; border-radius: 9px;
  padding: 7px 14px; cursor: pointer;
  text-decoration: none;
  transition: background .13s;
}
.sl-new-btn:hover { background: var(--teal-mid); }

/* ── Flash ─────────────────────────────────────────────────────────── */
.sl-flash {
  display: flex; align-items: center; gap: 9px;
  padding: .65rem 1.1rem;
  border-radius: 11px;
  font-family: 'Cairo', sans-serif; font-size: 13px;
  font-weight: 500;
  animation: fadeIn .2s ease;
}
.sl-flash.success { background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; }
.sl-flash.error   { background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }
@keyframes fadeIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }

/* ── Empty ─────────────────────────────────────────────────────────── */
.sl-empty {
  text-align: center; padding: 3rem 1rem;
  background: #fff; border: 1px dashed var(--border-strong);
  border-radius: 16px;
}
.sl-empty-icon { font-size: 40px; color: var(--sand); margin-bottom: .75rem; }
.sl-empty-title { font-family: 'Cairo', sans-serif; font-size: 15px; font-weight: 600; color: var(--text-mid); margin-bottom: .35rem; }
.sl-empty-sub { font-family: 'Cairo', sans-serif; font-size: 12.5px; color: var(--text-light); }

/* ── Share card ────────────────────────────────────────────────────── */
.sl-card {
  background: #fff;
  border: 1px solid var(--border-strong);
  border-radius: 16px;
  overflow: hidden;
  transition: box-shadow .15s;
}
.sl-card:hover { box-shadow: 0 4px 18px rgba(44,36,22,.09); }
.sl-card.is-revoked { opacity: .65; }
.sl-card.is-expired { opacity: .75; }

/* Card top strip — rengi duruma göre */
.sl-card-strip { height: 4px; }
.sl-card-strip.active  { background: linear-gradient(90deg, var(--teal-dark), var(--teal-mid)); }
.sl-card-strip.revoked { background: #d1d5db; }
.sl-card-strip.expired { background: #fde68a; }

.sl-card-body { padding: 1.1rem 1.25rem; }

/* Üst satır: başlık + badge + aksiyonlar */
.sl-card-top {
  display: flex; align-items: flex-start; gap: 10px;
  margin-bottom: .85rem;
}
.sl-card-title {
  font-family: 'Cairo', sans-serif;
  font-size: 14.5px; font-weight: 700;
  color: var(--text-dark);
  flex: 1;
  word-break: break-word;
}
.sl-card-title span { font-weight: 400; color: var(--text-light); }

/* Status badge */
.sl-status {
  display: inline-flex; align-items: center; gap: 4px;
  font-family: 'Cairo', sans-serif; font-size: 10.5px;
  font-weight: 700; letter-spacing: .4px; text-transform: uppercase;
  border-radius: 999px; padding: 3px 10px;
  white-space: nowrap; flex-shrink: 0;
}
.sl-status.active  { background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; }
.sl-status.expired { background: #fef3c7; border: 1px solid #fcd34d; color: #92400e; }
.sl-status.revoked { background: #f3f4f6; border: 1px solid #d1d5db; color: #6b7280; }

/* URL satırı */
.sl-url-row {
  display: flex; align-items: center; gap: 8px;
  background: var(--cream); border: 1px solid var(--border);
  border-radius: 9px; padding: 6px 10px;
  margin-bottom: .9rem;
}
.sl-url-text {
  font-family: 'Cairo', sans-serif; font-size: 12px;
  color: var(--text-mid); flex: 1;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  text-decoration: none;
}
.sl-url-text:hover { color: var(--teal-dark); }
.sl-copy-btn {
  display: inline-flex; align-items: center; gap: 4px;
  font-family: 'Cairo', sans-serif; font-size: 11px; font-weight: 600;
  background: none; border: 1px solid var(--border-strong);
  border-radius: 7px; padding: 3px 9px;
  cursor: pointer; color: var(--teal-dark);
  transition: all .13s; white-space: nowrap; flex-shrink: 0;
}
.sl-copy-btn:hover { background: var(--teal-light); }
.sl-copy-btn.copied { background: var(--teal-dark); color: #fff; border-color: var(--teal-dark); }

/* Meta chips */
.sl-meta-row {
  display: flex; flex-wrap: wrap; gap: 8px;
  margin-bottom: .9rem;
}
.sl-chip {
  display: inline-flex; align-items: center; gap: 5px;
  font-family: 'Cairo', sans-serif; font-size: 11.5px;
  color: var(--text-light);
  background: var(--cream2); border: 1px solid var(--border);
  border-radius: 8px; padding: 3px 9px;
}
.sl-chip i { font-size: 12px; color: var(--teal-mid); }
.sl-chip.vis-public  { color: var(--teal-dark); background: var(--teal-light); border-color: rgba(45,155,132,.25); }
.sl-chip.vis-private { color: #6d28d9; background: #f5f3ff; border-color: #ddd6fe; }
.sl-chip.warn { color: #92400e; background: #fef3c7; border-color: #fcd34d; }

/* Aksiyon butonları */
.sl-actions {
  display: flex; align-items: center; gap: 6px;
  padding-top: .8rem;
  border-top: 1px solid var(--border);
  flex-wrap: wrap;
}
.sl-btn {
  display: inline-flex; align-items: center; gap: 5px;
  font-family: 'Cairo', sans-serif; font-size: 12px; font-weight: 500;
  border-radius: 8px; padding: 5px 12px;
  cursor: pointer; border: 1px solid;
  transition: all .13s; text-decoration: none;
  white-space: nowrap;
}
.sl-btn-edit   { background: var(--cream); color: var(--text-mid); border-color: var(--border-strong); }
.sl-btn-edit:hover { background: var(--cream2); }
.sl-btn-vis    { background: var(--cream); color: var(--text-mid); border-color: var(--border-strong); }
.sl-btn-vis:hover { background: var(--cream2); }
.sl-btn-revoke { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
.sl-btn-revoke:hover { background: #ffedd5; }
.sl-btn-restore { background: var(--teal-light); color: var(--teal-dark); border-color: rgba(45,155,132,.3); }
.sl-btn-restore:hover { background: rgba(45,155,132,.18); }
.sl-btn-open   { background: var(--teal-dark); color: #fff; border-color: var(--teal-dark); }
.sl-btn-open:hover { background: var(--teal-mid); }
.sl-btn-del    { background: #fee2e2; color: #991b1b; border-color: #fca5a5; margin-left: auto; }
.sl-btn-del:hover { background: #fecaca; }

/* ── Silme onay satırı ─────────────────────────────────────────────── */
.sl-confirm-del {
  display: flex; align-items: center; gap: 10px;
  background: #fee2e2; border: 1px solid #fca5a5;
  border-radius: 10px; padding: .6rem 1rem;
  margin-top: .75rem;
  font-family: 'Cairo', sans-serif; font-size: 12.5px;
  color: #991b1b;
}
.sl-confirm-del strong { flex: 1; }
.sl-btn-del-yes {
  background: #dc2626; color: #fff; border: none;
  border-radius: 7px; padding: 4px 13px;
  font-family: 'Cairo', sans-serif; font-size: 12px; font-weight: 600;
  cursor: pointer;
}
.sl-btn-del-no {
  background: #fff; color: #6b7280;
  border: 1px solid #d1d5db;
  border-radius: 7px; padding: 4px 12px;
  font-family: 'Cairo', sans-serif; font-size: 12px;
  cursor: pointer;
}

/* ── Edit Modal ────────────────────────────────────────────────────── */
.sl-modal-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,.45);
  backdrop-filter: blur(3px);
  z-index: 50;
  display: flex; align-items: center; justify-content: center;
  padding: 1rem;
}
.sl-modal {
  background: #fff;
  border-radius: 18px;
  width: 100%; max-width: 480px;
  box-shadow: 0 20px 60px rgba(0,0,0,.2);
  overflow: hidden;
}
.sl-modal-head {
  display: flex; align-items: center; gap: 10px;
  padding: 1.1rem 1.25rem;
  border-bottom: 1px solid var(--border);
  background: var(--cream);
}
.sl-modal-title {
  font-family: 'Cairo', sans-serif; font-size: 15px; font-weight: 700;
  color: var(--text-dark); flex: 1;
}
.sl-modal-close {
  width: 30px; height: 30px; border-radius: 8px;
  background: none; border: 1px solid var(--border-strong);
  cursor: pointer; color: var(--text-light);
  display: flex; align-items: center; justify-content: center;
  font-size: 16px; transition: background .13s;
}
.sl-modal-close:hover { background: var(--cream2); }
.sl-modal-body { padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem; }
.sl-field { display: flex; flex-direction: column; gap: 5px; }
.sl-label {
  font-family: 'Cairo', sans-serif; font-size: 12px; font-weight: 600;
  color: var(--text-mid);
}
.sl-input {
  font-family: 'Cairo', sans-serif; font-size: 13.5px;
  border: 1px solid var(--border-strong);
  border-radius: 10px; padding: 8px 12px;
  background: var(--cream); color: var(--text-dark);
  outline: none; transition: border-color .15s;
  width: 100%;
}
.sl-input:focus { border-color: var(--teal-mid); background: #fff; }
.sl-input.error { border-color: #ef4444; }
.sl-error-msg { font-family: 'Cairo', sans-serif; font-size: 11.5px; color: #dc2626; }

.sl-radio-group { display: flex; gap: 8px; }
.sl-radio-opt {
  flex: 1; border: 1.5px solid var(--border-strong);
  border-radius: 10px; padding: 8px 10px;
  cursor: pointer; transition: all .13s;
  display: flex; align-items: center; gap: 7px;
  font-family: 'Cairo', sans-serif; font-size: 12.5px;
  color: var(--text-mid);
}
.sl-radio-opt:has(input:checked) {
  border-color: var(--teal-mid);
  background: var(--teal-light);
  color: var(--teal-dark);
  font-weight: 600;
}
.sl-radio-opt input { accent-color: var(--teal-dark); }
.sl-radio-opt i { font-size: 14px; }

.sl-modal-foot {
  display: flex; align-items: center; justify-content: flex-end; gap: 8px;
  padding: .9rem 1.25rem;
  border-top: 1px solid var(--border);
  background: var(--cream);
}
.sl-btn-cancel {
  font-family: 'Cairo', sans-serif; font-size: 13px;
  background: none; border: 1px solid var(--border-strong);
  border-radius: 9px; padding: 7px 16px;
  cursor: pointer; color: var(--text-mid);
}
.sl-btn-save {
  font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600;
  background: var(--teal-dark); color: #fff;
  border: none; border-radius: 9px; padding: 7px 20px;
  cursor: pointer; transition: background .13s;
  display: flex; align-items: center; gap: 6px;
}
.sl-btn-save:hover { background: var(--teal-mid); }

/* ── Payload özet ──────────────────────────────────────────────────── */
.sl-payload-peek {
  font-family: 'Cairo', sans-serif; font-size: 11px;
  color: var(--text-light);
  margin-top: 4px;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
</style>

{{-- ── Flash ─────────────────────────────────────────────────────── --}}
@if($flashMsg)
  <div class="sl-flash {{ $flashType }}">
    <i class="ti ti-{{ $flashType === 'success' ? 'circle-check' : 'alert-circle' }}" style="font-size:16px;"></i>
    {{ $flashMsg }}
  </div>
@endif

{{-- ── Toolbar ─────────────────────────────────────────────────────── --}}
<div class="sl-toolbar">
  <span class="sl-toolbar-title">
    <i class="ti ti-link"></i>
    {{ __('My Share Links') }}
  </span>

  {{-- Filtre --}}
  <div class="sl-filter-group">
    @foreach(['all' => __('All'), 'active' => __('Active'), 'expired' => __('Expired'), 'revoked' => __('Revoked')] as $val => $label)
      <button
        type="button"
        wire:click="$set('filterStatus', '{{ $val }}')"
        class="sl-filter-btn {{ $filterStatus === $val ? 'active' : '' }}"
      >{{ $label }}</button>
    @endforeach
  </div>

  {{-- Sırala --}}
  <select class="sl-sort" wire:model.live="sortBy">
    <option value="new">{{ __('Newest first') }}</option>
    <option value="old">{{ __('Oldest first') }}</option>
    <option value="views">{{ __('Most viewed') }}</option>
  </select>

  {{-- Yeni paylaşım için not araştırmasına yönlendir --}}
  <a href="{{ route('user.quran-notes-range') }}" class="sl-new-btn">
    <i class="ti ti-plus" style="font-size:14px;"></i>
    {{ __('New Share') }}
  </a>
</div>

{{-- ── Liste ───────────────────────────────────────────────────────── --}}
@forelse($this->shares as $share)
  @php
    $isRevoked = $share->isRevoked();
    $isExpired = !$isRevoked && $share->isExpired();
    $isActive  = !$isRevoked && !$isExpired;

    $cardClass  = $isRevoked ? 'is-revoked' : ($isExpired ? 'is-expired' : '');
    $stripClass = $isRevoked ? 'revoked' : ($isExpired ? 'expired' : 'active');

    $statusLabel = $isRevoked ? __('Revoked') : ($isExpired ? __('Expired') : __('Active'));
    $statusIcon  = $isRevoked ? 'ti-ban' : ($isExpired ? 'ti-clock-x' : 'ti-circle-check');
    $statusClass = $isRevoked ? 'revoked' : ($isExpired ? 'expired' : 'active');

    $shareUrl = route('notes.share.show', ['token' => $share->token]);

    // Payload özeti
    $totalNotes = collect($share->payload['grouped'] ?? [])
        ->flatMap(fn($s) => collect($s['ayas'] ?? [])->flatMap(fn($a) => $a['notes'] ?? []))
        ->count();
    $suraCount = count($share->payload['grouped'] ?? []);
    $rangeText = isset($share->payload['range'])
        ? ($share->payload['range']['start'] ?? '') . ' → ' . ($share->payload['range']['end'] ?? '')
        : null;
  @endphp

  <div class="sl-card {{ $cardClass }}">
    <div class="sl-card-strip {{ $stripClass }}"></div>
    <div class="sl-card-body">

      {{-- Üst satır --}}
      <div class="sl-card-top">
        <div>
          <div class="sl-card-title">
            {{ $share->title ?: __('Untitled Share') }}
            @if(!$share->title)
              <span>({{ __('no title') }})</span>
            @endif
          </div>
          <div class="sl-payload-peek">
            <i class="ti ti-book-2" style="font-size:10px;"></i>
            {{ $suraCount }} {{ __('suras') }} · {{ $totalNotes }} {{ __('notes') }}
            @if($rangeText) · {{ $rangeText }} @endif
          </div>
        </div>
        <span class="sl-status {{ $statusClass }}">
          <i class="ti {{ $statusIcon }}" style="font-size:10px;"></i>
          {{ $statusLabel }}
        </span>
      </div>

      {{-- URL satırı --}}
      <div class="sl-url-row">
        <a href="{{ $shareUrl }}" target="_blank" class="sl-url-text" title="{{ $shareUrl }}">
          {{ $shareUrl }}
        </a>
        <button
          type="button"
          class="sl-copy-btn"
          :class="{ copied: copied === {{ $share->id }} }"
          @click="copyUrl('{{ $share->token }}', {{ $share->id }})"
        >
          <i class="ti" :class="copied === {{ $share->id }} ? 'ti-check' : 'ti-copy'" style="font-size:12px;"></i>
          <span x-text="copied === {{ $share->id }} ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'">{{ __('Copy') }}</span>
        </button>
      </div>

      {{-- Meta chips --}}
      <div class="sl-meta-row">
        {{-- Görünürlük --}}
        <span class="sl-chip {{ $share->visibility === 'public' ? 'vis-public' : 'vis-private' }}">
          <i class="ti {{ $share->visibility === 'public' ? 'ti-world' : 'ti-lock' }}"></i>
          {{ $share->visibility === 'public' ? __('Public') : __('Private') }}
        </span>

        {{-- Görüntülenme --}}
        <span class="sl-chip">
          <i class="ti ti-eye"></i>
          {{ number_format($share->access_count) }} {{ __('views') }}
        </span>

        {{-- Son erişim --}}
        @if($share->last_accessed_at)
          <span class="sl-chip">
            <i class="ti ti-clock"></i>
            {{ __('Last') }}: {{ $share->last_accessed_at->locale(app()->getLocale())->diffForHumans() }}
          </span>
        @endif

        {{-- Oluşturulma --}}
        <span class="sl-chip">
          <i class="ti ti-calendar-plus"></i>
          {{ $share->created_at->locale(app()->getLocale())->isoFormat('D MMM Y') }}
        </span>

        {{-- Son kullanma --}}
        @if($share->expires_at)
          <span class="sl-chip {{ $isExpired ? 'warn' : '' }}">
            <i class="ti {{ $isExpired ? 'ti-clock-x' : 'ti-calendar-event' }}"></i>
            {{ $isExpired ? __('Expired:') : __('Ends:') }} {{ $share->expires_at->locale(app()->getLocale())->isoFormat('D MMM Y') }}
          </span>
        @else
          <span class="sl-chip">
            <i class="ti ti-infinity"></i>
            {{ __('No expiration') }}
          </span>
        @endif
      </div>

      {{-- Aksiyon butonları --}}
      <div class="sl-actions">
        {{-- Düzenle --}}
        <button type="button" class="sl-btn sl-btn-edit" wire:click="openEdit({{ $share->id }})">
          <i class="ti ti-pencil" style="font-size:12px;"></i> {{ __('Edit') }}
        </button>

        {{-- Görünürlük toggle --}}
        <button type="button" class="sl-btn sl-btn-vis" wire:click="toggleVisibility({{ $share->id }})">
          <i class="ti {{ $share->visibility === 'public' ? 'ti-lock' : 'ti-world' }}" style="font-size:12px;"></i>
          {{ $share->visibility === 'public' ? __('Make private') : __('Make public') }}
        </button>

        {{-- Revoke / Restore --}}
        @if($isRevoked)
          <button type="button" class="sl-btn sl-btn-restore" wire:click="restore({{ $share->id }})">
            <i class="ti ti-refresh" style="font-size:12px;"></i> {{ __('Reactivate') }}
          </button>
        @elseif($isActive)
          <button type="button" class="sl-btn sl-btn-revoke" wire:click="revoke({{ $share->id }})">
            <i class="ti ti-ban" style="font-size:12px;"></i> {{ __('Disable Access') }}
          </button>
        @endif

        {{-- Aç --}}
        <a href="{{ $shareUrl }}" target="_blank" class="sl-btn sl-btn-open">
          <i class="ti ti-external-link" style="font-size:12px;"></i> {{ __('Open') }}
        </a>

        {{-- Sil --}}
        <button type="button" class="sl-btn sl-btn-del" wire:click="confirmDelete({{ $share->id }})">
          <i class="ti ti-trash" style="font-size:12px;"></i> {{ __('Delete') }}
        </button>
      </div>

      {{-- Silme onayı --}}
      @if($confirmDeleteId === $share->id)
        <div class="sl-confirm-del">
          <i class="ti ti-alert-triangle" style="font-size:15px;"></i>
          <strong>{{ __('This share will be permanently deleted. Are you sure?') }}</strong>
          <button class="sl-btn-del-yes" wire:click="delete()">{{ __('Yes, Delete') }}</button>
          <button class="sl-btn-del-no"  wire:click="cancelDelete()">{{ __('Cancel') }}</button>
        </div>
      @endif

    </div>
  </div>

@empty
  <div class="sl-empty">
    <div class="sl-empty-icon"><i class="ti ti-link-off"></i></div>
    <div class="sl-empty-title">
      @if($filterStatus === 'all')
        {{ __('You have not created any shares yet') }}
      @elseif($filterStatus === 'active')
        {{ __('No active shares') }}
      @elseif($filterStatus === 'expired')
        {{ __('No expired shares') }}
      @else
        {{ __('No revoked shares') }}
      @endif
    </div>
    <div class="sl-empty-sub">
      {{ __('You can share your notes from the note research page.') }}
    </div>
  </div>
@endforelse

{{-- ── Edit Modal ──────────────────────────────────────────────────── --}}
@if($editModalOpen)
  <div class="sl-modal-overlay" wire:click.self="closeEdit">
    <div class="sl-modal">

      <div class="sl-modal-head">
        <i class="ti ti-pencil" style="font-size:17px;color:var(--teal-mid);"></i>
        <span class="sl-modal-title">{{ __('Edit Share') }}</span>
        <button type="button" class="sl-modal-close" wire:click="closeEdit">
          <i class="ti ti-x"></i>
        </button>
      </div>

      <div class="sl-modal-body">

        {{-- Başlık --}}
        <div class="sl-field">
          <label class="sl-label">{{ __('Title') }} <span style="font-weight:400;color:var(--text-light);">({{ __('optional') }})</span></label>
          <input
            type="text"
            class="sl-input @error('editTitle') error @enderror"
            placeholder="{{ __('A descriptive title for the share...') }}"
            wire:model="editTitle"
            maxlength="160"
          >
          @error('editTitle')
            <span class="sl-error-msg">{{ $message }}</span>
          @enderror
        </div>

        {{-- Görünürlük --}}
        <div class="sl-field">
          <label class="sl-label">{{ __('Visibility') }}</label>
          <div class="sl-radio-group">
            <label class="sl-radio-opt">
              <input type="radio" wire:model="editVisibility" value="public">
              <i class="ti ti-world"></i>
              {{ __('Public') }}
            </label>
            <label class="sl-radio-opt">
              <input type="radio" wire:model="editVisibility" value="private">
              <i class="ti ti-lock"></i>
              {{ __('Only me') }}
            </label>
          </div>
        </div>

        {{-- Son kullanma tarihi --}}
        <div class="sl-field">
          <label class="sl-label">{{ __('Expiration Date') }} <span style="font-weight:400;color:var(--text-light);">({{ __('empty = no expiration') }})</span></label>
          <input
            type="date"
            class="sl-input @error('editExpiry') error @enderror"
            wire:model="editExpiry"
            min="{{ now()->addDay()->format('Y-m-d') }}"
          >
          @error('editExpiry')
            <span class="sl-error-msg">{{ $message }}</span>
          @enderror
        </div>

      </div>

      <div class="sl-modal-foot">
        <button type="button" class="sl-btn-cancel" wire:click="closeEdit">{{ __('Cancel') }}</button>
        <button type="button" class="sl-btn-save" wire:click="saveEdit">
          <i class="ti ti-check" style="font-size:13px;"></i>
          {{ __('Save') }}
        </button>
      </div>

    </div>
  </div>
@endif

</div>
