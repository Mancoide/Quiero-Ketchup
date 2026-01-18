<?php

namespace App\Support\Orders;

use App\Enums\OrderFulfillmentType;
use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;

class OrdersRealtimeStats
{
    /**
     * @return array{pending:int,delivery:int,pickup:int,dine_in:int}
     */
    public static function counts(): array
    {
        return [
            'pending' => Order::query()->where('status', OrderStatus::PENDING->value)->count(),
            'delivery' => Order::query()->where('fulfillment_type', OrderFulfillmentType::DELIVERY->value)->count(),
            'pickup' => Order::query()->where('fulfillment_type', OrderFulfillmentType::PICKUP->value)->count(),
            'dine_in' => Order::query()->where('fulfillment_type', OrderFulfillmentType::DINE_IN->value)->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function orderSummary(Order $order): array
    {
        $order->loadMissing(['user', 'restaurant']);

        $status = OrderStatus::tryFromMixed($order->status);
        $fulfillment = OrderFulfillmentType::tryFromMixed($order->fulfillment_type);

        $statusValue = $status?->value
            ?? (string) $order->getRawOriginal('status');

        $fulfillmentValue = $fulfillment?->value
            ?? (string) $order->getRawOriginal('fulfillment_type');

        $currency = (string) $order->currency;
        $decimals = strtoupper($currency) === 'PYG' ? 0 : 2;
        $totalLabel = number_format((float) $order->total_amount, $decimals, ',', '.');

        $createdAtLabel = $order->created_at?->locale(app()->getLocale())->translatedFormat('M j, Y H:i:s');

        return [
            'id' => $order->id,
            'user' => (string) ($order->user?->name ?? '-'),
            'restaurant' => (string) ($order->restaurant?->name ?? '-'),
            'status' => $statusValue,
            'status_label' => $status?->label() ?? $statusValue,
            'status_color' => $status?->color() ?? 'gray',
            'fulfillment_type' => $fulfillmentValue,
            'fulfillment_label' => $fulfillment?->label() ?? $fulfillmentValue,
            'fulfillment_color' => $fulfillment?->color() ?? 'gray',
            'total_amount' => (string) $order->total_amount,
            'total_label' => $totalLabel,
            'currency' => (string) $order->currency,
            'created_at' => $order->created_at?->format('Y-m-d H:i') ?? null,
            'created_at_label' => $createdAtLabel,
            'view_url' => OrderResource::getUrl('view', ['record' => $order]),
            'edit_url' => OrderResource::getUrl('edit', ['record' => $order]),
        ];
    }
}
