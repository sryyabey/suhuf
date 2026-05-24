<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HadithEntry extends Model
{
    protected $fillable = [
        'book_id',
        'chapter_id',
        'external_id',
        'hadith_no',
        'grade',
        'arabic_text',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'hadith_no' => 'integer',
            'meta' => 'array',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(HadithBook::class, 'book_id');
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(HadithChapter::class, 'chapter_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(HadithTranslation::class, 'hadith_id');
    }
}
