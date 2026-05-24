<?php

namespace App\Filament\Resources\ResearchNotes\Pages;

use App\Filament\Resources\ResearchNotes\ResearchNoteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditResearchNote extends EditRecord
{
    protected static string $resource = ResearchNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
