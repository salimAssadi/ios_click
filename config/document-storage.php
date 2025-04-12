<?php

return [
    'base_path' => storage_path('app/documents'),
    'structure' => [
        'procedures' => [
            'path' => 'procedures',
            'allowed_extensions' => ['pdf', 'doc', 'docx'],
        ],
        'samples' => [
            'path' => 'samples',
            'allowed_extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
        ],
        'templates' => [
            'path' => 'templates',
            'allowed_extensions' => ['pdf', 'doc', 'docx'],
        ],
    ],
    'versions_path' => 'versions',
];
