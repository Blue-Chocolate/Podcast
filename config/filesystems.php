<?php

return [
    'default' => env('FILESYSTEM_DISK', 'public'),

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'throw' => false,
        ],

        'public' => [
    'driver' => 'local',
    'root' => public_path('storage'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],

'public_files' => [
    'driver' => 'local',
    'root' => public_path('files'),
    'url' => env('APP_URL').'/files',
    'visibility' => 'public',
],


        'livewire-tmp' => [
            'driver' => 'local',
            'root' => storage_path('app/livewire-tmp'),
            'visibility' => 'public',
            'throw' => false,
        ],
    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];