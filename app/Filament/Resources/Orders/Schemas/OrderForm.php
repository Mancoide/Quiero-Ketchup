<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 3,
                ])
                    ->schema([
                        Grid::make()
                            ->schema([
                                Section::make(__('resources.orders.sections.general'))
                                    ->schema([
                                        Select::make('user_id')
                                            ->label(__('resources.orders.fields.user'))
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->disabled(fn (string $context): bool => $context !== 'create'),

                                        Select::make('restaurant_id')
                                            ->label(__('resources.orders.fields.restaurant'))
                                            ->relationship('restaurant', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->disabled(fn (string $context): bool => $context !== 'create'),

                                        Select::make('status')
                                            ->label(__('resources.orders.fields.status'))
                                            ->options([
                                                'pending' => __('resources.orders.statuses.pending'),
                                                'confirmed' => __('resources.orders.statuses.confirmed'),
                                                'preparing' => __('resources.orders.statuses.preparing'),
                                                'ready' => __('resources.orders.statuses.ready'),
                                                'completed' => __('resources.orders.statuses.completed'),
                                                'cancelled' => __('resources.orders.statuses.cancelled'),
                                            ])
                                            ->native(false)
                                            ->required(),

                                        TextInput::make('total_amount')
                                            ->label(__('resources.orders.fields.total_amount'))
                                            ->numeric()
                                            ->required()
                                            ->disabled(fn (string $context): bool => $context !== 'create'),

                                        TextInput::make('currency')
                                            ->label(__('resources.orders.fields.currency'))
                                            ->maxLength(3)
                                            ->default('PYG')
                                            ->required()
                                            ->disabled(fn (string $context): bool => $context !== 'create'),

                                        KeyValue::make('metadata')
                                            ->label(__('resources.orders.fields.metadata'))
                                            ->helperText(__('resources.orders.helpers.metadata'))
                                            ->keyLabel(__('resources.key_value.key'))
                                            ->valueLabel(__('resources.key_value.value'))
                                            ->keyPlaceholder(__('resources.orders.placeholders.metadata_key'))
                                            ->valuePlaceholder(__('resources.orders.placeholders.metadata_value'))
                                            ->addActionLabel(__('resources.key_value.add'))
                                            ->nullable()
                                            ->columnSpanFull(),
                                    ])
                                    ->columns([
                                        'default' => 1,
                                        'md' => 2,
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(2),

                        Grid::make()
                            ->schema([
                                Section::make(__('resources.orders.sections.timestamps'))
                                    ->schema([
                                        Placeholder::make('created_at')
                                            ->label(__('resources.orders.fields.created_at'))
                                            ->content(fn (?Model $record): string => $record?->created_at?->format('Y-m-d H:i') ?? '-'),

                                        Placeholder::make('updated_at')
                                            ->label(__('resources.orders.fields.updated_at'))
                                            ->content(fn (?Model $record): string => $record?->updated_at?->format('Y-m-d H:i') ?? '-'),
                                    ])
                                    ->visible(fn (string $context): bool => $context !== 'create')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
