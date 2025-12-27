<?php

namespace App\Filament\Resources\Shop\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProductOptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'options';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('resources.product_options.plural');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('resources.product_options.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('type')
                            ->label(__('resources.product_options.fields.type'))
                            ->required()
                            ->options([
                                'single' => __('resources.product_options.types.single'),
                                'multiple' => __('resources.product_options.types.multiple'),
                            ])
                            ->default('single')
                            ->native(false),

                        Toggle::make('required')
                            ->label(__('resources.product_options.fields.required'))
                            ->default(false),

                        KeyValue::make('meta')
                            ->label(__('resources.product_options.fields.meta'))
                            ->helperText(__('resources.product_options.helpers.meta'))
                            ->keyLabel(__('resources.key_value.key'))
                            ->valueLabel(__('resources.key_value.value'))
                            ->keyPlaceholder(__('resources.product_options.placeholders.meta_key'))
                            ->valuePlaceholder(__('resources.product_options.placeholders.meta_value'))
                            ->addActionLabel(__('resources.key_value.add'))
                            ->nullable()
                            ->columnSpanFull(),

                        Repeater::make('items')
                            ->label(__('resources.product_option_items.plural'))
                            ->relationship()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('resources.product_option_items.fields.name'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('price')
                                    ->label(__('resources.product_option_items.fields.price'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0),

                                KeyValue::make('meta')
                                    ->label(__('resources.product_option_items.fields.meta'))
                                    ->helperText(__('resources.product_option_items.helpers.meta'))
                                    ->keyLabel(__('resources.key_value.key'))
                                    ->valueLabel(__('resources.key_value.value'))
                                    ->keyPlaceholder(__('resources.product_option_items.placeholders.meta_key'))
                                    ->valuePlaceholder(__('resources.product_option_items.placeholders.meta_value'))
                                    ->addActionLabel(__('resources.key_value.add'))
                                    ->nullable()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel(__('resources.product_options.singular'))
            ->pluralModelLabel(__('resources.product_options.plural'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('resources.product_options.fields.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('resources.product_options.fields.type'))
                    ->badge(),

                TextColumn::make('required')
                    ->label(__('resources.product_options.fields.required'))
                    ->badge(),
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
