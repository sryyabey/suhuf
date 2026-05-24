<?php

namespace App\Filament\Resources\UserQuranPageBookmarks;

use App\Filament\Resources\UserQuranPageBookmarks\Pages\CreateUserQuranPageBookmark;
use App\Filament\Resources\UserQuranPageBookmarks\Pages\EditUserQuranPageBookmark;
use App\Filament\Resources\UserQuranPageBookmarks\Pages\ListUserQuranPageBookmarks;
use App\Filament\Resources\UserQuranPageBookmarks\Schemas\UserQuranPageBookmarkForm;
use App\Filament\Resources\UserQuranPageBookmarks\Tables\UserQuranPageBookmarksTable;
use App\Models\UserQuranPageBookmark;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserQuranPageBookmarkResource extends Resource
{
    protected static ?string $model = UserQuranPageBookmark::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmarkSquare;

    protected static UnitEnum|string|null $navigationGroup = 'Kullanıcı Yönetimi';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'UserQuranPageBookmark';

    public static function form(Schema $schema): Schema
    {
        return UserQuranPageBookmarkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserQuranPageBookmarksTable::configure($table);
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
            'index' => ListUserQuranPageBookmarks::route('/'),
            'create' => CreateUserQuranPageBookmark::route('/create'),
            'edit' => EditUserQuranPageBookmark::route('/{record}/edit'),
        ];
    }
}
