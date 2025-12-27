<?php

namespace App\Filament\Resources\Orders\Tables;

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

                TextColumn::make('status')
                    ->label(__('resources.orders.fields.status'))
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'preparing' => 'info',
                        'ready' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => __('resources.orders.statuses.pending'),
                        'confirmed' => __('resources.orders.statuses.confirmed'),
                        'preparing' => __('resources.orders.statuses.preparing'),
                        'ready' => __('resources.orders.statuses.ready'),
                        'completed' => __('resources.orders.statuses.completed'),
                        'cancelled' => __('resources.orders.statuses.cancelled'),
                        default => (string) $state,
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
                SelectFilter::make('status')
                    ->label(__('resources.orders.fields.status'))
                    ->options([
                        'pending' => __('resources.orders.statuses.pending'),
                        'confirmed' => __('resources.orders.statuses.confirmed'),
                        'preparing' => __('resources.orders.statuses.preparing'),
                        'ready' => __('resources.orders.statuses.ready'),
                        'completed' => __('resources.orders.statuses.completed'),
                        'cancelled' => __('resources.orders.statuses.cancelled'),
                    ])
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
            ->defaultSort('created_at', 'desc');
    }
}
