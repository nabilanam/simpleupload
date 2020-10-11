<?php

return [
    /** Default diskName */
    'default_disk'          => 'simpleupload',

    /** Root upload directory */
    'root_directory'        => 'uploads',

    /** SimpleUpload will not delete file if the file path starts with protected_directory name */
    'protected_directory'   => 'defaults/',

    /** simpleupload disk config */
    'disk_config' => [
        'url'           => env('APP_URL'),
        'root'          => public_path(),
        'driver'        => 'local',
        'visibility'	=> 'public',
    ]
];
