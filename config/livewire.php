<?php

return [
    'temporary_file_upload' => [
        'disk' => 'livewire-tmp',
        'rules' => null,
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp3', 
            'mp4', 'mov', 'avi', 'wmv', 'webm', 'ogg',
            'jpg', 'jpeg', 'pdf', 'm4a',
        ],
        'max_upload_time' => 120, 
    ],
];