<?php

return [
    'name'        => 'Labor',
    'description' => 'Katalog der Laboranforderungen/-analyten (frei pflegbar).',
    'version'     => '1.0.0',

    'routing' => [
        'prefix'     => 'laboratory',
        'middleware' => ['web', 'auth'],
    ],

    'guard' => 'web',

    'navigation' => [
        'route' => 'laboratory.index',
        'icon'  => 'heroicon-o-beaker',
        'order' => 48,
    ],

    'sidebar' => [
        [
            'group' => 'Allgemein',
            'items' => [
                ['label' => 'Labor', 'route' => 'laboratory.index', 'icon' => 'heroicon-o-beaker'],
            ],
        ],
    ],
];
