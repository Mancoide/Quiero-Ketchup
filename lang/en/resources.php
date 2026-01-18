<?php

return [
    'key_value' => [
        'key' => 'Key',
        'value' => 'Value',
        'add' => 'Add',
    ],

    'orders' => [
        'singular' => 'Order',
        'plural' => 'Orders',
        'sections' => [
            'general' => 'Information',
            'timestamps' => 'Dates',
        ],
        'fields' => [
            'id' => '#',
            'user' => 'Customer',
            'restaurant' => 'Restaurant',
            'fulfillment_type' => 'Fulfillment type',
            'status' => 'Status',
            'total_amount' => 'Total',
            'currency' => 'Currency',
            'metadata' => 'Meta',
            'created_at' => 'Created',
            'updated_at' => 'Updated',
        ],
        'fulfillment_types' => [
            'delivery' => 'Delivery',
            'pickup' => 'Pickup',
            'dine_in' => 'Dine-in',
        ],
        'statuses' => [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'preparing' => 'Preparing',
            'ready' => 'Ready',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ],
        'realtime' => [
            'new_orders' => 'New orders',
            'helper' => 'Inserted in real time without reloading.',
            'clear' => 'Clear',
            'view' => 'View',
        ],
        'helpers' => [
            'metadata' => 'Optional extra data in key/value format.',
        ],
        'placeholders' => [
            'metadata_key' => 'e.g.: delivery_type',
            'metadata_value' => 'e.g.: delivery',
        ],
    ],
];
