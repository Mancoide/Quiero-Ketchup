<?php

namespace App\Filament\Resources\Administration\Restaurants\Pages;

use App\Filament\Resources\Administration\Restaurants\RestaurantResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListRestaurants extends ListRecords
{
    protected static string $resource = RestaurantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label(__('filament-actions::create.single.label', ['label' => static::getResource()::getModelLabel()]))
                ->url($this->getResource()::getUrl('create')),
        ];
    }
}
