<?php

namespace App\Filament\Resources\Reconciliations\Pages;

use App\Filament\Resources\Reconciliations\ReconciliationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateReconciliation extends CreateRecord
{
    protected static string $resource = ReconciliationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['matching_mode'] = $data['matching_mode'] ?? 'standard';

        return $data;
    }
}
