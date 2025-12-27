<?php

namespace App\Filament\Widgets;

use App\Enums\UserStatus;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class UsersStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    // protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        return [
            // Total de Usuarios con gráfico de 7 días
            Stat::make('Total Usuarios', User::count())
                ->description('Usuarios registrados')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary')
                ->chart($this->getUsersChartData())
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            // Usuarios Activos con porcentaje
            Stat::make('Usuarios Activos', User::active()->count())
                ->description($this->getActivePercentage() . ' del total')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            // Usuarios Inactivos
            Stat::make('Usuarios Inactivos', User::inactive()->count())
                ->description('Sin acceso al sistema')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('gray')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            // Usuarios Suspendidos con alerta
            Stat::make('Usuarios Suspendidos', User::suspended()->count())
                ->description($this->getSuspendedMessage())
                ->descriptionIcon($this->getSuspendedIcon())
                ->color($this->getSuspendedColor())
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),
        ];
    }

    /**
     * Genera datos del gráfico de usuarios de los últimos 7 días
     */
    protected function getUsersChartData(): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $data[] = User::whereDate('created_at', '<=', $date)->count();
        }

        return $data;
    }

    /**
     * Calcula el porcentaje de usuarios activos
     */
    protected function getActivePercentage(): string
    {
        $total = User::count();
        $active = User::active()->count();

        if ($total === 0) {
            return '0%';
        }

        $percentage = round(($active / $total) * 100, 1);

        return $percentage . '%';
    }

    /**
     * Mensaje para usuarios suspendidos
     */
    protected function getSuspendedMessage(): string
    {
        $count = User::suspended()->count();

        if ($count === 0) {
            return 'Sin usuarios suspendidos';
        }

        if ($count === 1) {
            return 'Requiere atención';
        }

        return 'Múltiples suspensiones';
    }

    /**
     * Ícono dinámico para usuarios suspendidos
     */
    protected function getSuspendedIcon(): string
    {
        $count = User::suspended()->count();

        return $count > 0
            ? 'heroicon-o-exclamation-triangle'
            : 'heroicon-o-shield-check';
    }

    /**
     * Color dinámico para usuarios suspendidos
     */
    protected function getSuspendedColor(): string
    {
        $count = User::suspended()->count();

        if ($count === 0) {
            return 'success';
        }

        if ($count >= 5) {
            return 'danger';
        }

        return 'warning';
    }
}
