<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class VerseTranslation extends Model
{
    protected $fillable = [
        'sura',
        'aya',
        'meal_key',
        'language',
        'text',
    ];

    public function scopeForVerse(Builder $query, int $sura, int $aya): Builder
    {
        return $query->where('sura', $sura)->where('aya', $aya);
    }
}
