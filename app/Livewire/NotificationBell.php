<?php

namespace App\Livewire;

use App\Models\NoteShareLink;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NotificationBell extends Component
{
    /* ── Computed: bildirim listesi ───────────────────────────────── */

    #[Computed]
    public function notifications(): Collection
    {
        $user = auth()->user();

        if (! $user) {
            return collect();
        }

        $now   = now();
        $items = collect();

        /* 1. Süresi dolmak üzere (3 gün içinde, henüz geçerli, iptal edilmemiş) */
        NoteShareLink::query()
            ->where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', $now)
            ->where('expires_at', '<=', $now->copy()->addDays(3))
            ->orderBy('expires_at')
            ->get()
            ->each(function ($link) use (&$items) {
                $diffHours = (int) now()->diffInHours($link->expires_at);

                if ($diffHours < 1) {
                    $left = 'Az kaldı';
                } elseif ($diffHours < 24) {
                    $left = $diffHours . ' saat';
                } else {
                    $left = (int) now()->diffInDays($link->expires_at) . ' gün';
                }

                $items->push([
                    'type'    => 'warning',
                    'icon'    => 'ti-clock',
                    'title'   => $link->title ?: 'Paylaşım Linki',
                    'message' => $left . ' sonra sona eriyor',
                    'link'    => route('user.shares'),
                ]);
            });

        /* 2. Son 7 günde sona ermiş (iptal edilmemiş) */
        NoteShareLink::query()
            ->where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->where('expires_at', '>=', $now->copy()->subDays(7))
            ->orderByDesc('expires_at')
            ->get()
            ->each(function ($link) use (&$items) {
                $items->push([
                    'type'    => 'danger',
                    'icon'    => 'ti-link-off',
                    'title'   => $link->title ?: 'Paylaşım Linki',
                    'message' => 'Sona erdi — ' . $link->expires_at->diffForHumans(),
                    'link'    => route('user.shares'),
                ]);
            });

        return $items;
    }

    #[Computed]
    public function count(): int
    {
        return $this->notifications->count();
    }

    /* ── Render ───────────────────────────────────────────────────── */

    public function render(): View
    {
        return view('livewire.notification-bell');
    }
}
