<?php

namespace App\Filament\Resources\Orders\Widgets;

use App\Support\Orders\OrdersRealtimeStats;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrdersTotalsWidget extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 'full';

    protected int | array | null $columns = 4;

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $stats = OrdersRealtimeStats::counts();

        return [
            Stat::make(__('resources.orders.statuses.pending'), (int) ($stats['pending'] ?? 0))
                ->color('warning')
                ->extraAttributes(['data-orders-stats' => 'pending']),

            Stat::make(__('resources.orders.fulfillment_types.pickup'), (int) ($stats['pickup'] ?? 0))
                ->color('info')
                ->extraAttributes(['data-orders-stats' => 'pickup']),

            Stat::make(__('resources.orders.fulfillment_types.dine_in'), (int) ($stats['dine_in'] ?? 0))
                ->color('gray')
                ->extraAttributes(['data-orders-stats' => 'dine_in']),

            Stat::make(__('resources.orders.fulfillment_types.delivery'), (int) ($stats['delivery'] ?? 0))
                ->color('primary')
                ->extraAttributes(['data-orders-stats' => 'delivery']),
        ];
    }
}
