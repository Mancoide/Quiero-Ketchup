<?php

namespace App\Filament\Resources\CampuzanoReconciliations\Pages;

use App\Filament\Resources\CampuzanoReconciliations\CampuzanoReconciliationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateCampuzanoReconciliation extends CreateRecord
{
    protected static string $resource = CampuzanoReconciliationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['matching_mode'] = 'crossed_system_columns';
        $data['name'] = filled($data['name'] ?? null) ? $data['name'] : 'Conciliacion Sra Campuzano';

        return $data;
    }
}
