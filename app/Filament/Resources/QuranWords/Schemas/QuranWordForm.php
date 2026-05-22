<?php

namespace App\Filament\Resources\QuranWords\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class QuranWordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('aya')
                    ->numeric()
                    ->required(),
                TextInput::make('sura')
                    ->numeric()
                    ->required(),
                TextInput::make('position')
                    ->numeric()
                    ->required(),
                TextInput::make('verse_key')
                    ->required()
                    ->maxLength(255),
                Textarea::make('text')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('simple')
                    ->required()
                    ->maxLength(255),
                TextInput::make('juz')
                    ->numeric()
                    ->required(),
                TextInput::make('hezb')
                    ->numeric()
                    ->required(),
                TextInput::make('rub')
                    ->numeric()
                    ->required(),
                TextInput::make('page')
                    ->numeric()
                    ->required(),
                TextInput::make('class_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('line')
                    ->numeric()
                    ->required(),
                TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code_v3')
                    ->required()
                    ->maxLength(255),
                TextInput::make('char_type')
                    ->required()
                    ->maxLength(255),
                TextInput::make('audio')
                    ->required()
                    ->maxLength(255),
                Textarea::make('translation')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
