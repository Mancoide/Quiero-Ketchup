<?php

namespace App\Filament\Resources\Cms\Banners\Tables;

use App\Enums\CmsStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label(__('resources.cms_banners.fields.image'))
                    ->square()
                    ->getStateUsing(fn ($record) => $record->getImageUrl()),

                TextColumn::make('section.name')
                    ->label(__('resources.cms_banners.fields.section'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('button_text')
                    ->label(__('resources.cms_banners.fields.button_text'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextColumn::make('status')
                    ->label(__('resources.cms_banners.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (?CmsStatus $state): string => $state?->label() ?? '-')
                    ->color(fn (?CmsStatus $state): string => $state?->color() ?? 'gray')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label(__('resources.cms_banners.fields.sort_order'))
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }
}
