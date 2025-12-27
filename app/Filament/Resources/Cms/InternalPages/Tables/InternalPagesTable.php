<?php

namespace App\Filament\Resources\Cms\InternalPages\Tables;

use App\Enums\CmsStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class InternalPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('resources.cms_internal_pages.fields.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('section.name')
                    ->label(__('resources.cms_internal_pages.fields.section'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('resources.cms_internal_pages.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (?CmsStatus $state): string => $state?->label() ?? '-')
                    ->color(fn (?CmsStatus $state): string => $state?->color() ?? 'gray')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('resources.common.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->defaultSort('created_at', 'desc');
    }
}
