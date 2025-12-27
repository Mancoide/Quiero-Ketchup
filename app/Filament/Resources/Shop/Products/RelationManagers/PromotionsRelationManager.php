<?php

namespace App\Filament\Resources\Shop\Products\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
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

class PromotionsRelationManager extends RelationManager
{
    protected static string $relationship = 'promotions';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('resources.promotions.plural');
    }

    public function form(Schema $schema): Schema
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

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel(__('resources.promotions.singular'))
            ->pluralModelLabel(__('resources.promotions.plural'))
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
            ->headerActions([
                AttachAction::make(),
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DetachAction::make(),
                DeleteAction::make(),
            ]);
    }
}
