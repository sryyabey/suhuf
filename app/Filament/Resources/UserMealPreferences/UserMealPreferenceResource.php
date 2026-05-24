<?php

namespace App\Filament\Resources\UserMealPreferences;

use App\Filament\Resources\UserMealPreferences\Pages\CreateUserMealPreference;
use App\Filament\Resources\UserMealPreferences\Pages\EditUserMealPreference;
use App\Filament\Resources\UserMealPreferences\Pages\ListUserMealPreferences;
use App\Filament\Resources\UserMealPreferences\Schemas\UserMealPreferenceForm;
use App\Filament\Resources\UserMealPreferences\Tables\UserMealPreferencesTable;
use App\Models\UserMealPreference;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserMealPreferenceResource extends Resource
{
    protected static ?string $model = UserMealPreference::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static UnitEnum|string|null $navigationGroup = 'Kullanıcı Yönetimi';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'UserMealPreference';

    public static function form(Schema $schema): Schema
    {
        return UserMealPreferenceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserMealPreferencesTable::configure($table);
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
            'index' => ListUserMealPreferences::route('/'),
            'create' => CreateUserMealPreference::route('/create'),
            'edit' => EditUserMealPreference::route('/{record}/edit'),
        ];
    }
}
