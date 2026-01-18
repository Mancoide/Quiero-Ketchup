<?php

namespace App\Filament\Resources\Authentication\UserResource\Pages;

use App\Filament\Resources\Authentication\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

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
            Actions\DeleteAction::make()
                ->hidden(fn (): bool => $this->record->getKey() === Auth::id()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Usuario actualizado correctamente';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $data;
    }
}
