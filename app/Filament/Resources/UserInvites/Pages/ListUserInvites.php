<?php

namespace App\Filament\Resources\UserInvites\Pages;

use App\Filament\Resources\UserInvites\UserInviteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserInvites extends ListRecords
{
    protected static string $resource = UserInviteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
