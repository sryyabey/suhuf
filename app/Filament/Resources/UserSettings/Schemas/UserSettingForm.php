<?php

namespace App\Filament\Resources\UserSettings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('preferred_language')
                    ->required()
                    ->default('tr'),
                TextInput::make('preferred_arabic_font')
                    ->required()
                    ->default('amiri'),
                TextInput::make('preferred_tafsir_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('preferred_tafsir_name')
                    ->default(null),
                TextInput::make('last_read_sura')
                    ->numeric()
                    ->default(null),
                TextInput::make('last_read_aya')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
