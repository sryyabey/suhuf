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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class BackupService
{
    public function createPendingFullBackup(User $user): UserBackup
    {
        return UserBackup::query()->create([
            'user_id' => $user->id,
            'type' => 'full',
            'status' => 'pending',
            'storage_disk' => 'local',
            'schema_version' => 1,
        ]);
    }

    public function processBackup(UserBackup $backup): UserBackup
    {
        if ($backup->status !== 'pending') {
            return $backup;
        }

        $user = User::query()->findOrFail($backup->user_id);

        $backup->update([
            'status' => 'running',
            'started_at' => now(),
            'error' => null,
        ]);

        try {
            $payload = $this->buildPayload($user);
            $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if ($json === false) {
                throw new \RuntimeException('Backup payload could not be encoded.');
            }

            $compressed = gzencode($json, 9);
            if ($compressed === false) {
                throw new \RuntimeException('Backup payload could not be compressed.');
            }

            $timestamp = now()->format('Ymd_His');
            $path = "private/backups/{$user->id}/{$timestamp}_{$backup->id}.json.gz";
            Storage::disk($backup->storage_disk)->put($path, $compressed);

            $backup->update([
                'status' => 'completed',
                'storage_path' => $path,
                'checksum' => hash('sha256', $json),
                'record_counts' => $payload['record_counts'] ?? [],
                'finished_at' => now(),
            ]);
        } catch (Throwable $e) {
            $backup->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
                'finished_at' => now(),
            ]);

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

        $compressed = Storage::disk($backup->storage_disk)->get($backup->storage_path);
        $json = gzdecode($compressed);

        if ($json === false) {
            throw new \RuntimeException('Backup file could not be decompressed.');
        }

        $payload = json_decode($json, true);
        if (! is_array($payload)) {
            throw new \RuntimeException('Backup file is not valid JSON.');
        }

        return DB::transaction(fn () => $this->replaceUserDataFromPayload($user, $payload));
    }

    private function buildPayload(User $user): array
    {
        $notes = ResearchNote::query()->where('user_id', $user->id)->with('tags:id')->get();
        $tags = ResearchTag::query()->where('user_id', $user->id)->get();
        $shareLinks = NoteShareLink::query()->where('user_id', $user->id)->get();
        $bookmarks = UserQuranPageBookmark::query()->where('user_id', $user->id)->get();
        $setting = UserSetting::query()->where('user_id', $user->id)->first();
        $mealPreferences = UserMealPreference::query()->where('user_id', $user->id)->get();

        return [
            'schema_version' => 1,
            'exported_at' => now()->toIso8601String(),
            'user_id' => $user->id,
            'record_counts' => [
                'research_notes' => $notes->count(),
                'research_tags' => $tags->count(),
                'note_share_links' => $shareLinks->count(),
                'bookmarks' => $bookmarks->count(),
                'meal_preferences' => $mealPreferences->count(),
                'setting' => $setting ? 1 : 0,
            ],
            'data' => [
                'research_tags' => $tags->map(fn (ResearchTag $tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                    'created_at' => optional($tag->created_at)?->toIso8601String(),
                    'updated_at' => optional($tag->updated_at)?->toIso8601String(),
                ])->values()->all(),
                'research_notes' => $notes->map(fn (ResearchNote $note) => [
                    'id' => $note->id,
                    'sura' => $note->sura,
                    'aya' => $note->aya,
                    'word_position' => $note->word_position,
                    'type' => $note->type,
                    'title' => $note->title,
                    'content' => $note->content,
                    'tag_ids' => $note->tags->pluck('id')->values()->all(),
                    'created_at' => optional($note->created_at)?->toIso8601String(),
                    'updated_at' => optional($note->updated_at)?->toIso8601String(),
                ])->values()->all(),
                'note_share_links' => $shareLinks->map(fn (NoteShareLink $link) => [
                    'token' => $link->token,
                    'title' => $link->title,
                    'visibility' => $link->visibility,
                    'expires_at' => optional($link->expires_at)?->toIso8601String(),
                    'revoked_at' => optional($link->revoked_at)?->toIso8601String(),
                    'payload' => $link->payload,
                    'access_count' => $link->access_count,
                    'last_accessed_at' => optional($link->last_accessed_at)?->toIso8601String(),
                    'created_at' => optional($link->created_at)?->toIso8601String(),
                    'updated_at' => optional($link->updated_at)?->toIso8601String(),
                ])->values()->all(),
                'bookmarks' => $bookmarks->map(fn (UserQuranPageBookmark $bookmark) => [
                    'page' => $bookmark->page,
                    'label' => $bookmark->label,
                    'created_at' => optional($bookmark->created_at)?->toIso8601String(),
                    'updated_at' => optional($bookmark->updated_at)?->toIso8601String(),
                ])->values()->all(),
                'setting' => $setting ? [
                    'preferred_language' => $setting->preferred_language,
                    'preferred_arabic_font' => $setting->preferred_arabic_font,
                    'preferred_tafsir_id' => $setting->preferred_tafsir_id,
                    'preferred_tafsir_name' => $setting->preferred_tafsir_name,
                    'last_read_sura' => $setting->last_read_sura,
                    'last_read_aya' => $setting->last_read_aya,
                ] : null,
                'meal_preferences' => $mealPreferences->map(fn (UserMealPreference $item) => [
                    'language' => $item->language,
                    'meal_key' => $item->meal_key,
                ])->values()->all(),
            ],
        ];
    }

    private function replaceUserDataFromPayload(User $user, array $payload): array
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
                'name' => $tagData['name'] ?? '',
                'slug' => $tagData['slug'] ?? Str::slug((string) ($tagData['name'] ?? 'tag')),
            ]);
            $tagIdMap[(int) ($tagData['id'] ?? 0)] = $tag->id;
        }

        $restoredNotes = 0;
        foreach (($data['research_notes'] ?? []) as $noteData) {
            $note = ResearchNote::query()->create([
                'user_id' => $user->id,
                'sura' => (int) ($noteData['sura'] ?? 1),
                'aya' => (int) ($noteData['aya'] ?? 1),
                'word_position' => $noteData['word_position'] ?? null,
                'type' => (string) ($noteData['type'] ?? 'note'),
                'title' => (string) ($noteData['title'] ?? ''),
                'content' => (string) ($noteData['content'] ?? ''),
            ]);

            $mappedTagIds = collect($noteData['tag_ids'] ?? [])->map(fn ($oldId) => $tagIdMap[(int) $oldId] ?? null)->filter()->unique()->values()->all();
            if (! empty($mappedTagIds)) {
                $note->tags()->sync($mappedTagIds);
            }
            $restoredNotes++;
        }

        $restoredShareLinks = 0;
        foreach (($data['note_share_links'] ?? []) as $linkData) {
            $token = (string) ($linkData['token'] ?? Str::random(40));
            while (NoteShareLink::query()->where('token', $token)->exists()) {
                $token = Str::random(40);
            }

            NoteShareLink::query()->create([
                'user_id' => $user->id,
                'token' => $token,
                'title' => $linkData['title'] ?? null,
                'visibility' => $linkData['visibility'] ?? 'public',
                'expires_at' => $linkData['expires_at'] ?? null,
                'revoked_at' => $linkData['revoked_at'] ?? null,
                'payload' => $linkData['payload'] ?? [],
                'access_count' => (int) ($linkData['access_count'] ?? 0),
                'last_accessed_at' => $linkData['last_accessed_at'] ?? null,
            ]);
            $restoredShareLinks++;
        }

        $restoredBookmarks = 0;
        foreach (($data['bookmarks'] ?? []) as $bookmarkData) {
            UserQuranPageBookmark::query()->create([
                'user_id' => $user->id,
                'page' => (int) ($bookmarkData['page'] ?? 1),
                'label' => $bookmarkData['label'] ?? null,
            ]);
            $restoredBookmarks++;
        }

        $restoredMeals = 0;
        foreach (($data['meal_preferences'] ?? []) as $mealData) {
            if (($mealData['meal_key'] ?? '') === '') {
                continue;
            }
            UserMealPreference::query()->create([
                'user_id' => $user->id,
                'language' => (string) ($mealData['language'] ?? 'tr'),
                'meal_key' => (string) $mealData['meal_key'],
            ]);
            $restoredMeals++;
        }

        if (is_array($data['setting'] ?? null)) {
            $settingData = $data['setting'];
            UserSetting::query()->create([
                'user_id' => $user->id,
                'preferred_language' => $settingData['preferred_language'] ?? 'tr',
                'preferred_arabic_font' => $settingData['preferred_arabic_font'] ?? 'amiri',
                'preferred_tafsir_id' => $settingData['preferred_tafsir_id'] ?? null,
                'preferred_tafsir_name' => $settingData['preferred_tafsir_name'] ?? null,
                'last_read_sura' => $settingData['last_read_sura'] ?? null,
                'last_read_aya' => $settingData['last_read_aya'] ?? null,
            ]);
        }

        return [
            'research_tags' => count($tagIdMap),
            'research_notes' => $restoredNotes,
            'note_share_links' => $restoredShareLinks,
            'bookmarks' => $restoredBookmarks,
            'meal_preferences' => $restoredMeals,
            'setting' => is_array($data['setting'] ?? null) ? 1 : 0,
        ];
    }
}
