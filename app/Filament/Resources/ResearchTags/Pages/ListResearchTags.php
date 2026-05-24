<?php

namespace App\Filament\Resources\ResearchTags\Pages;

use App\Filament\Resources\ResearchTags\ResearchTagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListResearchTags extends ListRecords
{
    protected static string $resource = ResearchTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
