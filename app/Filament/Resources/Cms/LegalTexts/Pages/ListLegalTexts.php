<?php

namespace App\Filament\Resources\Cms\LegalTexts\Pages;

use App\Filament\Resources\Cms\LegalTexts\LegalTextResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLegalTexts extends ListRecords
{
    protected static string $resource = LegalTextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
