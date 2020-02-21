<?php

return [
    /** Change this if you dont want to set diskName everytime */
    'default_disk'      => 'simpleupload',

    /**
     * SimpleUpload will not delete a file
     * when you call deleteIfExists and the path starts with default_directory
     */
    'default_directory' => 'uploads/default/',

    /** SimpleUpload disk config */
    'disk_config' => [
        'driver' => 'local',
        'root' => public_path(),
        'url' => env('APP_URL') . '/uploads',
        'visibility' => 'public',
    ]
];
