<?php

namespace App\Filament\Resources\Administration\Restaurants;

use App\Filament\Resources\Administration\Restaurants\Pages\CreateRestaurant;
use App\Filament\Resources\Administration\Restaurants\Pages\EditRestaurant;
use App\Filament\Resources\Administration\Restaurants\Pages\ListRestaurants;
use App\Filament\Resources\Administration\Restaurants\Pages\ViewRestaurant;
use App\Filament\Resources\Administration\Restaurants\Schemas\RestaurantForm;
use App\Filament\Resources\Administration\Restaurants\Tables\RestaurantsTable;
use App\Models\Restaurant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('menu.groups.administration');
    }

    public static function getModelLabel(): string
    {
        return __('resources.restaurants.singular');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.restaurants.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return RestaurantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RestaurantsTable::configure($table);
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
            'index' => ListRestaurants::route('/'),
            'create' => CreateRestaurant::route('/create'),
            'view' => ViewRestaurant::route('/{record}'),
            'edit' => EditRestaurant::route('/{record}/edit'),
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
