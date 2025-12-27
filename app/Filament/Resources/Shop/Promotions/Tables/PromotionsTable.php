<?php

namespace App\Filament\Resources\Shop\Promotions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PromotionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('resources.promotions.fields.code'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('resources.promotions.fields.type'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'percentage' => __('resources.promotions.types.percentage'),
                        'fixed' => __('resources.promotions.types.fixed'),
                        'free_delivery' => __('resources.promotions.types.free_delivery'),
                        default => (string) $state,
                    }),

                TextColumn::make('value')
                    ->label(__('resources.promotions.fields.value'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('products_count')
                    ->label(__('resources.promotions.fields.products_count'))
                    ->counts('products')
                    ->sortable(),

                TextColumn::make('starts_at')
                    ->label(__('resources.promotions.fields.starts_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ends_at')
                    ->label(__('resources.promotions.fields.ends_at'))
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
            ]);
    }
}
