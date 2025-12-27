<?php

namespace App\Filament\Resources\Authentication;

use App\Enums\UserStatus;
use App\Filament\Resources\Authentication\UserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'users';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('menu.groups.authentication');
    }

    public static function getModelLabel(): string
    {
        return __('resources.users.singular');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.users.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', UserStatus::ACTIVE)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Sección: Avatar
                Section::make('Foto de Perfil')
                    ->description('Sube una imagen cuadrada para el avatar del usuario')
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('avatar')
                            ->collection('avatar')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1:1'])
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('600')
                            ->imageResizeTargetHeight('600')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->disk('public')
                            ->visibility('public')
                            ->downloadable()
                            ->openable()
                            ->deletable()
                            ->hint('Formatos: JPG, PNG, WEBP. Máximo 2MB.')
                            ->helperText('La imagen se ajustará automáticamente a formato cuadrado.')
                            ->panelLayout('integrated')
                            ->imageEditorEmptyFillColor('#000000')
                            ->responsiveImages()
                            ->columnSpanFull()
                            ->hiddenLabel(),
                    ])
                    ->collapsed(false)
                    ->compact(),

                // Sección: Información Personal
                Section::make('Información Personal')
                    ->description('Datos básicos del usuario')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Juan Pérez')
                            ->autocomplete('name')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('usuario@ejemplo.com')
                            ->autocomplete('email')
                            ->prefixIcon('heroicon-o-envelope')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->minLength(8)
                            ->maxLength(255)
                            ->placeholder('Mínimo 8 caracteres')
                            ->autocomplete('new-password')
                            ->revealable()
                            ->helperText('Dejar en blanco para mantener la contraseña actual')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // Sección: Estado de la Cuenta
                Section::make('Estado de la Cuenta')
                    ->description('Control de acceso del usuario')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                UserStatus::ACTIVE->value => 'Activo',
                                UserStatus::INACTIVE->value => 'Inactivo',
                                UserStatus::SUSPENDED->value => 'Suspendido',
                            ])
                            ->default(UserStatus::ACTIVE->value)
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-o-chevron-down')
                            ->helperText(fn ($state) => match($state) {
                                UserStatus::ACTIVE->value => '✓ El usuario puede acceder al sistema',
                                UserStatus::INACTIVE->value => '⚠ El usuario no puede iniciar sesión',
                                UserStatus::SUSPENDED->value => '✗ Acceso bloqueado temporalmente',
                                default => 'Selecciona el estado del usuario',
                            }),

                        TextEntry::make('created_at')
                            ->placeholder('Fecha de Registro')
                            // ->content(fn (?User $record): ?string => $record?->created_at?->diffForHumans())
                            ->visible(fn (string $context): bool => $context === 'edit'),

                        TextEntry::make('updated_at')
                            ->placeholder('Última Actualización')
                            // ->content(fn (?User $record): ?string => $record?->updated_at?->diffForHumans())
                            ->visible(fn (string $context): bool => $context === 'edit'),
                    ])
                    ->columns(3),

                // Sección: Roles y Permisos (Shield)
                Section::make('Roles y Permisos')
                    ->description('Asigna roles para controlar el acceso del usuario')
                    ->icon('heroicon-o-key')
                    ->schema([
                        Forms\Components\CheckboxList::make('roles')
                            ->relationship('roles', 'name')
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2)
                            ->gridDirection('row')
                            ->required()
                            ->helperText('Selecciona al menos un rol para el usuario')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->label('')
                        ->circular()
                        ->grow(false)
                        ->defaultImageUrl(asset('images/default-avatar.png')),
                    Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->label('Nombre')
                            ->searchable()
                            ->sortable()
                            ->weight('bold')
                            ->color('primary')
                            ->icon('heroicon-o-user'),
                        Tables\Columns\TextColumn::make('email')
                            ->label('Email')
                            ->searchable()
                            ->color('gray')
                            ->icon('heroicon-o-envelope')
                            ->size('sm'),
                    ]),
                    // Roles (badges múltiples)
                    Tables\Columns\TextColumn::make('roles.name')
                        ->label('Roles')
                        ->badge()
                        ->separator(',')
                        ->colors([
                            'primary' => 'super_admin',
                            'danger' => 'admin',
                            'warning' => 'manager',
                            'gray' => 'user',
                        ])
                        ->icon('heroicon-o-shield-check')
                        ->searchable()
                        ->toggleable(),
                    // Estado (badge con toggle)
                    Tables\Columns\TextColumn::make('status')
                        ->label('Estado')
                        ->badge()
                        ->formatStateUsing(fn (UserStatus $state): string => $state->label())
                        ->color(fn (UserStatus $state): string => $state->color())
                        ->icon(fn (UserStatus $state): string => $state->icon())
                        ->sortable()
                        ->toggleable(),
                    // Fecha de creación
                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Registrado')
                        ->dateTime('d/m/Y H:i')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    // Última actualización
                    Tables\Columns\TextColumn::make('updated_at')
                        ->label('Actualizado')
                        ->dateTime('d/m/Y H:i')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),

            ])
            ->filters([
                // Filtro por estado
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        UserStatus::ACTIVE->value => 'Activo',
                        UserStatus::INACTIVE->value => 'Inactivo',
                        UserStatus::SUSPENDED->value => 'Suspendido',
                    ])
                    ->native(false)
                    ->multiple()
                    ->indicator('Estado'),

                // Filtro por rol
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Rol')
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->indicator('Rol'),

                // Filtro por fecha de registro
                Tables\Filters\Filter::make('created_at')
                    ->label('Fecha de Registro')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde')
                            ->placeholder('Fecha inicial')
                            ->native(false),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta')
                            ->placeholder('Fecha final')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'Desde ' . \Carbon\Carbon::parse($data['created_from'])->format('d/m/Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Hasta ' . \Carbon\Carbon::parse($data['created_until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->color('info'),

                EditAction::make()
                    ->color('warning'),

                // Acción personalizada: Cambiar estado
                Action::make('toggle_status')
                    ->label(fn (User $record): string => match($record->status) {
                        UserStatus::ACTIVE => 'Desactivar',
                        UserStatus::INACTIVE => 'Activar',
                        UserStatus::SUSPENDED => 'Activar',
                    })
                    ->icon(fn (User $record): string => match($record->status) {
                        UserStatus::ACTIVE => 'heroicon-o-x-circle',
                        UserStatus::INACTIVE => 'heroicon-o-check-circle',
                        UserStatus::SUSPENDED => 'heroicon-o-check-circle',
                    })
                    ->color(fn (User $record): string => match($record->status) {
                        UserStatus::ACTIVE => 'danger',
                        UserStatus::INACTIVE => 'success',
                        UserStatus::SUSPENDED => 'success',
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record): string =>
                        'Cambiar estado de ' . $record->name
                    )
                    ->modalDescription(fn (User $record): string => match($record->status) {
                        UserStatus::ACTIVE => '¿Desactivar este usuario? No podrá acceder al sistema.',
                        UserStatus::INACTIVE => '¿Activar este usuario? Podrá acceder nuevamente.',
                        UserStatus::SUSPENDED => '¿Activar este usuario suspendido?',
                    })
                    ->action(function (User $record): void {
                        $newStatus = $record->status === UserStatus::ACTIVE
                            ? UserStatus::INACTIVE
                            : UserStatus::ACTIVE;

                        $record->update(['status' => $newStatus]);

                        Notification::make()
                            ->success()
                            ->title('Estado actualizado')
                            ->body("Usuario {$newStatus->label()}")
                            ->send();
                    })
                    ->hidden(fn (User $record): bool => $record->id === auth()->id()),

                DeleteAction::make()
                    ->hidden(fn (User $record): bool => $record->id === auth()->id())
                    ->modalHeading('Eliminar Usuario')
                    ->modalDescription('¿Estás seguro? Esta acción no se puede deshacer.')
                    ->successNotificationTitle('Usuario eliminado correctamente'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Activar usuarios seleccionados
                    BulkAction::make('activate')
                        ->label('Activar Seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => UserStatus::ACTIVE]);

                            Notification::make()
                                ->success()
                                ->title('Usuarios activados')
                                ->body("{$records->count()} usuarios activados correctamente")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Desactivar usuarios seleccionados
                    BulkAction::make('deactivate')
                        ->label('Desactivar Seleccionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => UserStatus::INACTIVE]);

                            Notification::make()
                                ->success()
                                ->title('Usuarios desactivados')
                                ->body("{$records->count()} usuarios desactivados correctamente")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make()
                        ->modalHeading('Eliminar Usuarios')
                        ->modalDescription('¿Eliminar los usuarios seleccionados?')
                        ->successNotificationTitle('Usuarios eliminados'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistSortInSession()
            ->searchable()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Email' => $record->email,
            'Estado' => $record->status->label(),
        ];
    }
}
