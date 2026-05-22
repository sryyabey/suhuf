<?php

namespace App\Filament\Resources\QuranWords\Pages;

use App\Filament\Resources\QuranWords\QuranWordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuranWords extends ListRecords
{
    protected static string $resource = QuranWordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
