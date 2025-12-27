<?php

namespace App\Filament\Resources\Administration\Addresses\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class AddressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->tabs([
                        Tab::make(__('resources.addresses.tabs.information'))
                            ->schema([
                                Grid::make([
                                    'default' => 1,
                                    'md' => 2,
                                ])
                                    ->schema([
                                        Select::make('user_id')
                                            ->label(__('resources.addresses.fields.user'))
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->nullable()
                                            ->columnSpanFull(),

                                        TextInput::make('street')
                                            ->label(__('resources.addresses.fields.street'))
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        TextInput::make('city')
                                            ->label(__('resources.addresses.fields.city'))
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('state')
                                            ->label(__('resources.addresses.fields.state'))
                                            ->maxLength(255)
                                            ->nullable(),

                                        TextInput::make('postal_code')
                                            ->label(__('resources.addresses.fields.postal_code'))
                                            ->maxLength(255)
                                            ->nullable(),

                                        TextInput::make('country')
                                            ->label(__('resources.addresses.fields.country'))
                                            ->required()
                                            ->maxLength(2)
                                            ->default('PY'),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tab::make(__('resources.addresses.tabs.location'))
                            ->schema([
                                KeyValue::make('location')
                                    ->label(__('resources.addresses.fields.location'))
                                    ->nullable()
                                    ->columnSpanFull(),
                            ]),

                        Tab::make(__('resources.addresses.tabs.advanced'))
                            ->schema([
                                KeyValue::make('meta')
                                    ->label(__('resources.addresses.fields.meta'))
                                    ->nullable()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
