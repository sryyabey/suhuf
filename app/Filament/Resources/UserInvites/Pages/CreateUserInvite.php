<?php

namespace App\Filament\Resources\UserInvites\Pages;

use App\Filament\Resources\UserInvites\UserInviteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserInvite extends CreateRecord
{
    protected static string $resource = UserInviteResource::class;
}
