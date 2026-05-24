<?php

namespace App\Filament\Resources\VerseTranslations\Pages;

use App\Filament\Resources\VerseTranslations\VerseTranslationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVerseTranslations extends ListRecords
{
    protected static string $resource = VerseTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
