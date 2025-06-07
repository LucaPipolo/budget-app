<?php

declare(strict_types=1);

return [

    // Labels
    'labels' => [
        'logo' => 'Logo',
        'name' => 'Name',
        'type' => 'Type',
        'origin' => 'Origin',
        'balance' => 'Balance',
        'currency' => 'Currency',
        'iban' => 'IBAN',
        'swift' => 'SWIFT/BIC',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ],

    // Enums
    'enums' => [
        'type' => [
            'bank' => 'Bank',
            'cash' => 'Cash',
            'investments' => 'Investments',
        ],
        'origin' => [
            'web' => 'Web',
            'api' => 'API',
            'external' => 'External',
        ],
    ],

];
