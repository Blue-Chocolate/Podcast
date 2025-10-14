<?php

return [
    'temporary_file_upload' => [
        'disk' => 'public', // بدل ما تكون في local
        'rules' => null,
        'directory' => 'livewire-tmp',
        'middleware' => null,
        'preview_mimes' => [
            'image/jpeg', 'image/png', 'video/mp4',
        ],
        'max_upload_time' => 5, // minutes
    ],
];
