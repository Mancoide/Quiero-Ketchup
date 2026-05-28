<?php

namespace App\Filament\Resources\CampuzanoReconciliations;

use App\Filament\Resources\CampuzanoReconciliations\Pages\CreateCampuzanoReconciliation;
use App\Filament\Resources\CampuzanoReconciliations\Pages\EditCampuzanoReconciliation;
use App\Filament\Resources\CampuzanoReconciliations\Pages\ListCampuzanoReconciliations;
use App\Filament\Resources\CampuzanoReconciliations\Pages\ViewCampuzanoReconciliation;
use App\Filament\Resources\Reconciliations\Schemas\ReconciliationForm;
use App\Filament\Resources\Reconciliations\Tables\ReconciliationsTable;
use App\Models\Reconciliation;
use App\Services\ReconciliationProcessorService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CampuzanoReconciliationResource extends Resource
{
    protected static ?string $model = Reconciliation::class;

    protected static ?string $slug = 'conciliacion-sra-campuzano';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('menu.groups.accounting');
    }

    public static function getModelLabel(): string
    {
        return 'Conciliacion Sra Campuzano';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Conciliacion Sra Campuzano';
    }

    public static function getNavigationLabel(): string
    {
        return 'Conciliacion Sra Campuzano';
    }

    public static function form(Schema $schema): Schema
    {
        return ReconciliationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReconciliationsTable::configure($table, static::class);
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->canAccessCampuzanoReconciliations() ?? false;
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function canCreate(): bool
    {
        return static::canAccess();
    }

    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = Auth::user();

        if (! $user?->canAccessCampuzanoReconciliations()) {
            return false;
        }

        return $user->isSuperAdmin() || $record->user_id === $user->id;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return static::canView($record);
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return static::canView($record);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->where('matching_mode', 'crossed_system_columns');

        $user = Auth::user();

        if ($user && ! $user->isSuperAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampuzanoReconciliations::route('/'),
            'create' => CreateCampuzanoReconciliation::route('/create'),
            'view' => ViewCampuzanoReconciliation::route('/{record}'),
            'edit' => EditCampuzanoReconciliation::route('/{record}/edit'),
        ];
    }

    public static function processRecord(Reconciliation $record): Reconciliation
    {
        try {
            $record = app(ReconciliationProcessorService::class)->process($record);

            Notification::make()
                ->title(__('resources.reconciliations.notifications.process_success'))
                ->success()
                ->send();

            return $record;
        } catch (\Throwable $exception) {
            Notification::make()
                ->title(__('resources.reconciliations.notifications.process_error'))
                ->body($exception->getMessage())
                ->danger()
                ->send();

            return $record->refresh();
        }
    }
}
