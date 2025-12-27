<?php

namespace App\Filament\Resources\Authentication\UserResource\Pages;

use App\Filament\Resources\Authentication\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewUser extends ViewRecord
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
            Actions\EditAction::make()
                ->color('warning'),
            Actions\DeleteAction::make()
                ->hidden(fn (): bool => $this->record->getKey() === Auth::id()),
        ];
    }
}
