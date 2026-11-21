<?php

return [
    'shipment_statuses' => [
        'CREATED',
        'BOOKED',
        'PICKED_UP',
        'IN_TRANSIT',
        'DELIVERED',
        'CANCELLED',
    ],

    'terminal_statuses' => [
        'DELIVERED',
        'CANCELLED',
    ],

    'valid_transitions' => [
        'CREATED' => ['BOOKED', 'CANCELLED'],
        'BOOKED' => ['PICKED_UP', 'CANCELLED'],
        'PICKED_UP' => ['IN_TRANSIT', 'CANCELLED'],
        'IN_TRANSIT' => ['DELIVERED', 'CANCELLED'],
        'DELIVERED' => [],
        'CANCELLED' => [],
    ],

    'delay_thresholds' => [
        'low' => 6.0,
        'medium' => 24.0,
    ],

    'exception_types' => [
        'CUSTOMS_HOLD',
        'WEATHER_DELAY',
        'PORT_CONGESTION',
        'DAMAGED',
        'MISROUTED',
        'DOCUMENTATION_ISSUE',
    ],

    'severities' => ['LOW', 'MEDIUM', 'HIGH'],

    'exception_statuses' => ['OPEN', 'INVESTIGATING', 'RESOLVED'],

    'geocoding_api_url' => env('GEOCODING_API_URL', 'https://nominatim.openstreetmap.org'),
    'weather_api_url' => env('WEATHER_API_URL', 'https://api.open-meteo.com/v1'),
];
