<?php

namespace App\Filament\Resources\Reconciliations\Pages;

use App\Enums\ReconciliationStatus;
use App\Filament\Resources\Reconciliations\ReconciliationResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReconciliation extends EditRecord
{
    protected static string $resource = ReconciliationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('actions.back'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),

            Action::make('process')
                ->label(__('resources.reconciliations.actions.process'))
                ->icon('heroicon-o-play')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->status !== ReconciliationStatus::PROCESSING)
                ->action(function (): void {
                    ReconciliationResource::processRecord($this->record);
                    $this->record->refresh();
                    $this->fillForm();
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
