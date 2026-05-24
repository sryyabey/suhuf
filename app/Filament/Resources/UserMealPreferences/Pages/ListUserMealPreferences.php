<?php

namespace App\Filament\Resources\UserMealPreferences\Pages;

use App\Filament\Resources\UserMealPreferences\UserMealPreferenceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserMealPreferences extends ListRecords
{
    protected static string $resource = UserMealPreferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
