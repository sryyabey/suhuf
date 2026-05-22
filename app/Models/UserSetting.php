<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'preferred_language',
        'last_read_sura',
        'last_read_aya',
    ];

    protected $casts = [
        'last_read_sura' => 'integer',
        'last_read_aya'  => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
