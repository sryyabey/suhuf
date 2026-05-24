<?php

namespace App\Filament\Resources\ResearchTags\Pages;

use App\Filament\Resources\ResearchTags\ResearchTagResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditResearchTag extends EditRecord
{
    protected static string $resource = ResearchTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
