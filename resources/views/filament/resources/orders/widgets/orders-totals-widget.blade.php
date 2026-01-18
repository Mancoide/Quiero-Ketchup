@php
    $pending = (int) ($stats['pending'] ?? 0);
    $delivery = (int) ($stats['delivery'] ?? 0);
    $pickup = (int) ($stats['pickup'] ?? 0);
    $dineIn = (int) ($stats['dine_in'] ?? 0);
@endphp

<div class="flex flex-wrap gap-2">
    <div class="fi-card fi-card-content px-3 py-2 min-w-[9rem]">
        <div class="text-[11px] text-gray-500">{{ __('resources.orders.statuses.pending') }}</div>
        <div class="text-lg font-semibold" data-orders-stats-pending>{{ $pending }}</div>
    </div>

    <div class="fi-card fi-card-content px-3 py-2 min-w-[9rem]">
        <div class="text-[11px] text-gray-500">{{ __('resources.orders.fulfillment_types.pickup') }}</div>
        <div class="text-lg font-semibold" data-orders-stats-pickup>{{ $pickup }}</div>
    </div>

    <div class="fi-card fi-card-content px-3 py-2 min-w-[9rem]">
        <div class="text-[11px] text-gray-500">{{ __('resources.orders.fulfillment_types.dine_in') }}</div>
        <div class="text-lg font-semibold" data-orders-stats-dine-in>{{ $dineIn }}</div>
    </div>

    <div class="fi-card fi-card-content px-3 py-2 min-w-[9rem]">
        <div class="text-[11px] text-gray-500">{{ __('resources.orders.fulfillment_types.delivery') }}</div>
        <div class="text-lg font-semibold" data-orders-stats-delivery>{{ $delivery }}</div>
    </div>
</div>
