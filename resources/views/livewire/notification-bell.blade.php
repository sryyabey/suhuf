<div
  class="nb-wrap"
  x-data="{
    open: false,
    top: 0,
    right: 0,
    updatePos() {
      const r = this.$refs.btn.getBoundingClientRect();
      this.top   = r.bottom + 8;
      this.right = window.innerWidth - r.right;
    },
    toggle() { this.updatePos(); this.open = !this.open; }
  }"
  @click.outside="open = false"
>
<style>
.nb-wrap { position: relative; }

/* Bell button */
.nb-btn {
  width: 36px; height: 36px; border-radius: 8px;
  background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1);
  display: flex; align-items: center; justify-content: center;
  color: rgba(255,255,255,0.65); font-size: 17px;
  cursor: pointer; position: relative; transition: background .13s;
  flex-shrink: 0;
}
.nb-btn:hover { background: rgba(255,255,255,0.14); }
.nb-btn:focus { outline: none; }

/* Badge */
.nb-badge {
  position: absolute; top: 5px; right: 5px;
  width: 8px; height: 8px; border-radius: 50%;
  background: #f59e0b; border: 1.5px solid var(--teal-dark);
}
.nb-badge.is-danger { background: #ef4444; }

/* Dropdown — position: fixed so topbar overflow:hidden doesn't clip it */
.nb-dropdown {
  position: fixed;
  width: 320px; background: #fff;
  border: 1px solid rgba(184,134,11,0.2);
  border-radius: 14px; box-shadow: 0 12px 36px rgba(0,0,0,.18);
  overflow: hidden; z-index: 9999;
  transform-origin: top right;
}

.nb-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: .75rem 1rem; border-bottom: 1px solid rgba(184,134,11,.12);
  background: #faf7f0;
}
.nb-header-title {
  font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 700;
  color: #2c2416; display: flex; align-items: center; gap: 7px;
}
.nb-header-title i { font-size: 15px; color: #2d9b84; }
.nb-header-count {
  font-family: 'Cairo', sans-serif; font-size: 10.5px; font-weight: 700;
  padding: 2px 8px; border-radius: 999px;
  background: #fef3c7; color: #b45309; border: 1px solid #fde68a;
}

/* Items */
.nb-item {
  display: flex; align-items: flex-start; gap: 10px;
  padding: .8rem 1rem; text-decoration: none; color: inherit;
  border-bottom: 1px solid rgba(184,134,11,.08);
  transition: background .12s;
}
.nb-item:last-child { border-bottom: none; }
.nb-item:hover { background: #faf7f0; }

.nb-item-icon {
  width: 32px; height: 32px; border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  font-size: 15px; flex-shrink: 0;
}
.nb-item.type-warning .nb-item-icon { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
.nb-item.type-danger  .nb-item-icon { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

.nb-item-body { flex: 1; min-width: 0; }
.nb-item-title {
  font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 700;
  color: #2c2416; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.nb-item-msg {
  font-family: 'Cairo', sans-serif; font-size: 11.5px; color: #5a4a2e;
  margin-top: 2px;
}
.nb-item-arrow { font-size: 13px; color: #8a7a60; flex-shrink: 0; align-self: center; }

/* Footer */
.nb-footer {
  padding: .6rem 1rem; border-top: 1px solid rgba(184,134,11,.12);
  background: #faf7f0; text-align: center;
}
.nb-footer a {
  font-family: 'Cairo', sans-serif; font-size: 12px; color: #2d9b84;
  text-decoration: none; font-weight: 600;
}
.nb-footer a:hover { text-decoration: underline; }

/* Empty */
.nb-empty {
  padding: 1.5rem 1rem; text-align: center;
  font-family: 'Cairo', sans-serif; color: #8a7a60;
  display: flex; flex-direction: column; align-items: center; gap: 7px;
}
.nb-empty i { font-size: 28px; color: #e8dfc8; }
.nb-empty-title { font-size: 13px; font-weight: 600; color: #5a4a2e; }
.nb-empty-sub { font-size: 11.5px; }

/* Mobilde: JS koordinatları yerine topbar altına sabitle */
@media (max-width: 640px) {
  .nb-dropdown {
    top: var(--topbar-h) !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    border-radius: 0 0 16px 16px !important;
    max-height: 70vh;
    overflow-y: auto;
  }
}
</style>

  {{-- Bell button --}}
  <button
    x-ref="btn"
    type="button"
    class="nb-btn"
    title="Bildirimler"
    aria-label="Bildirimler"
    @click="toggle()"
  >
    <i class="ti ti-bell" aria-hidden="true"></i>

    @if($this->count > 0)
      <span
        class="nb-badge {{ $this->notifications->contains('type', 'danger') ? 'is-danger' : '' }}"
        aria-hidden="true"
      ></span>
    @endif
  </button>

  {{-- Dropdown — position:fixed, coordinates set by Alpine --}}
  <div
    x-show="open"
    x-transition
    class="nb-dropdown"
    :style="`top:${top}px; right:${right}px`"
    @click.stop
  >
    {{-- Header --}}
    <div class="nb-header">
      <div class="nb-header-title">
        <i class="ti ti-bell"></i> Bildirimler
      </div>
      @if($this->count > 0)
        <span class="nb-header-count">{{ $this->count }} uyarı</span>
      @endif
    </div>

    {{-- Items --}}
    @forelse($this->notifications as $notif)
      <a
        href="{{ $notif['link'] }}"
        class="nb-item type-{{ $notif['type'] }}"
        @click="open = false"
      >
        <div class="nb-item-icon">
          <i class="ti {{ $notif['icon'] }}"></i>
        </div>
        <div class="nb-item-body">
          <div class="nb-item-title">{{ $notif['title'] }}</div>
          <div class="nb-item-msg">{{ $notif['message'] }}</div>
        </div>
        <i class="ti ti-chevron-right nb-item-arrow"></i>
      </a>
    @empty
      <div class="nb-empty">
        <i class="ti ti-bell-off"></i>
        <div class="nb-empty-title">Bildirim yok</div>
        <div class="nb-empty-sub">Paylaşım uyarıları burada görünür</div>
      </div>
    @endforelse

    {{-- Footer --}}
    <div class="nb-footer">
      <a href="{{ route('user.shares') }}" @click="open = false">
        Tüm paylaşımları gör <i class="ti ti-arrow-right" style="font-size:11px;"></i>
      </a>
    </div>
  </div>

</div>
