<?php

namespace App\Filament\Resources\UserMealPreferences\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserMealPreferenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('language')
                    ->required(),
                TextInput::make('meal_key')
                    ->required(),
            ]);
    }
}
