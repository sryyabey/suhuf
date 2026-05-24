<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBackup extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'status',
        'storage_disk',
        'storage_path',
        'checksum',
        'record_counts',
        'schema_version',
        'started_at',
        'finished_at',
        'error',
    ];

    protected function casts(): array
    {
        return [
            'record_counts' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'schema_version' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
