<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuranWord extends Model
{
    protected $fillable = [
        'aya',
        'sura',
        'position',
        'verse_key',
        'text',
        'simple',
        'juz',
        'hezb',
        'rub',
        'page',
        'class_name',
        'line',
        'code',
        'code_v3',
        'char_type',
        'audio',
        'translation',
    ];

    public function verseTranslations(): HasMany
    {
        return $this->hasMany(VerseTranslation::class, 'sura', 'sura')
            ->where('aya', $this->aya);
    }

    public function translationQuery(?string $mealKey = null, string $language = 'tr'): Builder
    {
        return VerseTranslation::query()
            ->where('sura', $this->sura)
            ->where('aya', $this->aya)
            ->when($mealKey, fn (Builder $q) => $q->where('meal_key', $mealKey))
            ->where('language', $language);
    }

    public function getVerseTranslation(?string $mealKey = null, string $language = 'tr'): ?string
    {
        return $this->translationQuery($mealKey, $language)->value('text');
    }
}
