<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HadithTranslation extends Model
{
    protected $fillable = [
        'hadith_id',
        'language',
        'title',
        'text',
        'narrator',
    ];

    public function hadith(): BelongsTo
    {
        return $this->belongsTo(HadithEntry::class, 'hadith_id');
    }
}
