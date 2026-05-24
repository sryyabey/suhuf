<?php

namespace App\Filament\Resources\UserQuranPageBookmarks\Pages;

use App\Filament\Resources\UserQuranPageBookmarks\UserQuranPageBookmarkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserQuranPageBookmark extends EditRecord
{
    protected static string $resource = UserQuranPageBookmarkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
