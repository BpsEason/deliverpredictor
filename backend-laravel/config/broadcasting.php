<?php

return [
    'default' => env('BROADCAST_DRIVER', 'null'),
    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'host' => 'api-'.env('PUSHER_APP_CLUSTER', 'mt1').'.pusher.com',
                'port' => 443,
                'scheme' => 'https',
                'encrypted' => true,
                'useTLS' => true,
            ],
            'client_options' => [],
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],
        'log' => [
            'driver' => 'log',
        ],
        'null' => [
            'driver' => 'null',
        ],
    ],
];
