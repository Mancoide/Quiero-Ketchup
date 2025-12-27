<?php

namespace App\Filament\Resources\Authentication;

use App\Filament\Resources\Authentication\RoleResource\Pages;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string | BackedEnum | null  $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('menu.groups.authentication');
    }

    public static function getModelLabel(): string
    {
        return __('resources.roles.singular');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.roles.plural');
    }


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Sección: Información del Rol
                Section::make('Información del Rol')
                    ->description('Define el nombre y alcance del rol')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Rol')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Ej: admin, moderador, editor')
                            ->helperText('Usa letras minúsculas, sin espacios ni caracteres especiales')
                            ->autocomplete('off')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('name', Str::slug($state, '_'));
                            })
                            ->prefixIcon('heroicon-o-tag')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('guard_name')
                            ->label('Guard')
                            ->default('web')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Sistema de autenticación (no modificable)')
                            ->prefixIcon('heroicon-o-shield-exclamation')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                // Sección: Permisos Asignados
                Section::make('Permisos del Rol')
                    ->description('Selecciona los permisos que tendrá este rol')
                    ->icon('heroicon-o-key')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->label('')
                            ->relationship('permissions', 'name')
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(3)
                            ->gridDirection('row')
                            ->options(function () {
                                return \Spatie\Permission\Models\Permission::all()
                                    ->groupBy(function ($permission) {
                                        // Agrupar por recurso (view_user, create_user -> user)
                                        $parts = explode('_', $permission->name);
                                        return count($parts) > 1 ? implode('_', array_slice($parts, 1)) : $permission->name;
                                    })
                                    ->map(function ($group, $resource) {
                                        return $group->pluck('name', 'id');
                                    })
                                    ->flatten()
                                    ->toArray();
                            })
                            ->descriptions(function () {
                                return \Spatie\Permission\Models\Permission::all()
                                    ->mapWithKeys(function ($permission) {
                                        // Generar descripción amigable
                                        $action = Str::before($permission->name, '_');
                                        $resource = Str::after($permission->name, '_');

                                        $actionLabels = [
                                            'view' => 'Ver',
                                            'view_any' => 'Listar',
                                            'create' => 'Crear',
                                            'update' => 'Editar',
                                            'delete' => 'Eliminar',
                                            'delete_any' => 'Eliminar Múltiples',
                                            'force_delete' => 'Eliminar Permanente',
                                            'force_delete_any' => 'Eliminar Permanente Múltiples',
                                            'restore' => 'Restaurar',
                                            'restore_any' => 'Restaurar Múltiples',
                                            'replicate' => 'Duplicar',
                                        ];

                                        $label = $actionLabels[$action] ?? ucfirst($action);
                                        $resourceLabel = Str::headline($resource);

                                        return [$permission->id => "{$label} {$resourceLabel}"];
                                    })
                                    ->toArray();
                            })
                            ->helperText('Selecciona todos los permisos necesarios para este rol')
                            ->columnSpanFull(),
                    ])
                    ->collapsed()
                    ->collapsible(),

                // Sección: Información del Sistema
                Section::make('Información del Sistema')
                    ->description('Datos de auditoría')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        TextEntry::make('users_count')
                            ->placeholder('Usuarios con este Rol')
                            // ->content(fn (?Role $record): string => $record?->users()->count() ?? 0)
                            ->visible(fn (string $context): bool => $context === 'edit'),

                        TextEntry::make('permissions_count')
                            ->placeholder('Permisos Asignados')
                            // ->content(fn (?Role $record): string => $record?->permissions()->count() ?? 0)
                            ->visible(fn (string $context): bool => $context === 'edit'),

                        TextEntry::make('created_at')
                            ->placeholder('Fecha de Creación')
                            ->visible(fn (string $context): bool => $context === 'edit'),

                        TextEntry::make('updated_at')
                            ->placeholder('Última Actualización')
                            ->visible(fn (string $context): bool => $context === 'edit'),
                    ])
                    ->columns(4)
                    ->collapsed()
                    ->collapsible()
                    ->visible(fn (string $context): bool => $context === 'edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Ícono visual
                Tables\Columns\IconColumn::make('icon')
                    ->label('')
                    ->icon('heroicon-o-shield-check')
                    ->size('lg')
                    ->color(fn (Role $record): string => match($record->name) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'manager' => 'info',
                        default => 'primary',
                    })
                    ->toggleable(isToggledHiddenByDefault: false),

                // Nombre del rol
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre del Rol')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nombre copiado')
                    ->formatStateUsing(fn (string $state): string => Str::headline($state))
                    ->badge()
                    ->color(fn (Role $record): string => match($record->name) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'manager' => 'info',
                        default => 'primary',
                    }),

                // Guard name
                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-shield-exclamation')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Cantidad de usuarios
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Usuarios')
                    ->counts('users')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match(true) {
                        $state === 0 => 'gray',
                        $state < 5 => 'success',
                        $state < 20 => 'warning',
                        default => 'danger',
                    })
                    ->icon('heroicon-o-users')
                    ->toggleable(),

                // Cantidad de permisos
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('Permisos')
                    ->counts('permissions')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-key')
                    ->toggleable(),

                // Fecha de creación
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->description(fn (Role $record): string => $record->created_at->format('d/m/Y'))
                    ->toggleable(isToggledHiddenByDefault: true),

                // Última actualización
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtro por guard
                Tables\Filters\SelectFilter::make('guard_name')
                    ->label('Guard')
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ])
                    ->native(false),

                // Filtro por cantidad de usuarios
                Tables\Filters\Filter::make('has_users')
                    ->label('Con Usuarios Asignados')
                    ->query(fn (Builder $query): Builder => $query->has('users'))
                    ->toggle(),

                // Filtro por cantidad de permisos
                Tables\Filters\Filter::make('has_permissions')
                    ->label('Con Permisos Asignados')
                    ->query(fn (Builder $query): Builder => $query->has('permissions'))
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->color('info'),

                EditAction::make()
                    ->color('warning'),

                DeleteAction::make()
                    ->modalHeading('Eliminar Rol')
                    ->modalDescription('¿Estás seguro? Los usuarios con este rol perderán sus permisos.')
                    ->successNotificationTitle('Rol eliminado correctamente')
                    ->before(function (Role $record) {
                        // Prevenir eliminar super_admin
                        if ($record->name === 'super_admin') {
                            Notification::make()
                                ->danger()
                                ->title('Acción no permitida')
                                ->body('No se puede eliminar el rol super_admin')
                                ->persistent()
                                ->send();

                            return false;
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->modalHeading('Eliminar Roles')
                        ->modalDescription('¿Eliminar los roles seleccionados?')
                        ->successNotificationTitle('Roles eliminados')
                        ->before(function ($records) {
                            // Prevenir eliminar super_admin
                            if ($records->contains('name', 'super_admin')) {
                                Notification::make()
                                    ->danger()
                                    ->title('Acción no permitida')
                                    ->body('No se puede eliminar el rol super_admin')
                                    ->persistent()
                                    ->send();

                                return false;
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s')
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistSortInSession()
            ->striped()
            ->paginated([10, 25, 50]);
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'guard_name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Usuarios' => $record->users()->count(),
            'Permisos' => $record->permissions()->count(),
        ];
    }
}
