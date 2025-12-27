<?php

namespace App\Filament\Resources\Cms\InternalPages;

use App\Filament\Resources\Cms\InternalPages\Pages\CreateInternalPage;
use App\Filament\Resources\Cms\InternalPages\Pages\EditInternalPage;
use App\Filament\Resources\Cms\InternalPages\Pages\ListInternalPages;
use App\Filament\Resources\Cms\InternalPages\Schemas\InternalPageForm;
use App\Filament\Resources\Cms\InternalPages\Tables\InternalPagesTable;
use App\Models\InternalPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InternalPageResource extends Resource
{
    protected static ?string $model = InternalPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationGroup(): ?string
    {
        return __('menu.groups.cms');
    }

    public static function getModelLabel(): string
    {
        return __('resources.cms_internal_pages.singular');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.cms_internal_pages.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return InternalPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InternalPagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInternalPages::route('/'),
            'create' => CreateInternalPage::route('/create'),
            'edit' => EditInternalPage::route('/{record}/edit'),
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
