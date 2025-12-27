<?php

namespace App\Filament\Resources\Cms\LegalTexts\Pages;

use App\Filament\Resources\Cms\LegalTexts\LegalTextResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditLegalText extends EditRecord
{
    protected static string $resource = LegalTextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('actions.back'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),

            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
