<?php

namespace App\Filament\Resources\VerseTranslations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class VerseTranslationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sura')
                    ->required()
                    ->numeric(),
                TextInput::make('aya')
                    ->required()
                    ->numeric(),
                TextInput::make('meal_key')
                    ->required(),
                TextInput::make('language')
                    ->required()
                    ->default('tr'),
                Textarea::make('text')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
