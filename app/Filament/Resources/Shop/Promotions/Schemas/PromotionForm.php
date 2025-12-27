<?php

namespace App\Filament\Resources\Shop\Promotions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class PromotionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('code')
                            ->label(__('resources.promotions.fields.code'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Select::make('type')
                            ->label(__('resources.promotions.fields.type'))
                            ->required()
                            ->options([
                                'percentage' => __('resources.promotions.types.percentage'),
                                'fixed' => __('resources.promotions.types.fixed'),
                                'free_delivery' => __('resources.promotions.types.free_delivery'),
                            ])
                            ->default('percentage')
                            ->native(false),

                        TextInput::make('value')
                            ->label(__('resources.promotions.fields.value'))
                            ->required()
                            ->numeric()
                            ->minValue(0),

                        DateTimePicker::make('starts_at')
                            ->label(__('resources.promotions.fields.starts_at'))
                            ->nullable(),

                        DateTimePicker::make('ends_at')
                            ->label(__('resources.promotions.fields.ends_at'))
                            ->nullable(),

                        Select::make('products')
                            ->label(__('resources.promotions.fields.products'))
                            ->relationship(
                                name: 'products',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query
                                    ->select(['products.id', 'products.name'])
                                    ->orderBy('products.name'),
                            )
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),

                        KeyValue::make('meta')
                            ->label(__('resources.promotions.fields.meta'))
                            ->helperText(__('resources.promotions.helpers.meta'))
                            ->keyLabel(__('resources.key_value.key'))
                            ->valueLabel(__('resources.key_value.value'))
                            ->keyPlaceholder(__('resources.promotions.placeholders.meta_key'))
                            ->valuePlaceholder(__('resources.promotions.placeholders.meta_value'))
                            ->addActionLabel(__('resources.key_value.add'))
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
