<?php

namespace App\Livewire;

use App\Models\NoteShareLink;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ShareLinksPage extends Component
{
    /* ── Filtre / sıralama ─────────────────────────────────────────── */
    public string $filterStatus = 'all';   // all | active | expired | revoked
    public string $sortBy       = 'new';   // new | old | views

    /* ── Düzenleme modalı ─────────────────────────────────────────── */
    public bool    $editModalOpen  = false;
    public ?int    $editingId      = null;
    public string  $editTitle      = '';
    public string  $editVisibility = 'public';
    public string  $editExpiry     = '';       // '' = süresiz, ya da 'YYYY-MM-DD' formatında

    /* ── Silme onayı ──────────────────────────────────────────────── */
    public ?int $confirmDeleteId = null;

    /* ── Flash mesajı ─────────────────────────────────────────────── */
    public string $flashMsg  = '';
    public string $flashType = 'success'; // success | error

    /* ── Computed: filtreli liste ─────────────────────────────────── */
    #[Computed]
    public function shares()
    {
        $query = NoteShareLink::query()
            ->where('user_id', Auth::id())
            ->withCount([]); // access_count zaten kolonda var

        // Filtre
        $query->when($this->filterStatus === 'active', function ($q) {
            $q->whereNull('revoked_at')
              ->where(fn ($q2) => $q2->whereNull('expires_at')->orWhere('expires_at', '>', now()));
        })->when($this->filterStatus === 'expired', function ($q) {
            $q->whereNull('revoked_at')->where('expires_at', '<=', now());
        })->when($this->filterStatus === 'revoked', function ($q) {
            $q->whereNotNull('revoked_at');
        });

        // Sıralama
        match ($this->sortBy) {
            'old'   => $query->oldest(),
            'views' => $query->orderByDesc('access_count'),
            default => $query->latest(),
        };

        return $query->get();
    }

    /* ── Düzenleme modalını aç ────────────────────────────────────── */
    public function openEdit(int $id): void
    {
        $share = $this->findOwned($id);
        if (! $share) return;

        $this->editingId      = $id;
        $this->editTitle      = $share->title ?? '';
        $this->editVisibility = $share->visibility;
        $this->editExpiry     = $share->expires_at?->format('Y-m-d') ?? '';
        $this->editModalOpen  = true;
        $this->resetValidation();
    }

    public function closeEdit(): void
    {
        $this->editModalOpen = false;
        $this->editingId     = null;
    }

    /* ── Kaydet ───────────────────────────────────────────────────── */
    public function saveEdit(): void
    {
        $this->validate([
            'editTitle'      => 'nullable|max:160',
            'editVisibility' => 'required|in:public,private',
            'editExpiry'     => 'nullable|date|after:today',
        ], [
            'editExpiry.after' => 'Son kullanma tarihi bugünden sonra olmalıdır.',
            'editExpiry.date'  => 'Geçerli bir tarih girin.',
        ]);

        $share = $this->findOwned($this->editingId);
        if (! $share) return;

        $share->update([
            'title'      => trim($this->editTitle) ?: null,
            'visibility' => $this->editVisibility,
            'expires_at' => $this->editExpiry ? Carbon::parse($this->editExpiry)->endOfDay() : null,
        ]);

        $this->closeEdit();
        $this->flash('Paylaşım güncellendi.');
        unset($this->shares);
    }

    /* ── Erişimi iptal et (revoke) ────────────────────────────────── */
    public function revoke(int $id): void
    {
        $share = $this->findOwned($id);
        if (! $share || $share->isRevoked()) return;

        $share->update(['revoked_at' => now()]);
        $this->flash('Bağlantı erişime kapatıldı.');
        unset($this->shares);
    }

    /* ── Yeniden aktif et ─────────────────────────────────────────── */
    public function restore(int $id): void
    {
        $share = $this->findOwned($id);
        if (! $share) return;

        $share->update(['revoked_at' => null]);
        $this->flash('Bağlantı yeniden aktif edildi.');
        unset($this->shares);
    }

    /* ── Silme onayı ──────────────────────────────────────────────── */
    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmDeleteId = null;
    }

    public function delete(): void
    {
        $share = $this->findOwned($this->confirmDeleteId);
        if (! $share) { $this->confirmDeleteId = null; return; }

        $share->delete();
        $this->confirmDeleteId = null;
        $this->flash('Paylaşım silindi.');
        unset($this->shares);
    }

    /* ── Görünürlük toggle ────────────────────────────────────────── */
    public function toggleVisibility(int $id): void
    {
        $share = $this->findOwned($id);
        if (! $share) return;

        $share->update(['visibility' => $share->visibility === 'public' ? 'private' : 'public']);
        unset($this->shares);
    }

    /* ── Yardımcılar ──────────────────────────────────────────────── */
    private function findOwned(?int $id): ?NoteShareLink
    {
        if (! $id) return null;
        return NoteShareLink::where('id', $id)->where('user_id', Auth::id())->first();
    }

    private function flash(string $msg, string $type = 'success'): void
    {
        $this->flashMsg  = $msg;
        $this->flashType = $type;
        $this->dispatch('flash-show');
    }

    public function render()
    {
        return view('livewire.share-links-page');
    }
}
