<?php

namespace App\Filament\Resources\UserInvites\Pages;

use App\Filament\Resources\UserInvites\UserInviteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserInvite extends EditRecord
{
    protected static string $resource = UserInviteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
