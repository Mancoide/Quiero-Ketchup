<?php

namespace App\Filament\Resources\Authentication\RoleResource\Pages;

use App\Filament\Resources\Authentication\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->label('Nuevo Rol'),
        ];
    }

    public function getTitle(): string
    {
        return 'Roles y Permisos';
    }
}
