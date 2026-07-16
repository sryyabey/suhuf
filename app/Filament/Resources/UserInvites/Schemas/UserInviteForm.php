<?php

namespace App\Filament\Resources\UserInvites\Schemas;

use App\Models\UserInvite;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserInviteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Referrer')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('code')
                    ->default(fn () => UserInvite::generateCode())
                    ->maxLength(64)
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('max_uses')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required(),
                TextInput::make('used_count')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->disabled()
                    ->dehydrated(false),
                DateTimePicker::make('expires_at'),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
            ]);
    }
}
