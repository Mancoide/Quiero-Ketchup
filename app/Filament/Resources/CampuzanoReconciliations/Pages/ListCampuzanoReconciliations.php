<?php

namespace App\Filament\Resources\CampuzanoReconciliations\Pages;

use App\Filament\Resources\CampuzanoReconciliations\CampuzanoReconciliationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCampuzanoReconciliations extends ListRecords
{
    protected static string $resource = CampuzanoReconciliationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
