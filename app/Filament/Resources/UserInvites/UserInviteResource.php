<?php

namespace App\Filament\Resources\UserInvites;

use App\Filament\Resources\UserInvites\Pages\CreateUserInvite;
use App\Filament\Resources\UserInvites\Pages\EditUserInvite;
use App\Filament\Resources\UserInvites\Pages\ListUserInvites;
use App\Filament\Resources\UserInvites\Schemas\UserInviteForm;
use App\Filament\Resources\UserInvites\Tables\UserInvitesTable;
use App\Models\UserInvite;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UserInviteResource extends Resource
{
    protected static ?string $model = UserInvite::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static UnitEnum|string|null $navigationGroup = 'Kullanıcı Yönetimi';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return UserInviteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserInvitesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserInvites::route('/'),
            'create' => CreateUserInvite::route('/create'),
            'edit' => EditUserInvite::route('/{record}/edit'),
        ];
    }
}
