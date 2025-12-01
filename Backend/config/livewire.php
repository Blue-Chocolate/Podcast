<?php

return [
    'temporary_file_upload' => [
    'disk' => 'livewire-tmp',
    'rules' => 'max:1048576', // الحجم بالـ KB، هنا 10240KB = 10MB
    'preview_mimes' => [
        'png', 'gif', 'bmp', 'svg', 'wav', 'mp3', 
        'mp4', 'mov', 'avi', 'wmv', 'webm', 'ogg',
        'jpg', 'jpeg', 'pdf', 'm4a',
    ],
    'max_upload_time' => 120, 
],

];