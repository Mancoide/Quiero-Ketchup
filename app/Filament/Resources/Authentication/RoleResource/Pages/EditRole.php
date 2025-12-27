<?php

namespace App\Filament\Resources\Authentication\RoleResource\Pages;

use App\Filament\Resources\Authentication\RoleResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Role;

class EditRole extends EditRecord
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
            Actions\ViewAction::make()
                ->color('info'),

            Actions\Action::make('duplicate')
                ->label('Duplicar Rol')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Duplicar Rol')
                ->modalDescription('Se creará una copia de este rol con todos sus permisos')
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
                ->hidden(fn (): bool => $this->record->name === 'super_admin')
                ->before(function () {
                    if ($this->record->name === 'super_admin') {
                        Notification::make()
                            ->danger()
                            ->title('Acción no permitida')
                            ->body('No se puede eliminar el rol super_admin')
                            ->persistent()
                            ->send();

                        return false;
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Rol actualizado correctamente';
    }
}
