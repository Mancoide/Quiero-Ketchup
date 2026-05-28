<?php

namespace App\Filament\Resources\Reconciliations\Tables;

use App\Enums\ReconciliationStatus;
use App\Filament\Resources\Reconciliations\ReconciliationResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReconciliationsTable
{
    public static function configure(Table $table, string $resourceClass = ReconciliationResource::class): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('resources.reconciliations.fields.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('resources.reconciliations.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (ReconciliationStatus | string | null $state): string => $state instanceof ReconciliationStatus ? $state->label() : (string) $state)
                    ->color(fn (ReconciliationStatus | string | null $state): string => $state instanceof ReconciliationStatus ? $state->color() : 'gray'),

                TextColumn::make('total_bank_records')
                    ->label(__('resources.reconciliations.fields.total_bank_records'))
                    ->numeric(),

                TextColumn::make('total_company_records')
                    ->label(__('resources.reconciliations.fields.total_company_records'))
                    ->numeric(),

                TextColumn::make('matched_records')
                    ->label(__('resources.reconciliations.fields.matched_records'))
                    ->numeric(),

                TextColumn::make('possible_matches')
                    ->label(__('resources.reconciliations.fields.possible_matches'))
                    ->numeric(),

                TextColumn::make('processed_at')
                    ->label(__('resources.reconciliations.fields.processed_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('process')
                    ->label(__('resources.reconciliations.actions.process'))
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => $record->status !== ReconciliationStatus::PROCESSING)
                    ->action(fn ($record) => $resourceClass::processRecord($record)),
                Action::make('download')
                    ->label(__('resources.reconciliations.actions.download'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn ($record): ?string => $record->result_file_url, shouldOpenInNewTab: true)
                    ->visible(fn ($record): bool => filled($record->result_file)),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
