<?php

namespace App\Filament\Resources\ResearchNotes;

use App\Filament\Resources\ResearchNotes\Pages\CreateResearchNote;
use App\Filament\Resources\ResearchNotes\Pages\EditResearchNote;
use App\Filament\Resources\ResearchNotes\Pages\ListResearchNotes;
use App\Filament\Resources\ResearchNotes\Schemas\ResearchNoteForm;
use App\Filament\Resources\ResearchNotes\Tables\ResearchNotesTable;
use App\Models\ResearchNote;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ResearchNoteResource extends Resource
{
    protected static ?string $model = ResearchNote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static UnitEnum|string|null $navigationGroup = 'Araştırma';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'ResearchNote';

    public static function form(Schema $schema): Schema
    {
        return ResearchNoteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResearchNotesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListResearchNotes::route('/'),
            'create' => CreateResearchNote::route('/create'),
            'edit' => EditResearchNote::route('/{record}/edit'),
        ];
    }
}
