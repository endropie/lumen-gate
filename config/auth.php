<?php

return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'custom',
        ],
    ],

    'providers' => [
        'custom' => [
            'driver' => 'auth-provider'
        ]
    ]
];
