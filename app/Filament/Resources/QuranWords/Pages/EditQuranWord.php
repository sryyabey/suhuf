<?php

namespace App\Filament\Resources\QuranWords\Pages;

use App\Filament\Resources\QuranWords\QuranWordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuranWord extends EditRecord
{
    protected static string $resource = QuranWordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
