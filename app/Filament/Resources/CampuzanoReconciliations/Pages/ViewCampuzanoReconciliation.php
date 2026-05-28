<?php

namespace App\Filament\Resources\CampuzanoReconciliations\Pages;

use App\Enums\ReconciliationStatus;
use App\Filament\Resources\CampuzanoReconciliations\CampuzanoReconciliationResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCampuzanoReconciliation extends ViewRecord
{
    protected static string $resource = CampuzanoReconciliationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('actions.back'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),

            EditAction::make(),

            Action::make('process')
                ->label(__('resources.reconciliations.actions.process'))
                ->icon('heroicon-o-play')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->status !== ReconciliationStatus::PROCESSING)
                ->action(function (): void {
                    CampuzanoReconciliationResource::processRecord($this->record);
                    $this->record->refresh();
                }),

            Action::make('download')
                ->label(__('resources.reconciliations.actions.download'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn (): ?string => $this->record->result_file_url, shouldOpenInNewTab: true)
                ->visible(fn (): bool => filled($this->record->result_file)),

            DeleteAction::make(),
        ];
    }
}
