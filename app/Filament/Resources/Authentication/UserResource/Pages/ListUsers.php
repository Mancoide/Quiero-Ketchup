<?php

namespace App\Filament\Resources\Authentication\UserResource\Pages;

use App\Filament\Resources\Authentication\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->label('Nuevo Usuario'),
        ];
    }

    public function getTitle(): string
    {
        return 'Usuarios';
    }
}
