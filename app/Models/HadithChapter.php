<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HadithChapter extends Model
{
    protected $fillable = [
        'book_id',
        'code',
        'chapter_no',
        'name_ar',
        'name_en',
        'name_tr',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'chapter_no' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(HadithBook::class, 'book_id');
    }

    public function hadiths(): HasMany
    {
        return $this->hasMany(HadithEntry::class, 'chapter_id');
    }
}
