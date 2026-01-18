@php
    $isOrdersIndex = request()->routeIs('filament.admin.resources.orders.index');
@endphp

@if ($isOrdersIndex)
    <div
        data-orders-realtime-root
        data-orders-realtime-debug="{{ app()->isLocal() ? 1 : 0 }}"
        data-orders-realtime-view-label="{{ __('resources.orders.realtime.view') }}"
        data-orders-realtime-edit-label="{{ __('filament-actions::edit.single.label') }}"
        class="fi-ta"
    >
        <div class="fi-ta-header flex flex-wrap gap-2 items-center justify-between">
            <div class="flex flex-wrap gap-2 items-center">
                <div class="text-sm font-medium">
                    {{ __('resources.orders.realtime.new_orders') }}
                </div>
                <div class="text-xs text-gray-500">
                    {{ __('resources.orders.realtime.helper') }}
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/filament/orders-realtime.js')
@endif
