<?php

namespace App\Filament\Resources\Cms\Sections;

use App\Filament\Resources\Cms\Sections\Pages\CreateSection;
use App\Filament\Resources\Cms\Sections\Pages\EditSection;
use App\Filament\Resources\Cms\Sections\Pages\ListSections;
use App\Filament\Resources\Cms\Sections\Schemas\SectionForm;
use App\Filament\Resources\Cms\Sections\Tables\SectionsTable;
use App\Models\Section as CmsSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SectionResource extends Resource
{
    protected static ?string $model = CmsSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('menu.groups.cms');
    }

    public static function getModelLabel(): string
    {
        return __('resources.cms_sections.singular');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.cms_sections.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return SectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SectionsTable::configure($table);
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
            'index' => ListSections::route('/'),
            'create' => CreateSection::route('/create'),
            'edit' => EditSection::route('/{record}/edit'),
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
