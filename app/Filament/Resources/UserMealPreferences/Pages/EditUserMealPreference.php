<?php

namespace App\Filament\Resources\UserMealPreferences\Pages;

use App\Filament\Resources\UserMealPreferences\UserMealPreferenceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserMealPreference extends EditRecord
{
    protected static string $resource = UserMealPreferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
