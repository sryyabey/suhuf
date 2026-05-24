<?php

namespace App\Filament\Resources\UserQuranPageBookmarks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserQuranPageBookmarkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('page')
                    ->required()
                    ->numeric(),
                TextInput::make('label')
                    ->default(null),
            ]);
    }
}
