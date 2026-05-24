<?php

namespace App\Filament\Resources\UserSettings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('preferred_language')
                    ->searchable(),
                TextColumn::make('preferred_arabic_font')
                    ->searchable(),
                TextColumn::make('preferred_tafsir_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('preferred_tafsir_name')
                    ->searchable(),
                TextColumn::make('last_read_sura')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('last_read_aya')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
