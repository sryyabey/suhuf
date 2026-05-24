<?php

namespace App\Filament\Resources\ResearchNotes\Pages;

use App\Filament\Resources\ResearchNotes\ResearchNoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListResearchNotes extends ListRecords
{
    protected static string $resource = ResearchNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
