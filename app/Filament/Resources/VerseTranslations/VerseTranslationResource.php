<?php

namespace App\Filament\Resources\VerseTranslations;

use App\Filament\Resources\VerseTranslations\Pages\CreateVerseTranslation;
use App\Filament\Resources\VerseTranslations\Pages\EditVerseTranslation;
use App\Filament\Resources\VerseTranslations\Pages\ListVerseTranslations;
use App\Filament\Resources\VerseTranslations\Schemas\VerseTranslationForm;
use App\Filament\Resources\VerseTranslations\Tables\VerseTranslationsTable;
use App\Models\VerseTranslation;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VerseTranslationResource extends Resource
{
    protected static ?string $model = VerseTranslation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static UnitEnum|string|null $navigationGroup = "Kur'an İçeriği";

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'VerseTranslation';

    public static function form(Schema $schema): Schema
    {
        return VerseTranslationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VerseTranslationsTable::configure($table);
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
            'index' => ListVerseTranslations::route('/'),
            'create' => CreateVerseTranslation::route('/create'),
            'edit' => EditVerseTranslation::route('/{record}/edit'),
        ];
    }
}
