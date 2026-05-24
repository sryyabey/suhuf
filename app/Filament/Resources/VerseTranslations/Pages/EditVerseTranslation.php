<?php

namespace App\Filament\Resources\VerseTranslations\Pages;

use App\Filament\Resources\VerseTranslations\VerseTranslationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVerseTranslation extends EditRecord
{
    protected static string $resource = VerseTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
