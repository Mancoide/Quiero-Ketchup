<?php

namespace App\Filament\Resources\Administration\Restaurants\Pages;

use App\Filament\Resources\Administration\Restaurants\RestaurantResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewRestaurant extends ViewRecord
{
    protected static string $resource = RestaurantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('actions.back'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),

            Action::make('edit')
                ->label(__('filament-actions::edit.single.label', ['label' => static::getResource()::getModelLabel()]))
                ->url(fn (): string => $this->getResource()::getUrl('edit', ['record' => $this->getRecord()])),
        ];
    }
}
