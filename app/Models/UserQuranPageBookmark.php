<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserQuranPageBookmark extends Model
{
    protected $fillable = [
        'user_id',
        'page',
        'label',
    ];

    protected $casts = [
        'page' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
