<?php

namespace App\Filament\Resources\Administration\Restaurants\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class RestaurantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->tabs([
                        Tab::make(__('resources.restaurants.sections.general'))
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('images')
                                    ->collection('images')
                                    ->label(__('resources.restaurants.fields.images'))
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(4096)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->disk('public')
                                    ->visibility('public')
                                    ->downloadable()
                                    ->openable()
                                    ->columnSpanFull(),

                                Grid::make([
                                    'default' => 1,
                                    'md' => 2,
                                ])
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('resources.restaurants.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, callable $set, callable $get): void {
                                                if (filled($get('slug'))) {
                                                    return;
                                                }

                                                $set('slug', Str::slug((string) $state));
                                            }),

                                        TextInput::make('slug')
                                            ->label(__('resources.restaurants.fields.slug'))
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true),

                                        Select::make('status')
                                            ->label(__('resources.restaurants.fields.status'))
                                            ->options([
                                                'active' => __('resources.restaurants.statuses.active'),
                                                'inactive' => __('resources.restaurants.statuses.inactive'),
                                                'suspended' => __('resources.restaurants.statuses.suspended'),
                                            ])
                                            ->default('active')
                                            ->required(),

                                        Textarea::make('description')
                                            ->label(__('resources.restaurants.fields.description'))
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tab::make(__('resources.restaurants.sections.contact'))
                            ->schema([
                                Grid::make([
                                    'default' => 1,
                                    'md' => 2,
                                ])
                                    ->schema([
                                        TextInput::make('phone')
                                            ->label(__('resources.restaurants.fields.phone'))
                                            ->tel()
                                            ->maxLength(255)
                                            ->nullable(),

                                        TextInput::make('email')
                                            ->label(__('resources.restaurants.fields.email'))
                                            ->email()
                                            ->maxLength(255)
                                            ->nullable(),

                                        Select::make('address_id')
                                            ->label(__('resources.restaurants.fields.address'))
                                            ->relationship('address', 'street')
                                            ->searchable()
                                            ->preload()
                                            ->nullable()
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tab::make(__('resources.restaurants.sections.advanced'))
                            ->schema([
                                KeyValue::make('settings')
                                    ->label(__('resources.restaurants.fields.settings'))
                                    ->nullable()
                                    ->columnSpanFull(),

                                KeyValue::make('meta')
                                    ->label(__('resources.restaurants.fields.meta'))
                                    ->nullable()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
