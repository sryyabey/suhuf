<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResearchNote extends Model
{
    protected $fillable = [
        'user_id',
        'uuid',
        'sura',
        'aya',
        'word_position',
        'type',
        'title',
        'content',
    ];

    protected static function boot(): void
    {
        parent::boot();

        // Kayıt oluşturulurken UUID atanmamışsa ata
        static::creating(function (self $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });

        // Silinince tombstone yaz (sync için)
        static::deleted(function (self $model) {
            if (! empty($model->uuid)) {
                DB::table('user_sync_deletions')->insert([
                    'user_id'     => $model->user_id,
                    'entity_type' => 'research_note',
                    'entity_uuid' => $model->uuid,
                    'entity_key'  => null,
                    'deleted_at'  => now(),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ResearchTag::class, 'research_note_tag');
    }
}
