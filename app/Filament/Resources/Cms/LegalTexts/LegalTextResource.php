<?php

namespace App\Filament\Resources\Cms\LegalTexts;

use App\Filament\Resources\Cms\LegalTexts\Pages\CreateLegalText;
use App\Filament\Resources\Cms\LegalTexts\Pages\EditLegalText;
use App\Filament\Resources\Cms\LegalTexts\Pages\ListLegalTexts;
use App\Filament\Resources\Cms\LegalTexts\Schemas\LegalTextForm;
use App\Filament\Resources\Cms\LegalTexts\Tables\LegalTextsTable;
use App\Models\LegalText;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LegalTextResource extends Resource
{
    protected static ?string $model = LegalText::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?string $recordTitleAttribute = 'type';

    public static function getNavigationGroup(): ?string
    {
        return __('menu.groups.cms');
    }

    public static function getModelLabel(): string
    {
        return __('resources.legal_texts.singular');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.legal_texts.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return LegalTextForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LegalTextsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLegalTexts::route('/'),
            'create' => CreateLegalText::route('/create'),
            'edit' => EditLegalText::route('/{record}/edit'),
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
