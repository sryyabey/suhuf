<?php

namespace App\Filament\Resources\QuranWords;

use App\Filament\Resources\QuranWords\Pages\CreateQuranWord;
use App\Filament\Resources\QuranWords\Pages\EditQuranWord;
use App\Filament\Resources\QuranWords\Pages\ListQuranWords;
use App\Filament\Resources\QuranWords\Schemas\QuranWordForm;
use App\Filament\Resources\QuranWords\Tables\QuranWordsTable;
use App\Models\QuranWord;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuranWordResource extends Resource
{
    protected static ?string $model = QuranWord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static UnitEnum|string|null $navigationGroup = "Kur'an İçeriği";

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return QuranWordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuranWordsTable::configure($table);
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
            'index' => ListQuranWords::route('/'),
            'create' => CreateQuranWord::route('/create'),
            'edit' => EditQuranWord::route('/{record}/edit'),
        ];
    }
}
