<?php

namespace App\Filament\Resources\Administration\Addresses;

use App\Filament\Resources\Administration\Addresses\Pages\CreateAddress;
use App\Filament\Resources\Administration\Addresses\Pages\EditAddress;
use App\Filament\Resources\Administration\Addresses\Pages\ListAddresses;
use App\Filament\Resources\Administration\Addresses\Pages\ViewAddress;
use App\Filament\Resources\Administration\Addresses\Schemas\AddressForm;
use App\Filament\Resources\Administration\Addresses\Tables\AddressesTable;
use App\Models\Address;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AddressResource extends Resource
{
    protected static ?string $model = Address::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $recordTitleAttribute = 'street';

    public static function getNavigationGroup(): ?string
    {
        return __('menu.groups.administration');
    }

    public static function getModelLabel(): string
    {
        return __('resources.addresses.singular');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.addresses.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return AddressForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AddressesTable::configure($table);
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
            'index' => ListAddresses::route('/'),
            'create' => CreateAddress::route('/create'),
            'view' => ViewAddress::route('/{record}'),
            'edit' => EditAddress::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
