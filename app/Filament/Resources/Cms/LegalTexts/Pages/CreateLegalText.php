<?php

namespace App\Filament\Resources\Cms\LegalTexts\Pages;

use App\Filament\Resources\Cms\LegalTexts\LegalTextResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateLegalText extends CreateRecord
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
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
