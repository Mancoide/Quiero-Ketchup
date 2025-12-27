<?php

namespace App\Filament\Resources\Authentication\RoleResource\Pages;

use App\Filament\Resources\Authentication\RoleResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Spatie\Permission\Models\Role;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label(__('actions.back'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
            Actions\EditAction::make()
                ->color('warning'),

            Actions\Action::make('duplicate')
                ->label('Duplicar')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\TextInput::make('name')
                        ->label('Nombre del Nuevo Rol')
                        ->required()
                        ->unique('roles', 'name')
                        ->placeholder('Ej: moderador_copia'),
                ])
                ->action(function (array $data): void {
                    $newRole = Role::create([
                        'name' => $data['name'],
                        'guard_name' => $this->record->guard_name,
                    ]);

                    $newRole->syncPermissions($this->record->permissions);

                    Notification::make()
                        ->success()
                        ->title('Rol duplicado')
                        ->body("Nuevo rol '{$data['name']}' creado")
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->hidden(fn (): bool => $this->record->name === 'super_admin'),
        ];
    }
}
