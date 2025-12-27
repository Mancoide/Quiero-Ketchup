<?php

namespace App\Filament\Resources\Shop\Promotions;

use App\Filament\Resources\Shop\Promotions\Pages\CreatePromotion;
use App\Filament\Resources\Shop\Promotions\Pages\EditPromotion;
use App\Filament\Resources\Shop\Promotions\Pages\ListPromotions;
use App\Filament\Resources\Shop\Promotions\Schemas\PromotionForm;
use App\Filament\Resources\Shop\Promotions\Tables\PromotionsTable;
use App\Models\Promotion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'code';

    public static function getNavigationGroup(): ?string
    {
        return __('menu.groups.shop');
    }

    public static function getModelLabel(): string
    {
        return __('resources.promotions.singular');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.promotions.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return PromotionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PromotionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPromotions::route('/'),
            'create' => CreatePromotion::route('/create'),
            'edit' => EditPromotion::route('/{record}/edit'),
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
