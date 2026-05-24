<?php

namespace App\Filament\Resources\ResearchTags;

use App\Filament\Resources\ResearchTags\Pages\CreateResearchTag;
use App\Filament\Resources\ResearchTags\Pages\EditResearchTag;
use App\Filament\Resources\ResearchTags\Pages\ListResearchTags;
use App\Filament\Resources\ResearchTags\Schemas\ResearchTagForm;
use App\Filament\Resources\ResearchTags\Tables\ResearchTagsTable;
use App\Models\ResearchTag;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ResearchTagResource extends Resource
{
    protected static ?string $model = ResearchTag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static UnitEnum|string|null $navigationGroup = 'Araştırma';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'ResearchTag';

    public static function form(Schema $schema): Schema
    {
        return ResearchTagForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResearchTagsTable::configure($table);
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
            'index' => ListResearchTags::route('/'),
            'create' => CreateResearchTag::route('/create'),
            'edit' => EditResearchTag::route('/{record}/edit'),
        ];
    }
}
