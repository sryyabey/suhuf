<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HadithBook extends Model
{
    protected $fillable = [
        'source',
        'code',
        'name_ar',
        'name_en',
        'name_tr',
        'sort_order',
    ];

    public function chapters(): HasMany
    {
        return $this->hasMany(HadithChapter::class, 'book_id');
    }

    public function hadiths(): HasMany
    {
        return $this->hasMany(HadithEntry::class, 'book_id');
    }
}
