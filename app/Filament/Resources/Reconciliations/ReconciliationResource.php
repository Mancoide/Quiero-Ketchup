<?php

namespace App\Filament\Resources\Reconciliations;

use App\Filament\Resources\Reconciliations\Pages\CreateReconciliation;
use App\Filament\Resources\Reconciliations\Pages\EditReconciliation;
use App\Filament\Resources\Reconciliations\Pages\ListReconciliations;
use App\Filament\Resources\Reconciliations\Pages\ViewReconciliation;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ReconciliationResource extends Resource
{
    protected static ?string $model = Reconciliation::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('menu.groups.accounting');
    }

    public static function getModelLabel(): string
    {
        return __('resources.reconciliations.singular');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.reconciliations.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return ReconciliationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReconciliationsTable::configure($table);
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->canAccessStandardReconciliations() ?? false;
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

        if (! $user?->canAccessStandardReconciliations()) {
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
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('matching_mode')
                    ->orWhere('matching_mode', 'standard');
            });

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
            'index' => ListReconciliations::route('/'),
            'create' => CreateReconciliation::route('/create'),
            'view' => ViewReconciliation::route('/{record}'),
            'edit' => EditReconciliation::route('/{record}/edit'),
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
