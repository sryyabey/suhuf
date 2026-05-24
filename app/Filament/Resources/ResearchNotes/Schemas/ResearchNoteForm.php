<?php

namespace App\Filament\Resources\ResearchNotes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ResearchNoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('sura')
                    ->required()
                    ->numeric(),
                TextInput::make('aya')
                    ->required()
                    ->numeric(),
                TextInput::make('word_position')
                    ->numeric()
                    ->default(null),
                TextInput::make('type')
                    ->required()
                    ->default('note'),
                TextInput::make('title')
                    ->required(),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
