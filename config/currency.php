<?php return [
    'default' => env('CURRENCY', 'DKK'),
    'supported' => env('CURRENCY_SUPPORTED', ['DKK', 'SEK', 'NOK', 'USD', 'EUR']),
    'pairs' => env('CURRENCY_PAIRS', [
        'DKK/SEK',
        'DKK/NOK',
        'DKK/USD',
        'DKK/EUR',

        'SEK/DKK',
        'SEK/NOK',
        'SEK/USD',
        'SEK/EUR',

        'NOK/DKK',
        'NOK/SEK',
        'NOK/USD',
        'NOK/EUR',

        'USD/DKK',
        'USD/SEK',
        'USD/NOK',
        'USD/EUR',

        'EUR/DKK',
        'EUR/SEK',
        'EUR/NOK',
        'EUR/USD'
    ])
];
