<?php

namespace App\Filament\Resources\UserQuranPageBookmarks\Pages;

use App\Filament\Resources\UserQuranPageBookmarks\UserQuranPageBookmarkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserQuranPageBookmarks extends ListRecords
{
    protected static string $resource = UserQuranPageBookmarkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
