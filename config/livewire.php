<?php

return [
    'temporary_file_upload' => [
        'disk' => 'local', // Changed from livewire-tmp
        'rules' => null,
        'directory' => 'livewire-tmp',
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp3', 'mp4',
            'mov', 'avi', 'wmv', 'pdf', 'jpg', 'jpeg', 'webm', 'ogg', 'm4a', 'wav',
        ],
        'max_upload_time' => 5,
    ],
];