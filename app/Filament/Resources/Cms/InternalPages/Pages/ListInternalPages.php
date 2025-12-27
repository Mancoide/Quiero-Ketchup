<?php

namespace App\Filament\Resources\Cms\InternalPages\Pages;

use App\Filament\Resources\Cms\InternalPages\InternalPageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInternalPages extends ListRecords
{
    protected static string $resource = InternalPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
