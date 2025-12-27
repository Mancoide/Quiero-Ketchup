<?php

namespace App\Filament\Resources\Cms\InternalPages\Pages;

use App\Filament\Resources\Cms\InternalPages\InternalPageResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateInternalPage extends CreateRecord
{
    protected static string $resource = InternalPageResource::class;

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
