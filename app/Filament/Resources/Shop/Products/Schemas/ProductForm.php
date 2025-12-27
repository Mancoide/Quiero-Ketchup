<?php

namespace App\Filament\Resources\Shop\Products\Schemas;

use App\Models\Subcategory;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductForm
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
                            Section::make(__('resources.products.sections.media'))
                                ->schema([
                                    \Filament\Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                                        ->collection('images')
                                        ->label(__('resources.products.fields.images'))
                                        ->image()
                                        ->imageEditor()
                                        ->maxSize(4096)
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                        ->disk('public')
                                        ->visibility('public')
                                        ->downloadable()
                                        ->openable()
                                        ->multiple(),
                                ])
                                ->compact()
                                ->columnSpanFull(),

                            Section::make(__('resources.products.sections.general'))
                                ->schema([
                                    TextInput::make('name')
                                        ->label(__('resources.products.fields.name'))
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
                                        ->label(__('resources.products.fields.slug'))
                                        ->required()
                                        ->maxLength(255),

                                    Textarea::make('description')
                                        ->label(__('resources.products.fields.description'))
                                        ->rows(3)
                                        ->columnSpanFull(),

                                    KeyValue::make('meta')
                                        ->label(__('resources.products.fields.meta'))
                                        ->helperText(__('resources.products.helpers.meta'))
                                        ->keyLabel(__('resources.key_value.key'))
                                        ->valueLabel(__('resources.key_value.value'))
                                        ->keyPlaceholder(__('resources.products.placeholders.meta_key'))
                                        ->valuePlaceholder(__('resources.products.placeholders.meta_value'))
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
                            Section::make(__('resources.products.sections.pricing'))
                                ->schema([
                                    TextInput::make('price')
                                        ->label(__('resources.products.fields.price'))
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->prefix('$'),

                                    TextInput::make('currency')
                                        ->label(__('resources.products.fields.currency'))
                                        ->required()
                                        ->maxLength(3)
                                        ->default('PYG'),

                                    Toggle::make('available')
                                        ->label(__('resources.products.fields.available'))
                                        ->default(true),
                                ])
                                ->columnSpanFull(),

                            Section::make(__('resources.products.sections.classification'))
                                ->schema([
                                    Select::make('category_id')
                                        ->label(__('resources.products.fields.category'))
                                        ->relationship('category', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->nullable(),

                                    Select::make('subcategory_id')
                                        ->label(__('resources.products.fields.subcategory'))
                                        ->options(function (callable $get): array {
                                            $categoryId = $get('category_id');

                                            if (blank($categoryId)) {
                                                return [];
                                            }

                                            return Subcategory::query()
                                                ->where('category_id', $categoryId)
                                                ->orderBy('name')
                                                ->pluck('name', 'id')
                                                ->all();
                                        })
                                        ->searchable()
                                        ->nullable()
                                        ->disabled(fn (callable $get): bool => blank($get('category_id'))),

                                    Select::make('restaurants')
                                        ->label(__('resources.products.fields.restaurants'))
                                        ->relationship('restaurants', 'name', function (Builder $query): Builder {
                                            $table = $query->getModel()->getTable();

                                            return $query->select([
                                                "$table.id",
                                                "$table.name",
                                            ]);
                                        })
                                        ->multiple()
                                        ->preload()
                                        ->searchable()
                                ])
                                ->columnSpanFull(),
                        ])
                        ->columnSpan(1),
                ])->columnSpanFull(),
            ]);
    }
}
