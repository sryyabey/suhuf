<?php

namespace App\Filament\Resources\UserSettings\Pages;

use App\Filament\Resources\UserSettings\UserSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserSetting extends EditRecord
{
    protected static string $resource = UserSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
