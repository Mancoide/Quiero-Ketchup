<?php

namespace App\Events;

use App\Models\Order;
use App\Support\Orders\OrdersRealtimeStats;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('orders'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'orders.created';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'order' => OrdersRealtimeStats::orderSummary($this->order),
            'stats' => OrdersRealtimeStats::counts(),
        ];
    }
}
