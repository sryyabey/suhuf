<?php

namespace App\Services;

use App\Models\NoteShareLink;
use App\Models\ResearchNote;
use App\Models\ResearchTag;
use App\Models\User;
use App\Models\UserBackup;
use App\Models\UserMealPreference;
use App\Models\UserQuranPageBookmark;
use App\Models\UserSetting;
use App\Models\UserSyncDeletion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class BackupService
{
    // ─── Backup (tam yedek, disk) ─────────────────────────────────────────────

    public function createPendingFullBackup(User $user): UserBackup
    {
        return UserBackup::query()->create([
            'user_id'        => $user->id,
            'type'           => 'full',
            'status'         => 'pending',
            'storage_disk'   => 'local',
            'schema_version' => 2,
        ]);
    }

    public function processBackup(UserBackup $backup): UserBackup
    {
        if ($backup->status !== 'pending') {
            return $backup;
        }

        $user = User::query()->findOrFail($backup->user_id);

        $backup->update(['status' => 'running', 'started_at' => now(), 'error' => null]);

        try {
            $payload    = $this->exportPayload($user);
            $json       = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $compressed = gzencode($json, 9);

            $timestamp = now()->format('Ymd_His');
            $path      = "private/backups/{$user->id}/{$timestamp}_{$backup->id}.json.gz";
            Storage::disk($backup->storage_disk)->put($path, $compressed);

            $backup->update([
                'status'        => 'completed',
                'storage_path'  => $path,
                'checksum'      => hash('sha256', $json),
                'record_counts' => $payload['record_counts'] ?? [],
                'finished_at'   => now(),
            ]);
        } catch (Throwable $e) {
            $backup->update(['status' => 'failed', 'error' => $e->getMessage(), 'finished_at' => now()]);
            throw $e;
        }

        return $backup->fresh();
    }

    public function restore(User $user, UserBackup $backup): array
    {
        if ($backup->user_id !== $user->id) {
            throw new \RuntimeException('Backup does not belong to this user.');
        }
        if (! $backup->storage_path || ! Storage::disk($backup->storage_disk)->exists($backup->storage_path)) {
            throw new \RuntimeException('Backup file not found.');
        }

        $json    = gzdecode(Storage::disk($backup->storage_disk)->get($backup->storage_path));
        $payload = json_decode($json, true);

        if (! is_array($payload)) {
            throw new \RuntimeException('Backup file is not valid JSON.');
        }

        // Yedek restorasyonunda tam replace — merge değil
        return DB::transaction(fn () => $this->importPayloadFullReplace($user, $payload));
    }

    // ─── Payload dışa aktarma (schema v2) ─────────────────────────────────────

    /**
     * Kullanıcının tüm verisini JSON payload olarak döner.
     * schema_version = 2: uuid alanları + deletions bölümü içerir.
     */
    public function exportPayload(User $user): array
    {
        $notes           = ResearchNote::query()->where('user_id', $user->id)->with('tags:id,uuid')->get();
        $tags            = ResearchTag::query()->where('user_id', $user->id)->get();
        $shareLinks      = NoteShareLink::query()->where('user_id', $user->id)->get();
        $bookmarks       = UserQuranPageBookmark::query()->where('user_id', $user->id)->get();
        $setting         = UserSetting::query()->where('user_id', $user->id)->first();
        $mealPreferences = UserMealPreference::query()->where('user_id', $user->id)->get();
        $deletions       = UserSyncDeletion::query()->where('user_id', $user->id)->get();

        return [
            'schema_version' => 2,
            'exported_at'    => now()->toIso8601String(),
            'user_id'        => $user->id,
            'record_counts'  => [
                'research_notes'  => $notes->count(),
                'research_tags'   => $tags->count(),
                'note_share_links'=> $shareLinks->count(),
                'bookmarks'       => $bookmarks->count(),
                'meal_preferences'=> $mealPreferences->count(),
                'setting'         => $setting ? 1 : 0,
            ],
            'data' => [
                'research_tags' => $tags->map(fn (ResearchTag $t) => [
                    'uuid'       => $t->uuid,
                    'name'       => $t->name,
                    'slug'       => $t->slug,
                    'created_at' => optional($t->created_at)?->toIso8601String(),
                    'updated_at' => optional($t->updated_at)?->toIso8601String(),
                ])->values()->all(),

                'research_notes' => $notes->map(fn (ResearchNote $n) => [
                    'uuid'          => $n->uuid,
                    'sura'          => $n->sura,
                    'aya'           => $n->aya,
                    'word_position' => $n->word_position,
                    'type'          => $n->type,
                    'title'         => $n->title,
                    'content'       => $n->content,
                    'tag_uuids'     => $n->tags->pluck('uuid')->filter()->values()->all(),
                    'created_at'    => optional($n->created_at)?->toIso8601String(),
                    'updated_at'    => optional($n->updated_at)?->toIso8601String(),
                ])->values()->all(),

                'note_share_links' => $shareLinks->map(fn (NoteShareLink $l) => [
                    'token'            => $l->token,
                    'title'            => $l->title,
                    'visibility'       => $l->visibility,
                    'expires_at'       => optional($l->expires_at)?->toIso8601String(),
                    'revoked_at'       => optional($l->revoked_at)?->toIso8601String(),
                    'payload'          => $l->payload,
                    'access_count'     => $l->access_count,
                    'last_accessed_at' => optional($l->last_accessed_at)?->toIso8601String(),
                    'created_at'       => optional($l->created_at)?->toIso8601String(),
                    'updated_at'       => optional($l->updated_at)?->toIso8601String(),
                ])->values()->all(),

                'bookmarks' => $bookmarks->map(fn (UserQuranPageBookmark $b) => [
                    'page'       => $b->page,
                    'label'      => $b->label,
                    'created_at' => optional($b->created_at)?->toIso8601String(),
                    'updated_at' => optional($b->updated_at)?->toIso8601String(),
                ])->values()->all(),

                'setting' => $setting ? [
                    'preferred_language'   => $setting->preferred_language,
                    'preferred_arabic_font'=> $setting->preferred_arabic_font,
                    'preferred_tafsir_id'  => $setting->preferred_tafsir_id,
                    'preferred_tafsir_name'=> $setting->preferred_tafsir_name,
                    'last_read_sura'       => $setting->last_read_sura,
                    'last_read_aya'        => $setting->last_read_aya,
                    'updated_at'           => optional($setting->updated_at)?->toIso8601String(),
                ] : null,

                'meal_preferences' => $mealPreferences->map(fn (UserMealPreference $m) => [
                    'language' => $m->language,
                    'meal_key' => $m->meal_key,
                ])->values()->all(),

                // Tombstone listesi — karşı taraf bu UUID'leri siler
                'deletions' => [
                    'research_notes' => $deletions
                        ->where('entity_type', 'research_note')
                        ->pluck('entity_uuid')
                        ->filter()
                        ->values()
                        ->all(),
                    'research_tags' => $deletions
                        ->where('entity_type', 'research_tag')
                        ->pluck('entity_uuid')
                        ->filter()
                        ->values()
                        ->all(),
                    'bookmarks' => $deletions
                        ->where('entity_type', 'bookmark')
                        ->pluck('entity_key')
                        ->filter()
                        ->map(fn ($k) => (int) $k)
                        ->values()
                        ->all(),
                ],
            ],
        ];
    }

    // ─── Sync merge (schema v2, UPSERT — veri kaybı yok) ─────────────────────

    /**
     * Gelen payload'ı UUID eşleşmesiyle mevcut veriye birleştirir.
     *
     * Kural:
     *  - Kayıt yalnızca local'da varsa → ekle
     *  - Kayıt yalnızca payload'da varsa → ekle
     *  - Her ikisinde de varsa → daha yeni updated_at kazanır
     *  - Deletions listesindeki UUID'ler her iki taraftan da silinir
     */
    public function mergePayload(User $user, array $payload): array
    {
        return DB::transaction(function () use ($user, $payload) {
            $data = $payload['data'] ?? [];

            // ── 1. Silmeleri uygula (tombstone'lar) ──────────────────────────
            $this->applyDeletions($user, $data['deletions'] ?? []);

            // ── 2. Etiketleri birleştir ───────────────────────────────────────
            $tagUuidToId = $this->mergeTags($user, $data['research_tags'] ?? []);

            // ── 3. Notları birleştir ──────────────────────────────────────────
            $noteCounts = $this->mergeNotes($user, $data['research_notes'] ?? [], $tagUuidToId);

            // ── 4. Yer imlerini birleştir ─────────────────────────────────────
            $bookmarkCount = $this->mergeBookmarks($user, $data['bookmarks'] ?? []);

            // ── 5. Meal tercihlerini birleştir ────────────────────────────────
            $mealCount = $this->mergeMealPreferences($user, $data['meal_preferences'] ?? []);

            // ── 6. Ayarları birleştir ─────────────────────────────────────────
            $settingCount = $this->mergeSetting($user, $data['setting'] ?? null);

            return [
                'research_tags'  => count($tagUuidToId),
                'research_notes' => $noteCounts,
                'bookmarks'      => $bookmarkCount,
                'meal_preferences' => $mealCount,
                'setting'        => $settingCount,
            ];
        });
    }

    // ─── Tombstone uygula ─────────────────────────────────────────────────────

    private function applyDeletions(User $user, array $deletions): void
    {
        foreach ($deletions['research_notes'] ?? [] as $uuid) {
            ResearchNote::query()
                ->where('user_id', $user->id)
                ->where('uuid', $uuid)
                ->delete();
        }

        foreach ($deletions['research_tags'] ?? [] as $uuid) {
            ResearchTag::query()
                ->where('user_id', $user->id)
                ->where('uuid', $uuid)
                ->delete();
        }

        foreach ($deletions['bookmarks'] ?? [] as $page) {
            UserQuranPageBookmark::query()
                ->where('user_id', $user->id)
                ->where('page', (int) $page)
                ->delete();

            // Yer imi tombstone kaydını da ekle (diğer sync döngülerinde de geçerli olsun)
            DB::table('user_sync_deletions')->insertOrIgnore([
                'user_id'     => $user->id,
                'entity_type' => 'bookmark',
                'entity_uuid' => null,
                'entity_key'  => (string) $page,
                'deleted_at'  => now(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    // ─── Tag merge ────────────────────────────────────────────────────────────

    /** @return array<string, int>  uuid → local id */
    private function mergeTags(User $user, array $incomingTags): array
    {
        $uuidToId = [];

        foreach ($incomingTags as $tagData) {
            $uuid = (string) ($tagData['uuid'] ?? '');
            if ($uuid === '') {
                continue;
            }

            $existing = ResearchTag::query()
                ->where('user_id', $user->id)
                ->where('uuid', $uuid)
                ->first();

            if (! $existing) {
                $tag = ResearchTag::query()->create([
                    'user_id' => $user->id,
                    'uuid'    => $uuid,
                    'name'    => $tagData['name'] ?? '',
                    'slug'    => $tagData['slug'] ?? Str::slug((string) ($tagData['name'] ?? 'tag')),
                ]);
                $uuidToId[$uuid] = $tag->id;
            } else {
                // Daha yeni olan kazanır
                $incomingUpdated = isset($tagData['updated_at'])
                    ? Carbon::parse($tagData['updated_at'])
                    : null;

                if ($incomingUpdated && $incomingUpdated->gt($existing->updated_at)) {
                    $existing->update([
                        'name' => $tagData['name'] ?? $existing->name,
                        'slug' => $tagData['slug'] ?? $existing->slug,
                    ]);
                }
                $uuidToId[$uuid] = $existing->id;
            }
        }

        return $uuidToId;
    }

    // ─── Note merge ───────────────────────────────────────────────────────────

    private function mergeNotes(User $user, array $incomingNotes, array $tagUuidToId): int
    {
        $count = 0;

        foreach ($incomingNotes as $noteData) {
            $uuid = (string) ($noteData['uuid'] ?? '');
            if ($uuid === '') {
                continue;
            }

            $existing = ResearchNote::query()
                ->where('user_id', $user->id)
                ->where('uuid', $uuid)
                ->first();

            $tagIds = collect($noteData['tag_uuids'] ?? [])
                ->map(fn ($u) => $tagUuidToId[$u] ?? null)
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (! $existing) {
                $note = ResearchNote::query()->create([
                    'user_id'       => $user->id,
                    'uuid'          => $uuid,
                    'sura'          => (int) ($noteData['sura'] ?? 1),
                    'aya'           => (int) ($noteData['aya'] ?? 1),
                    'word_position' => $noteData['word_position'] ?? null,
                    'type'          => (string) ($noteData['type'] ?? 'note'),
                    'title'         => (string) ($noteData['title'] ?? ''),
                    'content'       => (string) ($noteData['content'] ?? ''),
                ]);
                if (! empty($tagIds)) {
                    $note->tags()->sync($tagIds);
                }
                $count++;
            } else {
                $incomingUpdated = isset($noteData['updated_at'])
                    ? Carbon::parse($noteData['updated_at'])
                    : null;

                if ($incomingUpdated && $incomingUpdated->gt($existing->updated_at)) {
                    $existing->update([
                        'sura'          => (int) ($noteData['sura'] ?? $existing->sura),
                        'aya'           => (int) ($noteData['aya'] ?? $existing->aya),
                        'word_position' => $noteData['word_position'] ?? $existing->word_position,
                        'type'          => (string) ($noteData['type'] ?? $existing->type),
                        'title'         => (string) ($noteData['title'] ?? $existing->title),
                        'content'       => (string) ($noteData['content'] ?? $existing->content),
                    ]);
                    if (! empty($tagIds)) {
                        $existing->tags()->sync($tagIds);
                    }
                }
                $count++;
            }
        }

        return $count;
    }

    // ─── Bookmark merge ───────────────────────────────────────────────────────

    private function mergeBookmarks(User $user, array $bookmarks): int
    {
        $count = 0;
        foreach ($bookmarks as $bm) {
            $page = (int) ($bm['page'] ?? 0);
            if ($page <= 0) {
                continue;
            }

            $existing = UserQuranPageBookmark::query()
                ->where('user_id', $user->id)
                ->where('page', $page)
                ->first();

            if (! $existing) {
                UserQuranPageBookmark::query()->create([
                    'user_id' => $user->id,
                    'page'    => $page,
                    'label'   => $bm['label'] ?? null,
                ]);
            } else {
                $incomingUpdated = isset($bm['updated_at'])
                    ? Carbon::parse($bm['updated_at'])
                    : null;

                if ($incomingUpdated && $incomingUpdated->gt($existing->updated_at)) {
                    $existing->update(['label' => $bm['label'] ?? $existing->label]);
                }
            }
            $count++;
        }

        return $count;
    }

    // ─── Meal preference merge ────────────────────────────────────────────────

    private function mergeMealPreferences(User $user, array $prefs): int
    {
        $count = 0;
        foreach ($prefs as $pref) {
            $key = (string) ($pref['meal_key'] ?? '');
            if ($key === '') {
                continue;
            }
            UserMealPreference::query()->firstOrCreate([
                'user_id'  => $user->id,
                'language' => (string) ($pref['language'] ?? 'tr'),
                'meal_key' => $key,
            ]);
            $count++;
        }

        return $count;
    }

    // ─── Setting merge ────────────────────────────────────────────────────────

    private function mergeSetting(User $user, ?array $settingData): int
    {
        if (! is_array($settingData)) {
            return 0;
        }

        $existing = UserSetting::query()->where('user_id', $user->id)->first();

        if (! $existing) {
            UserSetting::query()->create([
                'user_id'               => $user->id,
                'preferred_language'    => $settingData['preferred_language'] ?? 'tr',
                'preferred_arabic_font' => $settingData['preferred_arabic_font'] ?? 'amiri',
                'preferred_tafsir_id'   => $settingData['preferred_tafsir_id'] ?? null,
                'preferred_tafsir_name' => $settingData['preferred_tafsir_name'] ?? null,
                'last_read_sura'        => $settingData['last_read_sura'] ?? null,
                'last_read_aya'         => $settingData['last_read_aya'] ?? null,
            ]);
            return 1;
        }

        $incomingUpdated = isset($settingData['updated_at'])
            ? Carbon::parse($settingData['updated_at'])
            : null;

        if (! $incomingUpdated || $incomingUpdated->gte($existing->updated_at)) {
            $existing->update([
                'preferred_language'    => $settingData['preferred_language'] ?? $existing->preferred_language,
                'preferred_arabic_font' => $settingData['preferred_arabic_font'] ?? $existing->preferred_arabic_font,
                'preferred_tafsir_id'   => $settingData['preferred_tafsir_id'] ?? $existing->preferred_tafsir_id,
                'preferred_tafsir_name' => $settingData['preferred_tafsir_name'] ?? $existing->preferred_tafsir_name,
                'last_read_sura'        => $settingData['last_read_sura'] ?? $existing->last_read_sura,
                'last_read_aya'         => $settingData['last_read_aya'] ?? $existing->last_read_aya,
            ]);
        }

        return 1;
    }

    // ─── Full replace (sadece backup restore için) ────────────────────────────

    /**
     * Yedek geri yükleme: tüm veriyi siler, payload'dan yeniden oluşturur.
     * Sync için DEĞİL — yalnızca BackupController::restore kullanır.
     */
    public function importPayload(User $user, array $payload): array
    {
        // v2 payload ise merge kullan (geri dönük uyumluluk)
        if ((int) ($payload['schema_version'] ?? 1) >= 2) {
            return $this->mergePayload($user, $payload);
        }

        return $this->importPayloadFullReplace($user, $payload);
    }

    private function importPayloadFullReplace(User $user, array $payload): array
    {
        $data = $payload['data'] ?? [];

        $noteIds = ResearchNote::query()->where('user_id', $user->id)->pluck('id');
        if ($noteIds->isNotEmpty()) {
            DB::table('research_note_tag')->whereIn('research_note_id', $noteIds)->delete();
        }

        ResearchNote::query()->where('user_id', $user->id)->delete();
        ResearchTag::query()->where('user_id', $user->id)->delete();
        NoteShareLink::query()->where('user_id', $user->id)->delete();
        UserQuranPageBookmark::query()->where('user_id', $user->id)->delete();
        UserMealPreference::query()->where('user_id', $user->id)->delete();
        UserSetting::query()->where('user_id', $user->id)->delete();

        $tagIdMap = [];
        foreach (($data['research_tags'] ?? []) as $tagData) {
            $tag = ResearchTag::query()->create([
                'user_id' => $user->id,
                'uuid'    => $tagData['uuid'] ?? (string) Str::uuid(),
                'name'    => $tagData['name'] ?? '',
                'slug'    => $tagData['slug'] ?? Str::slug((string) ($tagData['name'] ?? 'tag')),
            ]);
            $tagIdMap[(int) ($tagData['id'] ?? 0)] = $tag->id;
        }

        $restoredNotes = 0;
        foreach (($data['research_notes'] ?? []) as $noteData) {
            $note = ResearchNote::query()->create([
                'user_id'       => $user->id,
                'uuid'          => $noteData['uuid'] ?? (string) Str::uuid(),
                'sura'          => (int) ($noteData['sura'] ?? 1),
                'aya'           => (int) ($noteData['aya'] ?? 1),
                'word_position' => $noteData['word_position'] ?? null,
                'type'          => (string) ($noteData['type'] ?? 'note'),
                'title'         => (string) ($noteData['title'] ?? ''),
                'content'       => (string) ($noteData['content'] ?? ''),
            ]);

            // v1: tag_ids → v2: tag_uuids
            if (! empty($noteData['tag_uuids'])) {
                $tagIds = collect($noteData['tag_uuids'])
                    ->map(fn ($u) => ResearchTag::query()->where('uuid', $u)->value('id'))
                    ->filter()->unique()->values()->all();
            } else {
                $tagIds = collect($noteData['tag_ids'] ?? [])
                    ->map(fn ($id) => $tagIdMap[(int) $id] ?? null)
                    ->filter()->unique()->values()->all();
            }

            if (! empty($tagIds)) {
                $note->tags()->sync($tagIds);
            }
            $restoredNotes++;
        }

        foreach (($data['note_share_links'] ?? []) as $linkData) {
            $token = (string) ($linkData['token'] ?? Str::random(40));
            while (NoteShareLink::query()->where('token', $token)->exists()) {
                $token = Str::random(40);
            }
            NoteShareLink::query()->create([
                'user_id'          => $user->id,
                'token'            => $token,
                'title'            => $linkData['title'] ?? null,
                'visibility'       => $linkData['visibility'] ?? 'public',
                'expires_at'       => $linkData['expires_at'] ?? null,
                'revoked_at'       => $linkData['revoked_at'] ?? null,
                'payload'          => $linkData['payload'] ?? [],
                'access_count'     => (int) ($linkData['access_count'] ?? 0),
                'last_accessed_at' => $linkData['last_accessed_at'] ?? null,
            ]);
        }

        $restoredBookmarks = 0;
        foreach (($data['bookmarks'] ?? []) as $bm) {
            UserQuranPageBookmark::query()->create([
                'user_id' => $user->id,
                'page'    => (int) ($bm['page'] ?? 1),
                'label'   => $bm['label'] ?? null,
            ]);
            $restoredBookmarks++;
        }

        $restoredMeals = 0;
        foreach (($data['meal_preferences'] ?? []) as $pref) {
            if (($pref['meal_key'] ?? '') === '') {
                continue;
            }
            UserMealPreference::query()->create([
                'user_id'  => $user->id,
                'language' => (string) ($pref['language'] ?? 'tr'),
                'meal_key' => (string) $pref['meal_key'],
            ]);
            $restoredMeals++;
        }

        if (is_array($data['setting'] ?? null)) {
            $s = $data['setting'];
            UserSetting::query()->create([
                'user_id'               => $user->id,
                'preferred_language'    => $s['preferred_language'] ?? 'tr',
                'preferred_arabic_font' => $s['preferred_arabic_font'] ?? 'amiri',
                'preferred_tafsir_id'   => $s['preferred_tafsir_id'] ?? null,
                'preferred_tafsir_name' => $s['preferred_tafsir_name'] ?? null,
                'last_read_sura'        => $s['last_read_sura'] ?? null,
                'last_read_aya'         => $s['last_read_aya'] ?? null,
            ]);
        }

        return [
            'research_tags'   => count($tagIdMap),
            'research_notes'  => $restoredNotes,
            'note_share_links'=> count($data['note_share_links'] ?? []),
            'bookmarks'       => $restoredBookmarks,
            'meal_preferences'=> $restoredMeals,
            'setting'         => is_array($data['setting'] ?? null) ? 1 : 0,
        ];
    }
}
