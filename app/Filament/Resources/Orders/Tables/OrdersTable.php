<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderFulfillmentType;
use App\Enums\OrderStatus;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('resources.orders.fields.id'))
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label(__('resources.orders.fields.user'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('restaurant.name')
                    ->label(__('resources.orders.fields.restaurant'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('fulfillment_type')
                    ->label(__('resources.orders.fields.fulfillment_type'))
                    ->badge()
                    ->color(function (OrderFulfillmentType|string|null $state): string {
                        return OrderFulfillmentType::tryFromMixed($state)?->color() ?? 'gray';
                    })
                    ->formatStateUsing(function (OrderFulfillmentType|string|null $state): string {
                        return OrderFulfillmentType::tryFromMixed($state)?->label() ?? (string) $state;
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('resources.orders.fields.status'))
                    ->badge()
                    ->color(function (OrderStatus|string|null $state): string {
                        return OrderStatus::tryFromMixed($state)?->color() ?? 'gray';
                    })
                    ->formatStateUsing(function (OrderStatus|string|null $state): string {
                        return OrderStatus::tryFromMixed($state)?->label() ?? (string) $state;
                    })
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label(__('resources.orders.fields.total_amount'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('currency')
                    ->label(__('resources.orders.fields.currency'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('resources.orders.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('fulfillment_type')
                    ->label(__('resources.orders.fields.fulfillment_type'))
                    ->options(OrderFulfillmentType::options())
                    ->native(false),

                SelectFilter::make('status')
                    ->label(__('resources.orders.fields.status'))
                    ->options(OrderStatus::options())
                    ->native(false),

                SelectFilter::make('restaurant_id')
                    ->label(__('resources.orders.fields.restaurant'))
                    ->relationship('restaurant', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('user_id')
                    ->label(__('resources.orders.fields.user'))
                    ->relationship('user', 'name')
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->paginationPageOptions([25, 50, 100])
            ->defaultPaginationPageOption(100)
            ->defaultSort('created_at', 'desc');
    }
}
