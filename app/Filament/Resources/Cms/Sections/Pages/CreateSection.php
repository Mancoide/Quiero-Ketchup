<?php

namespace App\Filament\Resources\Cms\Sections\Pages;

use App\Filament\Resources\Cms\Sections\SectionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateSection extends CreateRecord
{
    protected static string $resource = SectionResource::class;

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
