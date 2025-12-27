<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('resources.order_items.plural');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('product_id')
                            ->label(__('resources.order_items.fields.product'))
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('quantity')
                            ->label(__('resources.order_items.fields.quantity'))
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required()
                            ->live(),

                        TextInput::make('unit_price')
                            ->label(__('resources.order_items.fields.unit_price'))
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $get, callable $set): void {
                                $quantity = (int) ($get('quantity') ?? 1);
                                $unitPrice = (float) ($get('unit_price') ?? 0);
                                $set('total_price', $quantity * $unitPrice);
                            }),

                        TextInput::make('total_price')
                            ->label(__('resources.order_items.fields.total_price'))
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        KeyValue::make('meta')
                            ->label(__('resources.order_items.fields.meta'))
                            ->helperText(__('resources.order_items.helpers.meta'))
                            ->keyLabel(__('resources.key_value.key'))
                            ->valueLabel(__('resources.key_value.value'))
                            ->keyPlaceholder(__('resources.order_items.placeholders.meta_key'))
                            ->valuePlaceholder(__('resources.order_items.placeholders.meta_value'))
                            ->addActionLabel(__('resources.key_value.add'))
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel(__('resources.order_items.singular'))
            ->pluralModelLabel(__('resources.order_items.plural'))
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('resources.order_items.fields.product'))
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label(__('resources.order_items.fields.quantity'))
                    ->numeric(),

                TextColumn::make('unit_price')
                    ->label(__('resources.order_items.fields.unit_price'))
                    ->numeric(),

                TextColumn::make('total_price')
                    ->label(__('resources.order_items.fields.total_price'))
                    ->numeric(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ]);
    }
}
